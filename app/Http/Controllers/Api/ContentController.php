<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusStop;
use App\Models\ContentBusiness;
use App\Models\ContentGift;
use App\Models\ContentProduct;
use App\Models\ContentProfile;
use App\Models\ContentSocialLink;
use App\Models\ContentTourist;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404,
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
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
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
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
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404,
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
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
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => "Contenido {$type} con ID {$id} no encontrado",
                'status' => 404,
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Obtener modelo por tipo de contenido
     */
    private function getModelByType(string $type): string
    {
        return match ($type) {
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
        return match ($type) {
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

        return match ($type) {
            'profile' => [
                'name' => "{$required}|string|max:255",
                'bio' => 'nullable|string|max:1000',
                'avatar' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500',
                'color_palette' => 'nullable|json',
            ],
            'business' => [
                'name' => "{$required}|string|max:255",
                'description' => 'nullable|string|max:1000',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500',
                'logo' => 'nullable|string|max:500',
            ],
            'gift' => [
                'title' => "{$required}|string|max:255",
                'message' => 'nullable|string|max:2000',
                'sender_name' => 'nullable|string|max:255',
                'recipient_name' => 'nullable|string|max:255',
            ],
            'tourist' => [
                'name' => "{$required}|string|max:255",
                'description' => 'nullable|string|max:1000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:500',
            ],
            'bus_stop' => [
                'name' => "{$required}|string|max:255",
                'code' => "{$required}|string|max:50|unique:bus_stops,code" . ($isUpdate ? ',{id}' : ''),
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'address' => 'nullable|string|max:500',
            ],
            default => throw new \InvalidArgumentException("Tipo de contenido no soportado: {$type}")
        };
    }

    // ========================================
    // DYNAMIC CONTENT MANAGEMENT METHODS
    // ========================================

    /**
     * Create dynamic content for a token
     */
    public function createDynamicContent(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'token_id' => 'required|exists:nfc_tokens,id',
                'type' => 'required|string|in:PROFILE,BUSINESS,GIFT,EVENT,TOURIST,BUS_STOP',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            // Verify user owns the token
            $token = NfcToken::where('id', $validatedData['token_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create dynamic content
            $dynamicContent = DynamicContent::create([
                'content_id' => \Illuminate\Support\Str::uuid(),
                'type' => $validatedData['type'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'data' => [], // Valor por defecto para el campo data
                'nfc_token_id' => $token->id,
                'user_id' => Auth::id(),
                'is_active' => true,
                'status' => 'draft',
            ]);

            return response()->json([
                'data' => $dynamicContent,
                'message' => 'Contenido dinámico creado exitosamente',
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
     * Get dynamic content by ID
     */
    public function getDynamicContent(int $id): JsonResponse
    {
        try {
            $dynamicContent = DynamicContent::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'data' => $dynamicContent,
                'message' => 'Contenido dinámico obtenido exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido dinámico no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Update dynamic content
     */
    public function updateDynamicContent(Request $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'sometimes|boolean',
                'status' => 'sometimes|string|in:draft,published',
            ]);

            $dynamicContent = DynamicContent::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $dynamicContent->update($validatedData);

            return response()->json([
                'data' => $dynamicContent->fresh(),
                'message' => 'Contenido dinámico actualizado exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido dinámico no encontrado',
                'status' => 404,
            ], 404);
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
     * Delete dynamic content
     */
    public function deleteDynamicContent(int $id): JsonResponse
    {
        try {
            $dynamicContent = DynamicContent::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $dynamicContent->delete();

            return response()->json([
                'data' => null,
                'message' => 'Contenido dinámico eliminado exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido dinámico no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    // ========================================
    // PROFILE CONTENT METHODS
    // ========================================

    /**
     * Create profile content
     */
    public function createProfileContent(Request $request, int $dynamicContentId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_website' => 'nullable|url|max:500',
                'bio' => 'nullable|string|max:1000',
                'profession' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'color_palette' => 'nullable|array',
            ]);

            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $profileContent = ContentProfile::create([
                'dynamic_content_id' => $dynamicContentId,
                'name' => $validatedData['name'],
                'contact_email' => $validatedData['contact_email'] ?? null,
                'contact_phone' => $validatedData['contact_phone'] ?? null,
                'contact_website' => $validatedData['contact_website'] ?? null,
                'bio' => $validatedData['bio'] ?? null,
                'profession' => $validatedData['profession'] ?? null,
                'company' => $validatedData['company'] ?? null,
                'location' => $validatedData['location'] ?? null,
                'color_palette' => $validatedData['color_palette'] ?? null,
            ]);

            return response()->json([
                'data' => $profileContent,
                'message' => 'Contenido de perfil creado exitosamente',
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
     * Get profile content by dynamic content ID
     */
    public function getProfileContent(int $dynamicContentId): JsonResponse
    {
        try {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $profileContent = ContentProfile::where('dynamic_content_id', $dynamicContentId)
                ->with(['socialLinks'])
                ->firstOrFail();

            return response()->json([
                'data' => $profileContent,
                'message' => 'Contenido de perfil obtenido exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido de perfil no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Update profile content
     */
    public function updateProfileContent(Request $request, int $profileId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_website' => 'nullable|url|max:500',
                'bio' => 'nullable|string|max:1000',
                'profession' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'color_palette' => 'nullable|array',
            ]);

            $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($profileId);

            $profileContent->update($validatedData);

            return response()->json([
                'data' => $profileContent->fresh(),
                'message' => 'Contenido de perfil actualizado exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido de perfil no encontrado',
                'status' => 404,
            ], 404);
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

    // ========================================
    // SOCIAL LINKS METHODS
    // ========================================

    /**
     * Get social links for a profile
     */
    public function getSocialLinks(int $profileId): JsonResponse
    {
        try {
            $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($profileId);

            $socialLinks = ContentSocialLink::where('dynamic_content_id', $profileContent->dynamic_content_id)
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'data' => $socialLinks,
                'message' => 'Enlaces sociales obtenidos exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Perfil no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Create social link for a profile
     */
    public function createSocialLink(Request $request, int $profileId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'platform' => 'required|string|max:50',
                'url' => 'required|url|max:500',
                'username' => 'nullable|string|max:100',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($profileId);

            $socialLink = ContentSocialLink::create([
                'dynamic_content_id' => $profileContent->dynamic_content_id,
                'platform' => $validatedData['platform'],
                'url' => $validatedData['url'],
                'username' => $validatedData['username'] ?? null,
                'sort_order' => $validatedData['sort_order'] ?? 0,
            ]);

            return response()->json([
                'data' => $socialLink,
                'message' => 'Enlace social creado exitosamente',
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
     * Delete social link
     */
    public function deleteSocialLink(int $linkId): JsonResponse
    {
        try {
            $socialLink = ContentSocialLink::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($linkId);

            $socialLink->delete();

            return response()->json([
                'data' => null,
                'message' => 'Enlace social eliminado exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Enlace social no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500,
            ], 500);
        }
    }

    // ========================================
    // BUSINESS CONTENT METHODS (Simplified)
    // ========================================

    /**
     * Create business content - simplified implementation
     */
    public function createBusinessContent(Request $request, int $dynamicContentId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'business_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:500',
                'location' => 'nullable|string|max:255',
                'business_hours' => 'nullable|string|max:255',
                'color_palette' => 'nullable|array',
            ]);

            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $businessContent = ContentBusiness::create([
                'dynamic_content_id' => $dynamicContentId,
                'name' => $validatedData['business_name'],
                'description' => $validatedData['description'] ?? null,
                'contact_email' => $validatedData['contact_email'] ?? null,
                'contact_phone' => $validatedData['contact_phone'] ?? null,
                'website' => $validatedData['website'] ?? null,
                'location' => $validatedData['location'] ?? null,
                'business_hours' => $validatedData['business_hours'] ?? null,
                'color_palette' => $validatedData['color_palette'] ?? null,
            ]);

            return response()->json([
                'data' => $businessContent,
                'message' => 'Contenido de negocio creado exitosamente',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Get business content - placeholder
     */
    public function getBusinessContent(int $dynamicContentId): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Método no implementado todavía',
            'status' => 501,
        ], 501);
    }

    /**
     * Update business content - placeholder
     */
    public function updateBusinessContent(Request $request, int $businessId): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Método no implementado todavía',
            'status' => 501,
        ], 501);
    }

    // ========================================
    // GIFT CONTENT METHODS (Simplified)
    // ========================================

    /**
     * Create gift content - simplified implementation
     */
    public function createGiftContent(Request $request, int $dynamicContentId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
                'recipient_name' => 'nullable|string|max:255',
                'sender_name' => 'nullable|string|max:255',
                'theme' => 'nullable|string|max:50',
                'special_date' => 'nullable|date',
                'delivery_date' => 'nullable|date',
            ]);

            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $giftContent = ContentGift::create([
                'dynamic_content_id' => $dynamicContentId,
                'title' => $validatedData['title'],
                'message' => $validatedData['message'],
                'recipient_name' => $validatedData['recipient_name'] ?? null,
                'sender_name' => $validatedData['sender_name'] ?? null,
                'theme' => $validatedData['theme'] ?? null,
                'special_date' => $validatedData['special_date'] ?? null,
                'delivery_date' => $validatedData['delivery_date'] ?? null,
            ]);

            return response()->json([
                'data' => $giftContent,
                'message' => 'Contenido de regalo creado exitosamente',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Get gift content - placeholder
     */
    public function getGiftContent(int $dynamicContentId): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Método no implementado todavía',
            'status' => 501,
        ], 501);
    }

    /**
     * Update gift content - placeholder
     */
    public function updateGiftContent(Request $request, int $giftId): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Método no implementado todavía',
            'status' => 501,
        ], 501);
    }

    // ========================================
    // PLACEHOLDER METHODS FOR OTHER FEATURES
    // ========================================

    public function getBusinessProducts(int $businessId): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function createBusinessProduct(Request $request, int $businessId): JsonResponse
    {
        return response()->json(['data' => null, 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function updateBusinessProduct(Request $request, int $productId): JsonResponse
    {
        return response()->json(['data' => null, 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function deleteBusinessProduct(int $productId): JsonResponse
    {
        return response()->json(['data' => null, 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function getGiftGallery(int $giftId): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function createGiftGalleryItem(Request $request, int $giftId): JsonResponse
    {
        return response()->json(['data' => null, 'message' => 'Método no implementado', 'status' => 501], 501);
    }

    public function deleteGiftGalleryItem(int $itemId): JsonResponse
    {
        return response()->json(['data' => null, 'message' => 'Método no implementado', 'status' => 501], 501);
    }
}
