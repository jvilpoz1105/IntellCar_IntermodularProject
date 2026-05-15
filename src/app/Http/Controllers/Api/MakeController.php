<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\CarEngine;
use Illuminate\Http\Request;

class MakeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/makes",
     *     summary="Obtener listado de marcas de coches",
     *     tags={"Marcas"},
     *     @OA\Response(response=200, description="Listado de marcas")
     * )
     */
    public function index()
    {
        $makes = Make::orderBy('make_name')->get(['make_id', 'make_name', 'origin_country', 'status']);

        return response()->json($makes);
    }

    /**
     * @OA\Get(
     *     path="/api/makes/{id}",
     *     summary="Obtener detalles de una marca",
     *     tags={"Marcas"},
     *     @OA\Response(response=200, description="Detalles de la marca")
     * )
     */
    public function show($id)
    {
        $make = Make::with(['models', 'engines'])->findOrFail($id);

        return response()->json($make);
    }

    /**
     * Obtener los modelos de una marca específica.
     */
    public function models($id)
    {
        $make = Make::findOrFail($id);

        $models = CarModel::where('make_id', $make->make_id)
            ->orderBy('model_name')
            ->get(['model_id', 'model_name', 'make_id']);

        return response()->json($models);
    }

    /**
     * Obtener los motores disponibles para una marca específica.
     */
    public function engines($id)
    {
        $make = Make::findOrFail($id);

        $engines = CarEngine::where('make_id', $make->make_id)
            ->orderBy('engine_name')
            ->get(['engine_id', 'engine_name', 'fuel_type', 'make_id']);

        return response()->json($engines);
    }
}
