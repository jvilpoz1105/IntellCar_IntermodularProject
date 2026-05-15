<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventKdd;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Obtener listado de eventos/quedadas",
     *     tags={"Eventos"},
     *     @OA\Response(response=200, description="Listado de eventos")
     * )
     */
    public function index()
    {
        $events = EventKdd::with(['creator', 'paddock', 'attendees'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->paginate(20);

        return response()->json($events);
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Crear un nuevo evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=201, description="Evento creado")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'event_description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_participants' => 'nullable|integer|min:0',
            'paddock_id' => 'nullable|exists:paddock,paddock_id',
        ]);

        $event = EventKdd::create([
            ...$validated,
            'creator_id' => $request->user()->user_id,
        ]);

        return response()->json([
            'message' => 'Evento creado exitosamente',
            'event' => $event->load(['creator', 'paddock']),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Obtener detalles de un evento",
     *     tags={"Eventos"},
     *     @OA\Response(response=200, description="Detalles del evento")
     * )
     */
    public function show($id)
    {
        $event = EventKdd::with(['creator', 'paddock', 'attendees'])->findOrFail($id);
        
        return response()->json($event);
    }

    /**
     * @OA\Post(
     *     path="/api/events/{id}/join",
     *     summary="Unirse a un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Te has unido al evento")
     * )
     */
    public function join(Request $request, $id)
    {
        $event = EventKdd::findOrFail($id);
        $user = $request->user();

        if ($event->attendees()->where('user_id', $user->user_id)->exists()) {
            return response()->json(['message' => 'Ya estás inscrito en este evento'], 400);
        }

        // Verificar límite de participantes
        if ($event->max_participants > 0 && $event->attendees()->count() >= $event->max_participants) {
            return response()->json(['message' => 'El evento ha alcanzado el máximo de participantes'], 400);
        }

        $event->attendees()->attach($user->user_id);

        return response()->json(['message' => 'Te has unido al evento exitosamente']);
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{id}/leave",
     *     summary="Salir de un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Has salido del evento")
     * )
     */
    public function leave(Request $request, $id)
    {
        $event = EventKdd::findOrFail($id);
        $user = $request->user();

        $event->attendees()->detach($user->user_id);

        return response()->json(['message' => 'Has salido del evento exitosamente']);
    }
}
