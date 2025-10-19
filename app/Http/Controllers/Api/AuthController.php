<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login del usuario
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'data' => null,
                    'message' => 'Credenciales incorrectas',
                    'status' => 401,
                ], 401);
            }

            // Revocar tokens existentes para evitar acumulación
            $user->tokens()->delete();

            // Crear nuevo token
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
                'message' => 'Login exitoso',
                'status' => 200,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Crear token para el nuevo usuario
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
                'message' => 'Usuario registrado exitosamente',
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'data' => null,
                'message' => 'Logout exitoso',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'data' => $request->user(),
                'message' => 'Información del usuario obtenida exitosamente',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }
}
