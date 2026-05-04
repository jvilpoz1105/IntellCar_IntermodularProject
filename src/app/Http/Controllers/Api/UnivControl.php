<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Resources\Api\UnivPostResource;
use App\Http\Resources\Api\UnivPostSummaryResource;
use Illuminate\Http\Request;

class UnivControl extends Controller
{
    /**
     * Obtener listado general (solo datos más importantes).
     */
    public function index()
    {
        // En el listado general evitamos cargar todos los comentarios o motores enteros
        $posts = Post::with(['author:user_id,username,profile_picture', 'media', 'model.make'])
            ->where('visible', true)
            ->whereNull('onDeleteRequest')
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

        // Verificar permisos (autor o admin)
        if ($post->author_id !== $request->user()->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para eliminar este post'], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post eliminado exitosamente'
        ]);
    }
}
