<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Filters\PostFilter;
use App\Models\Post;
use App\Http\Resources\Api\UnivPostResource;
use App\Http\Resources\Api\UnivPostSummaryResource;
use Illuminate\Http\Request;

class UnivControl extends Controller
{
    /**
     * Obtener listado general con filtros opcionales.
     *
     * Parámetros de query soportados:
     *  Propios:      title[like], content[like]
     *  Relacionales: author[eq|like], make[eq|like], model[eq|like], fuel[eq], engine[eq|like], mood[eq|like]
     *  Specs EAV:    modelSpec[<key>][eq|lt|gt|lte|gte|like]
     *                engineSpec[<key>][eq|lt|gt|lte|gte|like]
     *
     * Ejemplo: GET /api/posts?make[like]=BMW&mood[eq]=drift&engineSpec[cv][gte]=200
     */
    public function index(Request $request)
    {
        $filter  = new PostFilter();
        $queries = $filter->newTransform($request);

        $query = Post::query()
            ->where('visible', true)
            ->whereNull('onDeleteRequest');

        // ── Filtros sobre campos propios ──────────────────────────────────────
        if (!empty($queries['main'])) {
            $query->where($queries['main']);
        }

        // ── Filtros relacionales simples (author, make, model, engine, mood) ──
        foreach ($queries['relations'] as $relationPath => $clauses) {
            foreach ($clauses as $clause) {
                $query->whereHas($relationPath, function ($q) use ($clause) {
                    $q->where($clause['column'], $clause['operator'], $clause['value']);
                });
            }
        }

        // ── Filtros EAV: modelSpec[<key>][op]=valor ───────────────────────────
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

        $posts = $query
            ->with(['author:user_id,username,profile_picture', 'media', 'model.make'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return UnivPostSummaryResource::collection($posts);
    }

    /**
     * Obtener por ID (todos los datos y relaciones).
     */
    public function show($id)
    {
        // Traemos todas las relaciones, incluyendo motor, likes y usuarios de cada comentario
        $post = Post::with(['author', 'model.make', 'engine', 'media', 'moods', 'likes', 'comments.user'])
            ->findOrFail($id);
        
        return new UnivPostResource($post);
    }

    /**
     * Actualizar post (PUT/PATCH - actualización parcial permitida).
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Verificar permisos (autor o admin)
        if ($post->author_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este post'], 403);
        }

        // 'sometimes' permite actualizar solo lo que se envíe
        $validated = $request->validate([
            'title' => 'sometimes|string|max:150|nullable',
            'content' => 'sometimes|string',
            'model_id' => 'sometimes|exists:car_model,model_id|nullable',
            'engine_id' => 'sometimes|exists:car_engine,engine_id|nullable',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post actualizado exitosamente',
            'post' => $post->fresh(['author', 'model.make']),
        ]);
    }

    /**
     * Solicitar eliminación de un post (Soft Delete).
     */
    public function softDelete(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Verificar permisos (autor o admin)
        if ($post->author_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para solicitar la eliminación de este post'], 403);
        }

        $post->update(['onDeleteRequest' => now()]);

        return response()->json([
            'message' => 'Eliminación del post solicitada exitosamente',
        ]);
    }

    /**
     * Eliminar un post por ID.
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden eliminar este post definitivamente'], 403);
        }

        // 1. Desvincular relaciones N:M
        $post->likes()->detach();
        $post->moods()->detach();

        // 2. Eliminar comentarios del post
        $post->comments()->delete();

        // 3. Borrar las entradas en la tabla de multimedia
        // TODO: MÁS ADELANTE SE DEBEN BORRAR FÍSICAMENTE DE S3 TAMBIÉN
        $post->media()->delete();

        // 4. Finalmente borrar el post
        $post->delete();

        return response()->json([
            'message' => 'Post y todas sus relaciones eliminados exitosamente'
        ]);
    }
}
