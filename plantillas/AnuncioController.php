<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anuncio as Anuncio;
use App\Filters\DroneAdvertFilter;
use App\Http\Resources\AdvertDetailsResource;
use App\Models\Dron;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\AssignOp\Mod;
use App\Http\Resources\AdvertResource;

class AnuncioController extends Controller
{
    private $pageSize = ['mobile' => 14, 'web' => 32];

    public function index(Request $request)
    {
        // Solo anuncios visibles
        return $this->getFilteredAdverts($request, true);
    }

    public function indexAll(Request $request)
    {
        // Todos los anuncios (incluyendo no visibles)
        return $this->getFilteredAdverts($request, false);
    }

    /**
     * Función auxiliar para manejar la lógica de filtrado tanto para anuncios visibles como para todos los anuncios.
     */
    private function getFilteredAdverts(Request $request, bool $onlyVisible)
    {
        $filter = new DroneAdvertFilter();
        $queries = $filter->newTransform($request);

        $query = Anuncio::query();

        if ($onlyVisible) {
            $query->where('visible', true);
        }

        $query->where($queries['main'] ?? []);

        foreach ($queries['relations'] as $relation => $clauses) {
            if ($relation !== 'dron.caracteristicas') {
                foreach ($clauses as $clause) {
                    $query->whereHas($relation, function ($q) use ($clause) {
                        $q->where($clause['column'], $clause['operator'], $clause['value']);
                    });
                }
            }
        }

        if ($request->has('spec')) {
            foreach ($request->query('spec') as $nombre => $filtros) {
                $query->whereHas('dron.caracteristicas', function ($q) use ($nombre, $filtros) {
                    $q->where('nombre', 'like', $nombre);
                    foreach ($filtros as $op => $valor) {
                        $sqlOp = $this->operatorMap[$op] ?? '=';
                        if (in_array($op, ['gte', 'lte', 'gt', 'lt'])) {
                            $q->whereRaw("CAST(valor AS DECIMAL(10,2)) $sqlOp ?", [$valor]);
                        } else {
                            $finalValue = ($op === 'like') ? "%{$valor}%" : $valor;
                            $q->where('valor', $sqlOp, $finalValue);
                        }
                    }
                });
            }
        }

        $adverts = $query->with([
            'vendedor',
            'dron.make',
            'dron.etiquetas',
            'dron.caracteristicas',
            'dron.multimedia'
        ])->paginate($this->pageSize['web']);

        if ($adverts->isEmpty()) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return AdvertResource::collection($adverts);
    }



    public function show($id)
    {
        $anuncio = Anuncio::with([
            'vendedor',
            'dron.make',
            'dron.etiquetas',
            'dron.caracteristicas',
            'dron.multimedia',
            'dron' => function ($query) {
                // Calculamos la media y el conteo directamente en el modelo Dron
                $query->withAvg('reviews as reviews_avg_score', 'score')
                    ->withCount('reviews as total_reviews_count');
            },
            'dron.reviews' => function ($query) {
                $query->latest()->limit(4)->with('authorUser:id,nombre');
            }
        ])->find($id);

        if (!$anuncio) {
            return response()->json(['message' => 'Anuncio no encontrado'], 404);
        }

        return new AdvertDetailsResource($anuncio);
    }

    public function search($titulo)
    {
        $anuncio = Anuncio::where('titulo', 'like', '%' . $titulo . '%')->get();

        if ($anuncio->isEmpty()) {
            return response()->json(['message' => 'Anuncio no encontrado'], 404);
        } else {
            $data = [
                'anuncio' => $anuncio,
                'status' => 200
            ];

            return response()->json($data, 200);
        }
    }

    public function aniadirAnuncio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'estado' => 'required|in:nuevo,usado_como_nuevo,usado_bueno,reacondicionado',
            'dron_id' => 'required|exists:drones,id',
            'vendedor_id' => 'required|exists:usuarios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $anuncio = Anuncio::create($request->all());

        return response()->json(['message' => 'Anuncio creado con exito', 'anuncio' => $anuncio], 201);
    }

    public function editarAnuncio(Request $request, $id)
    {
        $anuncio = Anuncio::find($id);

        if (!$anuncio) {
            return response()->json(['message' => 'Anuncio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'precio' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'estado' => 'sometimes|required|in:nuevo,usado_como_nuevo,usado_bueno,reacondicionado',
            'dron_id' => 'sometimes|required|exists:drones,id',
            'vendedor_id' => 'sometimes|required|exists:usuarios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $anuncio->update($request->all());

        return response()->json(['message' => 'Anuncio actualizado con exito', 'anuncio' => $anuncio], 200);
    }

    public function eliminarAnuncio($id)
    {
        $anuncio = Anuncio::find($id);

        if (!$anuncio) {
            return response()->json(['message' => 'Anuncio no encontrado'], 404);
        }

        $anuncio->delete();

        return response()->json(['message' => 'Anuncio eliminado con exito'], 200);
    }
}