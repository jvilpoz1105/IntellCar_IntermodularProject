<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class AppUserController extends Controller
{
    /**
     * Display a listing of the resource (Only Admin).
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = AppUser::with(['paddock', 'garage', 'posts'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource.
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
     * Remove the specified resource (Only Admin).
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
