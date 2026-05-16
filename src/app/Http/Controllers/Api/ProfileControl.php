<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileControl extends Controller
{
    /**
     * Obtener los datos del perfil del usuario autenticado
     */
    public function show(Request $request)
    {
        $user = $request->user()->load(['paddock', 'garage', 'adverts', 'posts']);
        return response()->json($user);
    }

    /**
     * Actualizar los datos del perfil (nombre, teléfono, dirección, etc.)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'user_name' => ['sometimes', 'string', 'max:255', Rule::unique('app_user')->ignore($user->user_id, 'user_id')],
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Actualizar la foto de perfil
     */
    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $user = $request->user();

        // Borrar imagen anterior si existe
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profiles', 'public');

        $user->update(['profile_picture' => $path]);

        return response()->json([
            'message' => 'Foto de perfil actualizada',
            'profile_picture' => $path
        ]);
    }

    /**
     * Solicitar borrado de cuenta (Soft Delete)
     */
    public function softDelete(Request $request)
    {
        $user = $request->user();
        
        $user->update(['onDeleteRequest' => now()]);

        // Opcional: Podrías hacer $user->tokens()->delete() para cerrar su sesión inmediatamente

        return response()->json([
            'message' => 'Eliminación de cuenta solicitada exitosamente'
        ]);
    }

    /**
     * Añadir un vehículo al garaje del usuario
     */
    public function addGarageItem(Request $request)
    {
        $validated = $request->validate([
            'model_id'      => 'required|exists:car_model,model_id',
            'motor_id'      => 'nullable|exists:car_engine,engine_id',
            'car_nickname'  => 'nullable|string|max:50',
            'description'   => 'nullable|string',
            'is_current_car'=> 'boolean',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photo_url'     => 'nullable|string|url',  // URL S3 (alternativa a photo)
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            // Subida multipart tradicional
            $path = $request->file('photo')->store('garage', 'public');
        } elseif (!empty($validated['photo_url'])) {
            // URL ya subida a S3 desde el front
            $path = $validated['photo_url'];
        }

        $garageItem = $request->user()->garage()->create([
            'model_id'      => $validated['model_id'],
            'motor_id'      => $validated['motor_id'] ?? null,
            'car_nickname'  => $validated['car_nickname'] ?? null,
            'description'   => $validated['description'] ?? null,
            'is_current_car'=> $validated['is_current_car'] ?? false,
            'photo_url'     => $path,
            'verified_owner'=> false,
        ]);

        return response()->json([
            'message'     => 'Vehículo añadido al garaje',
            'garage_item' => $garageItem->load(['model.make'])
        ], 201);
    }


    /**
     * Actualizar un vehículo del garaje
     */
    public function updateGarageItem(Request $request, $id)
    {
        $garageItem = $request->user()->garage()->findOrFail($id);

        $validated = $request->validate([
            'model_id' => 'sometimes|exists:car_model,model_id',
            'motor_id' => 'nullable|exists:car_engine,engine_id',
            'car_nickname' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_current_car' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($garageItem->photo_url) {
                Storage::disk('public')->delete($garageItem->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('garage', 'public');
        }

        unset($validated['photo']);
        $garageItem->update($validated);

        return response()->json([
            'message' => 'Vehículo del garaje actualizado',
            'garage_item' => $garageItem->fresh()
        ]);
    }

    /**
     * Eliminar un vehículo del garaje
     */
    public function removeGarageItem(Request $request, $id)
    {
        $garageItem = $request->user()->garage()->findOrFail($id);

        if ($garageItem->photo_url) {
            Storage::disk('public')->delete($garageItem->photo_url);
        }

        $garageItem->delete();

        return response()->json([
            'message' => 'Vehículo eliminado del garaje'
        ]);
    }

    /**
     * Borrado definitivo (Solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->user_tag !== 'admin') {
            return response()->json(['message' => 'Acceso denegado. Solo administradores pueden ejecutar el borrado definitivo.'], 403);
        }

        $userToDelete = AppUser::findOrFail($id);

        // Borrar foto de perfil física
        if ($userToDelete->profile_picture) {
            Storage::disk('public')->delete($userToDelete->profile_picture);
        }

        // TODO: Eliminar otro contenido dependiente (anuncios, posts) o dejar que la base de datos lo borre en cascada
        
        $userToDelete->delete();

        return response()->json([
            'message' => 'Usuario eliminado definitivamente'
        ]);
    }
}
