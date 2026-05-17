<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/kdds",
     *     summary="Listar eventos/quedadas",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="mine",
     *         in="query",
     *         required=false,
     *         description="Filtrar solo eventos en los que participa el usuario autenticado",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Listado paginado de eventos")
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     *     path="/api/kdds/{id}",
     *     summary="Obtener detalle de un evento",
     *     tags={"Eventos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalle del evento con asistentes"),
     *     @OA\Response(response=404, description="Evento no encontrado")
     * )
     */
    public function show($id) {}

    /**
     * @OA\Put(
     *     path="/api/kdds/{id}",
     *     summary="Actualizar un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Quedada Madrid Sur"),
     *             @OA\Property(property="event_description", type="string", example="Descripción del evento"),
     *             @OA\Property(property="event_date", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *             @OA\Property(property="location_name", type="string", example="Circuito del Jarama"),
     *             @OA\Property(property="address", type="string", example="Ctra. de Burgos, km 28"),
     *             @OA\Property(property="city", type="string", example="San Sebastián de los Reyes"),
     *             @OA\Property(property="latitude", type="number", format="float", example=40.6),
     *             @OA\Property(property="longitude", type="number", format="float", example=-3.6),
     *             @OA\Property(property="max_participants", type="integer", example=50),
     *             @OA\Property(property="paddock_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Evento actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este evento"),
     *     @OA\Response(response=404, description="Evento no encontrado")
     * )
     * @OA\Patch(
     *     path="/api/kdds/{id}",
     *     summary="Actualizar parcialmente un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Nuevo título"),
     *             @OA\Property(property="event_date", type="string", format="date-time", example="2025-08-15T10:00:00Z"),
     *             @OA\Property(property="city", type="string", example="Madrid"),
     *             @OA\Property(property="max_participants", type="integer", example=30)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Evento actualizado exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este evento")
     * )
     */
    public function update(Request $request, $id) {}

    /**
     * @OA\Post(
     *     path="/api/kdds/{id}/join",
     *     summary="Unirse a un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Te has unido al evento correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="attendees_count", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=409, description="Ya estás inscrito en este evento"),
     *     @OA\Response(response=422, description="El evento ha alcanzado el límite de participantes")
     * )
     */
    public function join(Request $request, $id) {}

    /**
     * @OA\Delete(
     *     path="/api/kdds/{id}/join",
     *     summary="Abandonar un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Has abandonado el evento",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="attendees_count", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="No estás inscrito en este evento")
     * )
     */
    public function leave(Request $request, $id) {}

    /**
     * @OA\Patch(
     *     path="/api/kdds/{id}/soft-delete",
     *     summary="Solicitar eliminación de un evento",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminación del evento solicitada exitosamente"),
     *     @OA\Response(response=403, description="Sin permiso para solicitar la eliminación")
     * )
     */
    public function softDelete(Request $request, $id) {}

    /**
     * @OA\Delete(
     *     path="/api/kdds/{id}",
     *     summary="Eliminar un evento definitivamente",
     *     tags={"Eventos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Evento y sus asistentes eliminados exitosamente"),
     *     @OA\Response(response=403, description="Solo los administradores pueden eliminar eventos")
     * )
     */
    public function destroy(Request $request, $id) {}
}

