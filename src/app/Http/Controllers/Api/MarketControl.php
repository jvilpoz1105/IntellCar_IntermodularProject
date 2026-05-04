<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarAdvert;
use App\Http\Resources\Api\MarketAdvertResource;
use App\Http\Resources\Api\MarketAdvertSummaryResource;
use Illuminate\Http\Request;

class MarketControl extends Controller
{
    /**
     * Obtener listado general (solo datos más importantes).
     */
    public function index()
    {
        // Para el listado general traemos solo las relaciones básicas (modelo, fotos).
        // Evitamos sobrecargar con motor, detalles complejos o todos los estados de ánimo (moods).
        $adverts = CarAdvert::with(['model.make', 'media'])
            ->where('visible', true)
            ->whereNull('onDeleteRequest')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return MarketAdvertSummaryResource::collection($adverts);
    }

    /**
     * Obtener por ID (todos los datos y relaciones).
     */
    public function show($id)
    {
        // Aquí sí traemos absolutamente todos los datos relacionados:
        // multimedia, características del motor, vendedor, estados (moods), etc.
        $advert = CarAdvert::with(['model.make', 'engine', 'seller', 'media', 'moods'])->findOrFail($id);
        
        return new MarketAdvertResource($advert);
    }

    /**
     * Actualizar anuncio (PUT/PATCH - actualización parcial permitida).
     */
    public function update(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Verificar permisos (dueño o admin)
        if ($advert->seller_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este anuncio'], 403);
        }

        // Usamos 'sometimes' para permitir actualizaciones parciales (PATCH)
        $validated = $request->validate([
            'ad_title' => 'sometimes|string|max:165',
            'ad_type' => 'sometimes|in:new,km0,used,renting,leasing,supcription',
            'ad_details' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'kilometers' => 'nullable|integer|min:0',
            'car_color' => 'sometimes|in:blanco,negro,gris,plata,rojo,azul,verde,amarillo,naranja,otro',
            'year_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'region' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'visible' => 'sometimes|boolean',
            'model_id' => 'sometimes|exists:car_model,model_id',
            'engine_id' => 'sometimes|exists:car_engine,engine_id',
        ]);

        $advert->update($validated);

        return response()->json([
            'message' => 'Anuncio actualizado exitosamente',
            'advert' => $advert->load(['model.make', 'engine']),
        ]);
    }

    /**
     * Solicitar eliminación de un anuncio (Soft Delete).
     */
    public function softDelete(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Verificar permisos (dueño o admin)
        if ($advert->seller_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para solicitar la eliminación de este anuncio'], 403);
        }

        $advert->update(['onDeleteRequest' => now()]);

        return response()->json([
            'message' => 'Eliminación del anuncio solicitada exitosamente',
        ]);
    }

    /**
     * Eliminar un anuncio por ID.
     */
    public function destroy(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Verificar permisos (dueño o admin)
        if ($advert->seller_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para eliminar este anuncio'], 403);
        }

        $advert->delete();

        return response()->json([
            'message' => 'Anuncio eliminado exitosamente',
        ]);
    }
}
