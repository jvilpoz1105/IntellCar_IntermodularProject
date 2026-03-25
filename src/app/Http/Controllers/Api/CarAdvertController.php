<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarAdvert;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CarAdvertController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/adverts",
     *     summary="Obtener listado de anuncios",
     *     tags={"Anuncios"},
     *     @OA\Parameter(
     *         name="visible",
     *         in="query",
     *         description="Filtrar por visibilidad (solo anuncios visibles)",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="ad_type",
     *         in="query",
     *         description="Filtrar por tipo de anuncio",
     *         @OA\Schema(type="string", enum={"new","km0","used","renting","leasing","supcription"})
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de anuncios",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CarAdvert"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = CarAdvert::with(['model.make', 'engine', 'seller', 'media', 'moods']);

        // Filtros
        if ($request->has('visible')) {
            $query->where('visible', $request->boolean('visible'));
        }

        if ($request->has('ad_type')) {
            $query->where('ad_type', $request->ad_type);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('car_color')) {
            $query->where('car_color', $request->car_color);
        }

        if ($request->has('region')) {
            $query->where('region', 'LIKE', '%' . $request->region . '%');
        }

        if ($request->has('city')) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        $adverts = $query->orderBy('publish_date', 'desc')->paginate(20);

        return response()->json($adverts);
    }

    /**
     * @OA\Post(
     *     path="/api/adverts",
     *     summary="Crear un nuevo anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ad_title","ad_type","price","model_id","engine_id","car_color","region","city"},
     *             @OA\Property(property="ad_title", type="string"),
     *             @OA\Property(property="ad_type", type="string", enum={"new","km0","used","renting","leasing","supcription"}),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="model_id", type="integer"),
     *             @OA\Property(property="engine_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Anuncio creado")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_title' => 'required|string|max:165',
            'ad_type' => 'required|in:new,km0,used,renting,leasing,supcription',
            'ad_details' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'kilometers' => 'nullable|integer|min:0',
            'car_color' => 'required|in:blanco,negro,gris,plata,rojo,azul,verde,amarillo,naranja,otro',
            'year_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'region' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'model_id' => 'required|exists:car_model,model_id',
            'engine_id' => 'required|exists:car_engine,engine_id',
        ]);

        $advert = CarAdvert::create([
            ...$validated,
            'seller_id' => $request->user()->user_id,
            'visible' => false, // Por defecto no visible hasta revisión
        ]);

        return response()->json([
            'message' => 'Anuncio creado exitosamente',
            'advert' => $advert->load(['model.make', 'engine', 'seller']),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/adverts/{id}",
     *     summary="Obtener detalles de un anuncio",
     *     tags={"Anuncios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalles del anuncio")
     * )
     */
    public function show($id)
    {
        $advert = CarAdvert::with(['model.make', 'engine', 'seller', 'media', 'moods'])->findOrFail($id);
        
        return response()->json($advert);
    }

    /**
     * @OA\Put(
     *     path="/api/adverts/{id}",
     *     summary="Actualizar un anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Anuncio actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Verificar que el usuario es el dueño del anuncio o es admin
        if ($advert->seller_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este anuncio'], 403);
        }

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
        ]);

        $advert->update($validated);

        return response()->json([
            'message' => 'Anuncio actualizado exitosamente',
            'advert' => $advert->load(['model.make', 'engine']),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/adverts/{id}",
     *     summary="Eliminar un anuncio",
     *     tags={"Anuncios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Anuncio eliminado")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $advert = CarAdvert::findOrFail($id);

        // Verificar que el usuario es el dueño del anuncio o es admin
        if ($advert->seller_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para eliminar este anuncio'], 403);
        }

        $advert->delete();

        return response()->json([
            'message' => 'Anuncio eliminado exitosamente',
        ]);
    }
}
