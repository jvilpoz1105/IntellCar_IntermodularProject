<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Filters\CarAdvertFilter;
use App\Models\CarAdvert;
use App\Http\Resources\Api\MarketAdvertResource;
use App\Http\Resources\Api\MarketAdvertSummaryResource;
use Illuminate\Http\Request;

class MarketControl extends Controller
{
    /**
     * Obtener listado general con filtros opcionales.
     *
     * Parámetros de query soportados:
     *  Propios:      title[like], type[eq], price[lte|gte], km[lte|gte],
     *                color[eq|like], year[lte|gte], region[eq|like], city[eq|like]
     *  Relacionales: make[eq|like], model[eq|like], fuel[eq], engine[eq|like]
     *  Specs EAV:    modelSpec[<key>][eq|lt|gt|lte|gte|like]
     *                engineSpec[<key>][eq|lt|gt|lte|gte|like]
     *
     * Ejemplo: GET /api/market?price[lte]=30000&make[like]=BMW&engineSpec[cv][gte]=200
     */
    public function index(Request $request)
    {
        $filter  = new CarAdvertFilter();
        $queries = $filter->newTransform($request);

        $query = CarAdvert::query()
            ->where('visible', true)
            ->whereNull('onDeleteRequest');

        // ── Filtros sobre campos propios ──────────────────────────────────────
        if (!empty($queries['main'])) {
            $query->where($queries['main']);
        }

        // ── Filtros relacionales simples (make, model, fuel, engine) ──────────
        foreach ($queries['relations'] as $relationPath => $clauses) {
            foreach ($clauses as $clause) {
                $query->whereHas($relationPath, function ($q) use ($clause) {
                    $q->where($clause['column'], $clause['operator'], $clause['value']);
                });
            }
        }

        // ── Filtros EAV: modelSpec[<key>][op]=valor ───────────────────────────
        // Ejemplo: ?modelSpec[puertas][eq]=4  o  ?modelSpec[potencia][gte]=150
        $operatorMap = ['eq' => '=', 'like' => 'like', 'lt' => '<', 'gt' => '>', 'lte' => '<=', 'gte' => '>='];

        if ($request->has('modelSpec')) {
            foreach ($request->query('modelSpec') as $key => $filters) {
                $query->whereHas('model.specs', function ($q) use ($key, $filters, $operatorMap) {
                    $q->where('sp_key', $key);
                    foreach ($filters as $op => $value) {
                        $sqlOp = $operatorMap[$op] ?? '=';
                        if (in_array($op, ['gte', 'lte', 'gt', 'lt'])) {
                            $q->whereRaw("CAST(sp_value AS DECIMAL(10,2)) {$sqlOp} ?", [$value]);
                        } else {
                            $finalValue = ($op === 'like') ? "%{$value}%" : $value;
                            $q->where('sp_value', $sqlOp, $finalValue);
                        }
                    }
                });
            }
        }

        // ── Filtros EAV: engineSpec[<key>][op]=valor ──────────────────────────
        // Ejemplo: ?engineSpec[cv][gte]=200  o  ?engineSpec[traccion][eq]=AWD
        if ($request->has('engineSpec')) {
            foreach ($request->query('engineSpec') as $key => $filters) {
                $query->whereHas('engine.specs', function ($q) use ($key, $filters, $operatorMap) {
                    $q->where('sp_key', $key);
                    foreach ($filters as $op => $value) {
                        $sqlOp = $operatorMap[$op] ?? '=';
                        if (in_array($op, ['gte', 'lte', 'gt', 'lt'])) {
                            $q->whereRaw("CAST(sp_value AS DECIMAL(10,2)) {$sqlOp} ?", [$value]);
                        } else {
                            $finalValue = ($op === 'like') ? "%{$value}%" : $value;
                            $q->where('sp_value', $sqlOp, $finalValue);
                        }
                    }
                });
            }
        }

        $adverts = $query
            ->with(['model.make', 'media'])
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
