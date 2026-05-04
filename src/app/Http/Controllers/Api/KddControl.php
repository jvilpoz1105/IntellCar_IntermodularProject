<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventKdd;
use App\Http\Resources\Api\KddEventResource;
use App\Http\Resources\Api\KddEventSummaryResource;
use Illuminate\Http\Request;

class KddControl extends Controller
{
    /**
     * Obtener listado general (solo datos más importantes).
     */
    public function index()
    {
        // En la vista general mostramos creador y paddock, sin cargar los asistentes completos
        $events = EventKdd::with(['creator:user_id,username,profile_picture', 'paddock'])
            ->where('event_date', '>=', now())
            ->where('visible', true)
            ->whereNull('onDeleteRequest')
            ->orderBy('event_date', 'asc')
            ->paginate(20);

        return KddEventSummaryResource::collection($events);
    }

    /**
     * Obtener por ID (todos los datos y relaciones).
     */
    public function show($id)
    {
        // Aquí traemos también todos los asistentes para la vista de detalle
        $event = EventKdd::with(['creator', 'paddock', 'attendees'])->findOrFail($id);
        
        return new KddEventResource($event);
    }

    /**
     * Actualizar evento (PUT/PATCH - actualización parcial permitida).
     */
    public function update(Request $request, $id)
    {
        $event = EventKdd::findOrFail($id);

        // Verificar permisos (creador o admin)
        if ($event->creator_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este evento'], 403);
        }

        // 'sometimes' permite la actualización de solo algunos campos
        $validated = $request->validate([
            'title' => 'sometimes|string|max:150',
            'event_description' => 'sometimes|string',
            'event_date' => 'sometimes|date|after:now',
            'location_name' => 'sometimes|string|max:255|nullable',
            'address' => 'sometimes|string|max:255|nullable',
            'city' => 'sometimes|string|max:100|nullable',
            'latitude' => 'sometimes|numeric|between:-90,90|nullable',
            'longitude' => 'sometimes|numeric|between:-180,180|nullable',
            'max_participants' => 'sometimes|integer|min:0|nullable',
            'paddock_id' => 'sometimes|exists:paddock,paddock_id|nullable',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'event' => $event->fresh(['creator', 'paddock']),
        ]);
    }

    /**
     * Solicitar eliminación de un evento/quedada (Soft Delete).
     */
    public function softDelete(Request $request, $id)
    {
        $event = EventKdd::findOrFail($id);

        // Verificar permisos (creador o admin)
        if ($event->creator_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para solicitar la eliminación de este evento'], 403);
        }

        $event->update(['onDeleteRequest' => now()]);

        return response()->json([
            'message' => 'Eliminación del evento solicitada exitosamente',
        ]);
    }

    /**
     * Eliminar un evento/quedada por ID.
     */
    public function destroy(Request $request, $id)
    {
        $event = EventKdd::findOrFail($id);

        if ($request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden eliminar este evento definitivamente'], 403);
        }

        // 1. Desvincular todos los asistentes de la tabla relacional
        $event->attendees()->detach();

        // 2. Finalmente borrar el evento
        $event->delete();

        return response()->json([
            'message' => 'Evento y sus asistentes desvinculados eliminados exitosamente'
        ]);
    }
}
