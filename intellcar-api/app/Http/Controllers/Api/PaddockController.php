<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paddock;
use Illuminate\Http\Request;

class PaddockController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/paddocks",
     *     summary="Obtener listado de paddocks/comunidades",
     *     tags={"Paddocks"},
     *     @OA\Response(response=200, description="Listado de paddocks")
     * )
     */
    public function index()
    {
        $paddocks = Paddock::withCount(['users', 'adverts', 'posts', 'events'])->get();
        
        return response()->json($paddocks);
    }

    /**
     * @OA\Get(
     *     path="/api/paddocks/{id}",
     *     summary="Obtener detalles de un paddock",
     *     tags={"Paddocks"},
     *     @OA\Response(response=200, description="Detalles del paddock")
     * )
     */
    public function show($id)
    {
        $paddock = Paddock::with(['users', 'adverts', 'posts', 'events'])
            ->withCount(['users', 'adverts', 'posts', 'events'])
            ->findOrFail($id);
        
        return response()->json($paddock);
    }
}
