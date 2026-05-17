<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarAdvert;
use App\Http\Resources\Api\MarketAdvertResource;
use App\Http\Resources\Api\MarketAdvertSummaryResource;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class MarketControl extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/market",
     *     summary="Listar anuncios de coches",
     *     tags={"Anuncios"},
     *     @OA\Parameter(name="visible", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="ad_type", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Listado de anuncios")
     * )
     */
    public function index(Request $request)
    {
        // Para el listado general traemos solo las relaciones básicas (modelo, fotos).
        // Evitamos sobrecargar con motor, detalles complejos o todos los estados de ánimo (moods).
        $query = CarAdvert::with(['model.make', 'media'])
            ->whereNull('onDeleteRequest');

        if ($request->boolean('visible', true)) {
            $query->where('visible', true);
        }

        if ($request->filled('ad_type')) {
            $query->where('ad_type', $request->input('ad_type'));
        }

        $adverts = $query->orderBy('created_at', 'desc')->paginate(20);

        return MarketAdvertSummaryResource::collection($adverts);
    }

    /**
     * @OA\Get(
     *     path="/api/market/{id}",
     *     summary="Obtener detalle de un anuncio",
     *     tags={"Anuncios"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalle del anuncio"),
     *     @OA\Response(response=404, description="Anuncio no encontrado")
     * )
     */
    public function show($id)
    {
        // Aquí sí traemos absolutamente todos los datos relacionados:
        // multimedia, características del motor, vendedor, estados (moods), etc.
        $advert = CarAdvert::with(['model.make', 'engine.specs', 'seller', 'media', 'moods'])->findOrFail($id);
        
        return new MarketAdvertResource($advert);
    }

    /**
     * @OA\Put(
     *     path="/api/market/{id}",
     *     summary="Actualizar un anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="ad_title", type="string", example="BMW Serie 3 320d Automatico"),
     *             @OA\Property(property="ad_type", type="string", enum={"new","km0","used","renting","leasing","supcription"}, example="used"),
     *             @OA\Property(property="ad_details", type="string", example="Excelente estado, mantenimiento al día"),
     *             @OA\Property(property="price", type="number", format="float", example=23990.00),
     *             @OA\Property(property="kilometers", type="integer", example=45000),
     *             @OA\Property(property="car_color", type="string", enum={"blanco","negro","gris","plata","rojo","azul","verde","amarillo","naranja","otro"}, example="gris"),
     *             @OA\Property(property="year_manufacture", type="integer", example=2020),
     *             @OA\Property(property="region", type="string", example="Madrid"),
     *             @OA\Property(property="city", type="string", example="Alcobendas"),
     *             @OA\Property(property="visible", type="boolean", example=true),
     *             @OA\Property(property="model_id", type="integer", example=12),
     *             @OA\Property(property="engine_id", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Anuncio actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este anuncio"),
     *     @OA\Response(response=404, description="Anuncio no encontrado")
     * )
     * @OA\Patch(
     *     path="/api/market/{id}",
     *     summary="Actualizar parcialmente un anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="price", type="number", format="float", example=21500.00),
     *             @OA\Property(property="visible", type="boolean", example=false),
     *             @OA\Property(property="ad_details", type="string", example="Precio negociable")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Anuncio actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este anuncio")
     * )
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
     * @OA\Patch(
     *     path="/api/market/{id}/soft-delete",
     *     summary="Solicitar eliminación de un anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminación del anuncio solicitada exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso")
     * )
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
     * @OA\Delete(
     *     path="/api/market/{id}",
     *     summary="Eliminar un anuncio definitivamente",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Anuncio y sus relaciones eliminados exitosamente"),
     *     @OA\Response(response=403, description="Solo los administradores pueden eliminar anuncios")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Seguridad extra (aunque ya esté protegido por middleware)
        if ($request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden eliminar este anuncio definitivamente'], 403);
        }

        // 1. Desvincular de los moods (sin afectar a los paddocks en sí)
        $advert->moods()->detach();

        // 2. Borrar las entradas en la tabla de multimedia
        // TODO: MÁS ADELANTE SE DEBEN BORRAR FÍSICAMENTE DE S3 TAMBIÉN
        $advert->media()->delete();

        // 3. Finalmente borrar el anuncio
        $advert->delete();

        return response()->json([
            'message' => 'Anuncio y sus relaciones eliminados exitosamente',
        ]);
    }
}
