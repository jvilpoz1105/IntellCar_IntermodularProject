<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Make;
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
        $makes = Make::with(['models', 'engines'])->get();
        
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
}
