<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/social",
     *     summary="Listar posts de la comunidad",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="mine",
     *         in="query",
     *         required=false,
     *         description="Filtrar solo posts del usuario autenticado",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="following",
     *         in="query",
     *         required=false,
     *         description="Filtrar solo posts de usuarios seguidos",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Listado paginado de posts")
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     *     path="/api/social/{id}",
     *     summary="Obtener detalle de un post",
     *     tags={"Posts"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del post con comentarios, likes y multimedia",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="likes_count", type="integer"),
     *             @OA\Property(property="is_liked", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post no encontrado")
     * )
     */
    public function show($id) {}

    /**
     * @OA\Post(
     *     path="/api/social/{id}/like",
     *     summary="Dar o quitar like a un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Estado del like actualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="liked", type="boolean"),
     *             @OA\Property(property="likes_count", type="integer")
     *         )
     *     )
     * )
     */
    public function toggleLike(Request $request, $id) {}

    /**
     * @OA\Post(
     *     path="/api/social/{id}/comment",
     *     summary="Añadir un comentario a un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment_text"},
     *             @OA\Property(property="comment_text", type="string", maxLength=1000, example="Gran coche!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comentario añadido",
     *         @OA\JsonContent(
     *             @OA\Property(property="comment", type="object"),
     *             @OA\Property(property="comments_count", type="integer")
     *         )
     *     )
     * )
     */
    public function storeComment(Request $request, $id) {}

    /**
     * @OA\Put(
     *     path="/api/social/{id}",
     *     summary="Actualizar un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Título del post"),
     *             @OA\Property(property="content", type="string", example="Contenido del post"),
     *             @OA\Property(property="model_id", type="integer", example=5),
     *             @OA\Property(property="engine_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Post actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este post"),
     *     @OA\Response(response=404, description="Post no encontrado")
     * )
     * @OA\Patch(
     *     path="/api/social/{id}",
     *     summary="Actualizar parcialmente un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Nuevo título"),
     *             @OA\Property(property="content", type="string", example="Contenido actualizado")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Post actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este post")
     * )
     */
    public function update(Request $request, $id) {}

    /**
     * @OA\Patch(
     *     path="/api/social/{id}/soft-delete",
     *     summary="Solicitar eliminación de un post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminación del post solicitada exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para solicitar la eliminación")
     * )
     */
    public function softDelete(Request $request, $id) {}

    /**
     * @OA\Delete(
     *     path="/api/social/{id}",
     *     summary="Eliminar un post definitivamente",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post y todas sus relaciones eliminados exitosamente"),
     *     @OA\Response(response=403, description="Solo los administradores pueden eliminar posts")
     * )
     */
    public function destroy(Request $request, $id) {}
}

