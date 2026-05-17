<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

class AppUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Listar todos los usuarios",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado de usuarios"),
     *     @OA\Response(response=403, description="Acceso denegado (solo admin)")
     * )
     */
    public function index()
    {
        $users = AppUser::with('paddock')->paginate(15);
        return response()->json($users);
    }

    /**
     * Store a newly created resource (Handled by AuthController register).
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Use /api/auth/register for user creation'], 405);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obtener perfil de un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Datos del usuario"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show(string $id)
    {
        $user = AppUser::with(['paddock', 'garage', 'posts'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/follow",
     *     summary="Seguir o dejar de seguir a un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Estado del seguimiento actualizado"),
     *     @OA\Response(response=422, description="No puedes seguirte a ti mismo")
     * )
     */
    public function toggleFollow(Request $request, string $id)
    {
        $targetUser = AppUser::findOrFail($id);
        $currentUser = $request->user();

        if ($currentUser->user_id === (int) $id) {
            return response()->json(['message' => 'No puedes seguirte a ti mismo'], 422);
        }

        if ($currentUser->following()->where('followed_id', $id)->exists()) {
            $currentUser->following()->detach($id);
            $following = false;
        } else {
            $currentUser->following()->attach($id);
            $following = true;
        }

        return response()->json([
            'following' => $following,
            'followers_count' => $targetUser->followers()->count(),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Actualizar datos de un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuario actualizado correctamente"),
     *     @OA\Response(response=403, description="Sin permiso para editar este perfil")
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = AppUser::findOrFail($id);

        // Security check: Only owner or admin
        if ($request->user()->user_id != $user->user_id && $request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para editar este perfil'], 403);
        }

        $validated = $request->validate([
            'user_name' => 'sometimes|string|max:90',
            'contact_email' => 'sometimes|email',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string|unique:app_user,phone,' . $id . ',user_id',
            'user_password' => 'sometimes|string|min:6',
            'paddock_id' => 'sometimes|exists:paddock,paddock_id',
        ]);

        if (isset($validated['user_password'])) {
            $validated['user_password'] = Hash::make($validated['user_password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuario eliminado correctamente"),
     *     @OA\Response(response=403, description="Solo los administradores pueden eliminar usuarios")
     * )
     */
    public function destroy(string $id)
    {
        $user = AppUser::findOrFail($id);
        
        // Prevents deleting yourself
        if (auth()->id() == $id) {
            return response()->json(['message' => 'No puedes borrar tu propia cuenta'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
