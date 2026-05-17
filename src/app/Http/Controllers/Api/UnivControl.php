<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Filters\PostFilter;
use App\Models\Post;
use App\Http\Resources\Api\UnivPostResource;
use App\Http\Resources\Api\UnivPostSummaryResource;
use App\Traits\ModeratesContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnivControl extends Controller
{
    use ModeratesContent;
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
     * Obtener listado general (solo datos más importantes).
     * Acepta query param ?mine=1 para filtrar solo posts del usuario autenticado.
     */
    public function index(Request $request)
    {
        $query = Post::with(['author:user_id,user_name,profile_picture', 'media', 'model.make'])
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
            ->whereNull('onDeleteRequest');

        if ($request->boolean('mine') && $request->user('sanctum')) {
            $userId = $request->user('sanctum')->user_id;
            $query->where('author_id', $userId);
        } elseif ($request->boolean('following') && $request->user('sanctum')) {
            $userId = $request->user('sanctum')->user_id;
            $followedIds = DB::table('user_follow')->where('follower_id', $userId)->pluck('followed_id');
            $query->whereIn('author_id', $followedIds);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);

        return UnivPostSummaryResource::collection($posts);
    }

    /**
     * Obtener por ID (todos los datos y relaciones).
     */
    public function show(Request $request, $id)
    {
        // No cargamos 'likes' via Eloquent porque la relación tiene withPivot('created_at')
        // y la tabla post_like no tiene esa columna. Usamos DB::table directamente.
        $post = Post::with(['author', 'model.make', 'engine', 'media', 'moods', 'comments.user'])
            ->findOrFail($id);

        $userId = $request->user('sanctum')?->user_id;
        $postId = $post->post_id;

        $data = (new UnivPostResource($post))->toArray($request);

        $data['likes_count'] = DB::table('post_like')->where('post_id', $postId)->count();
        $data['is_liked']    = $userId
            ? DB::table('post_like')->where('post_id', $postId)->where('user_id', $userId)->exists()
            : false;

        if (isset($data['author'])) {
            $data['author']['is_following'] = $userId
                ? DB::table('user_follow')
                    ->where('follower_id', $userId)
                    ->where('followed_id', $post->author_id)
                    ->exists()
                : false;
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Toggle like en un post (requiere autenticación).
     * Usa DB::table para evitar el withPivot('created_at') del modelo.
     */
    public function toggleLike(Request $request, $id)
    {
        $post   = Post::findOrFail($id);
        $userId = $request->user()->user_id;
        $postId = $post->post_id;

        $exists = DB::table('post_like')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            DB::table('post_like')
                ->where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();
            $liked = false;
        } else {
            DB::table('post_like')->insert(['post_id' => $postId, 'user_id' => $userId]);
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => DB::table('post_like')->where('post_id', $postId)->count(),
        ]);
    }

    /**
     * Añadir comentario a un post (requiere autenticación).
     */
    public function storeComment(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id'      => $request->user()->user_id,
            'comment_text' => $validated['comment_text'],
        ]);

        $comment->load('user:user_id,user_name');

        return response()->json([
            'comment'        => $comment,
            'comments_count' => $post->comments()->count(),
        ], 201);
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
