<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="IntellCar API",
 *     version="1.0.0",
 *     description="API REST para la aplicación IntellCar - Plataforma de compraventa y comunidad de coches",
 *     @OA\Contact(
 *         email="admin@intellcar.com"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Registrar nuevo usuario",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_name","email_address","phone","user_password"},
     *             @OA\Property(property="user_name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email_address", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="+34600000000"),
     *             @OA\Property(property="user_password", type="string", format="password", example="password123"),
     *             @OA\Property(property="contact_email", type="string", format="email", example="contact@example.com"),
     *             @OA\Property(property="address", type="string", example="Calle Principal 123"),
     *             @OA\Property(property="paddock_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:90',
            'email_address' => 'required|email|unique:app_user,email_address',
            'phone' => 'required|string|unique:app_user,phone',
            'user_password' => 'required|string|min:6',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'paddock_id' => 'nullable|exists:paddock,paddock_id',
        ]);

        $user = AppUser::create([
            'user_name' => $validated['user_name'],
            'email_address' => $validated['email_address'],
            'phone' => $validated['phone'],
            'user_password' => Hash::make($validated['user_password']),
            'contact_email' => $validated['contact_email'] ?? null,
            'address' => $validated['address'] ?? null,
            'user_tag' => 'indv',
            'paddock_id' => $validated['paddock_id'] ?? null,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Iniciar sesión",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email_address","user_password"},
     *             @OA\Property(property="email_address", type="string", format="email", example="user@intellcar.com"),
     *             @OA\Property(property="user_password", type="string", format="password", example="user123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciales incorrectas")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email',
            'user_password' => 'required',
        ]);

        $user = AppUser::where('email_address', $request->email_address)->first();

        if (!$user || !Hash::check($request->user_password, $user->user_password)) {
            throw ValidationException::withMessages([
                'email_address' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada. Contacta con soporte.',
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Cerrar sesión",
     *     tags={"Autenticación"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Sesión cerrada exitosamente")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Obtener usuario autenticado",
     *     tags={"Autenticación"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario",
     *         @OA\JsonContent(ref="#/components/schemas/AppUser")
     *     )
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->load('paddock'));
    }
}
