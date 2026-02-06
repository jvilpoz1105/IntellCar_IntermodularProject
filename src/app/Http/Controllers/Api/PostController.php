<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Obtener listado de posts",
     *     tags={"Posts"},
     *     @OA\Response(response=200, description="Listado de posts")
     * )
     */
    public function index()
    {
        $posts = Post::with(['author', 'model.make', 'engine', 'media', 'moods', 'likes', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($posts);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Crear un nuevo post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=201, description="Post creado")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'content' => 'required|string',
            'model_id' => 'nullable|exists:car_model,model_id',
            'engine_id' => 'nullable|exists:car_engine,engine_id',
        ]);

        $post = Post::create([
            ...$validated,
            'author_id' => $request->user()->user_id,
        ]);

        return response()->json([
            'message' => 'Post creado exitosamente',
            'post' => $post->load(['author', 'model', 'engine']),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Obtener detalles de un post",
     *     tags={"Posts"},
     *     @OA\Response(response=200, description="Detalles del post")
     * )
     */
    public function show($id)
    {
        $post = Post::with(['author', 'model.make', 'engine', 'media', 'moods', 'likes', 'comments.user'])
            ->findOrFail($id);
        
        return response()->json($post);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Actualizar un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Post actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->author_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este post'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'content' => 'sometimes|string',
            'model_id' => 'nullable|exists:car_model,model_id',
            'engine_id' => 'nullable|exists:car_engine,engine_id',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post actualizado exitosamente',
            'post' => $post,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Eliminar un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Post eliminado")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->author_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para eliminar este post'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post eliminado exitosamente']);
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{id}/like",
     *     summary="Dar like a un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Like añadido")
     * )
     */
    public function like(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        if ($post->likes()->where('user_id', $user->user_id)->exists()) {
            return response()->json(['message' => 'Ya has dado like a este post'], 400);
        }

        $post->likes()->attach($user->user_id);

        return response()->json(['message' => 'Like añadido exitosamente']);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}/like",
     *     summary="Quitar like de un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Like eliminado")
     * )
     */
    public function unlike(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        $post->likes()->detach($user->user_id);

        return response()->json(['message' => 'Like eliminado exitosamente']);
    }
}
