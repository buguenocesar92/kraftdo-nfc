<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentProfile;
use App\Models\ContentBusiness;
use App\Models\ContentGift;
use App\Models\ContentTourist;
use App\Models\BusStop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    /**
     * Mostrar contenido específico por tipo e ID
     */
    public function show(string $type, string $id): JsonResponse
    {
        try {
            $model = $this->getModelByType($type);
            $relations = $this->getRelationsByType($type);
            
            $content = $model::with($relations)->findOrFail($id);
            
            return response()->json([
                'data' => $content,
                'message' => "Contenido {$type} obtenido exitosamente",
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Crear nuevo contenido
     */
    public function store(Request $request, string $type): JsonResponse
    {
        try {
            $model = $this->getModelByType($type);
            $validationRules = $this->getValidationRules($type);
            
            $validatedData = $request->validate($validationRules);
            
            $content = $model::create($validatedData);
            
            return response()->json([
                'data' => $content,
                'message' => "Contenido {$type} creado exitosamente",
                'status' => 201
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Actualizar contenido existente
     */
    public function update(Request $request, string $type, string $id): JsonResponse
    {
        try {
            $model = $this->getModelByType($type);
            $validationRules = $this->getValidationRules($type, true); // true = update mode
            
            $content = $model::findOrFail($id);
            $validatedData = $request->validate($validationRules);
            
            $content->update($validatedData);
            
            return response()->json([
                'data' => $content->fresh(),
                'message' => "Contenido {$type} actualizado exitosamente",
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Eliminar contenido
     */
    public function destroy(string $type, string $id): JsonResponse
    {
        try {
            $model = $this->getModelByType($type);
            $content = $model::findOrFail($id);
            
            $content->delete();
            
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} eliminado exitosamente",
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Obtener modelo por tipo de contenido
     */
    private function getModelByType(string $type): string
    {
        return match($type) {
            'profile' => ContentProfile::class,
            'business' => ContentBusiness::class,
            'gift' => ContentGift::class,
            'tourist' => ContentTourist::class,
            'bus_stop' => BusStop::class,
            default => throw new \InvalidArgumentException("Tipo de contenido no soportado: {$type}")
        };
    }

    /**
     * Obtener relaciones por tipo de contenido
     */
    private function getRelationsByType(string $type): array
    {
        return match($type) {
            'profile' => ['skills', 'socialLinks', 'galleryImages'],
            'business' => ['products'],
            'gift' => ['multimedia'],
            'tourist' => ['nearbySpots'],
            'bus_stop' => ['routes', 'schedules', 'utilityPhones'],
            default => []
        };
    }

    /**
     * Obtener reglas de validación por tipo de contenido
     */
    private function getValidationRules(string $type, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';
        
        return match($type) {
            'profile' => [
                'name' => "{$required}|string|max:255",
                'bio' => 'nullable|string|max:1000',
                'avatar' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500',
                'color_palette' => 'nullable|json'
            ],
            'business' => [
                'name' => "{$required}|string|max:255",
                'description' => 'nullable|string|max:1000',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500',
                'logo' => 'nullable|string|max:500'
            ],
            'gift' => [
                'title' => "{$required}|string|max:255",
                'message' => 'nullable|string|max:2000',
                'sender_name' => 'nullable|string|max:255',
                'recipient_name' => 'nullable|string|max:255'
            ],
            'tourist' => [
                'name' => "{$required}|string|max:255",
                'description' => 'nullable|string|max:1000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500'
            ],
            'bus_stop' => [
                'name' => "{$required}|string|max:255",
                'code' => "{$required}|string|max:50|unique:bus_stops,code" . ($isUpdate ? ',{id}' : ''),
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'address' => 'nullable|string|max:500'
            ],
            default => throw new \InvalidArgumentException("Tipo de contenido no soportado: {$type}")
        };
    }
}