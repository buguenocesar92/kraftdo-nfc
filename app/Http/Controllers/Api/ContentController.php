<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\GiftContentController;
use App\Http\Controllers\Api\ProfileContentController;
use App\Http\Controllers\Api\BusinessContentController;
use App\Http\Controllers\Api\EventContentController;
use App\Http\Controllers\Api\TouristContentController;
use App\Http\Controllers\Api\BusStopContentController;
use App\Http\Requests\CreateGiftContentRequest;
use App\Http\Requests\UpdateGiftContentRequest;
use App\Http\Requests\CreateProfileContentRequest;
use App\Http\Requests\UpdateProfileContentRequest;
use App\Http\Requests\CreateSocialLinkRequest;
use App\Http\Requests\CreateBusinessContentRequest;
use App\Http\Requests\UpdateBusinessContentRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\CreateGalleryItemRequest;
use App\Http\Requests\CreateEventContentRequest;
use App\Http\Requests\UpdateEventContentRequest;
use App\Http\Requests\CreateTouristContentRequest;
use App\Http\Requests\UpdateTouristContentRequest;
use App\Http\Requests\CreateBusStopContentRequest;
use App\Http\Requests\UpdateBusStopContentRequest;
use App\Models\BusStop;
use App\Models\ContentBusiness;
use App\Models\ContentGalleryImage;
use App\Models\ContentEvent;
use App\Models\ContentGift;
use App\Models\ContentMultimedia;
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
    protected ProfileContentController $profileContentController;
    protected BusinessContentController $businessContentController;
    protected GiftContentController $giftContentController;
    protected EventContentController $eventContentController;
    protected TouristContentController $touristContentController;
    protected BusStopContentController $busStopContentController;

    public function __construct(
        ProfileContentController $profileContentController,
        BusinessContentController $businessContentController,
        GiftContentController $giftContentController,
        EventContentController $eventContentController,
        TouristContentController $touristContentController,
        BusStopContentController $busStopContentController
    ) {
        $this->profileContentController = $profileContentController;
        $this->businessContentController = $businessContentController;
        $this->giftContentController = $giftContentController;
        $this->eventContentController = $eventContentController;
        $this->touristContentController = $touristContentController;
        $this->busStopContentController = $busStopContentController;
    }

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
            'event' => ContentEvent::class,
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
            'event' => ['dynamicContent'],
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
                'message' => 'nullable|string|max:2000',
                'sender_name' => 'nullable|string|max:255',
                'recipient_name' => 'nullable|string|max:255',
            ],
            'event' => [
                'event_location' => 'nullable|string|max:500',
                'event_start_date' => 'nullable|date',
                'event_end_date' => 'nullable|date|after:event_start_date',
                'event_organizer' => 'nullable|string|max:255',
                'ticket_price' => 'nullable|numeric|min:0|max:999999.99',
                'ticket_currency' => 'nullable|string|in:USD,EUR,GBP,CLP,MXN,ARS',
                'registration_url' => 'nullable|url|max:500',
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
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function createProfileContent(CreateProfileContentRequest $request, int $dynamicContentId): JsonResponse
    {
        return $this->profileContentController->createProfileContent($request, $dynamicContentId);
    }

    /**
     * Get profile content by dynamic content ID
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function getProfileContent(int $dynamicContentId): JsonResponse
    {
        return $this->profileContentController->getProfileContent($dynamicContentId);
    }

    /**
     * Update profile content
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function updateProfileContent(UpdateProfileContentRequest $request, int $profileId): JsonResponse
    {
        return $this->profileContentController->updateProfileContent($request, $profileId);
    }

    // ========================================
    // SOCIAL LINKS METHODS
    // ========================================

    /**
     * Get social links for a profile
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function getSocialLinks(int $profileId): JsonResponse
    {
        return $this->profileContentController->getSocialLinks($profileId);
    }

    /**
     * Create social link for a profile
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function createSocialLink(CreateSocialLinkRequest $request, int $profileId): JsonResponse
    {
        return $this->profileContentController->createSocialLink($request, $profileId);
    }

    /**
     * Delete social link
     * @deprecated Use ProfileContentController directly for new implementations
     */
    public function deleteSocialLink(int $linkId): JsonResponse
    {
        return $this->profileContentController->deleteSocialLink($linkId);
    }

    // ========================================
    // BUSINESS CONTENT METHODS (Simplified)
    // ========================================

    /**
     * Create business content
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function createBusinessContent(CreateBusinessContentRequest $request, int $dynamicContentId): JsonResponse
    {
        return $this->businessContentController->createBusinessContent($request, $dynamicContentId);
    }

    /**
     * Get business content
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function getBusinessContent(int $dynamicContentId): JsonResponse
    {
        return $this->businessContentController->getBusinessContent($dynamicContentId);
    }

    /**
     * Update business content
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function updateBusinessContent(UpdateBusinessContentRequest $request, int $businessId): JsonResponse
    {
        return $this->businessContentController->updateBusinessContent($request, $businessId);
    }

    // ========================================
    // GIFT CONTENT METHODS (Delegated to GiftContentController)
    // ========================================

    /**
     * Create gift content - delegates to GiftContentController
     * @deprecated Use GiftContentController directly
     */
    public function createGiftContent(CreateGiftContentRequest $request, int $dynamicContentId): JsonResponse
    {
        $giftController = app(GiftContentController::class);
        
        return $giftController->store($request, $dynamicContentId);
    }

    /**
     * Get gift content by dynamic content ID - delegates to GiftContentController
     * @deprecated Use GiftContentController directly
     */
    public function getGiftContent(int $dynamicContentId): JsonResponse
    {
        $giftController = app(GiftContentController::class);
        
        return $giftController->show($dynamicContentId);
    }

    /**
     * Update gift content - delegates to GiftContentController
     * @deprecated Use GiftContentController directly
     */
    public function updateGiftContent(UpdateGiftContentRequest $request, int $giftId): JsonResponse
    {
        $giftController = app(GiftContentController::class);
        
        return $giftController->update($request, $giftId);
    }

    // ========================================
    // PLACEHOLDER METHODS FOR OTHER FEATURES
    // ========================================

    /**
     * Get business products
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function getBusinessProducts(int $businessId): JsonResponse
    {
        return $this->businessContentController->getBusinessProducts($businessId);
    }

    /**
     * Create business product
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function createBusinessProduct(CreateProductRequest $request, int $businessId): JsonResponse
    {
        return $this->businessContentController->createBusinessProduct($request, $businessId);
    }

    /**
     * Update business product
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function updateBusinessProduct(UpdateProductRequest $request, int $productId): JsonResponse
    {
        return $this->businessContentController->updateBusinessProduct($request, $productId);
    }

    /**
     * Delete business product
     * @deprecated Use BusinessContentController directly for new implementations
     */
    public function deleteBusinessProduct(int $productId): JsonResponse
    {
        return $this->businessContentController->deleteBusinessProduct($productId);
    }

    /**
     * Get gift gallery
     * @deprecated Use GiftContentController directly for new implementations
     */
    public function getGiftGallery(int $giftId): JsonResponse
    {
        return $this->giftContentController->getGiftGallery($giftId);
    }

    /**
     * Create gift gallery item
     * @deprecated Use GiftContentController directly for new implementations
     */
    public function createGiftGalleryItem(CreateGalleryItemRequest $request, int $giftId): JsonResponse
    {
        return $this->giftContentController->createGiftGalleryItem($request, $giftId);
    }

    /**
     * Delete gift gallery item
     * @deprecated Use GiftContentController directly for new implementations
     */
    public function deleteGiftGalleryItem(int $itemId): JsonResponse
    {
        return $this->giftContentController->deleteGiftGalleryItem($itemId);
    }

    // ========================================
    // EVENT CONTENT METHODS (Delegated to EventContentController)
    // ========================================

    /**
     * Create event content - delegates to EventContentController
     * @deprecated Use EventContentController directly
     */
    public function createEventContent(CreateEventContentRequest $request, int $dynamicContentId): JsonResponse
    {
        return $this->eventContentController->createEventContent($request, $dynamicContentId);
    }

    /**
     * Get event content by dynamic content ID - delegates to EventContentController
     * @deprecated Use EventContentController directly
     */
    public function getEventContent(int $dynamicContentId): JsonResponse
    {
        return $this->eventContentController->getEventContent($dynamicContentId);
    }

    /**
     * Update event content - delegates to EventContentController
     * @deprecated Use EventContentController directly
     */
    public function updateEventContent(UpdateEventContentRequest $request, int $eventId): JsonResponse
    {
        return $this->eventContentController->updateEventContent($request, $eventId);
    }

    /**
     * Delete event content - delegates to EventContentController
     * @deprecated Use EventContentController directly
     */
    public function deleteEventContent(int $eventId): JsonResponse
    {
        return $this->eventContentController->deleteEventContent($eventId);
    }

    // ========================================
    // TOURIST CONTENT METHODS (Delegated to TouristContentController)
    // ========================================

    /**
     * Create tourist content - delegates to TouristContentController
     * @deprecated Use TouristContentController directly
     */
    public function createTouristContent(CreateTouristContentRequest $request, int $dynamicContentId): JsonResponse
    {
        return $this->touristContentController->createTouristContent($request, $dynamicContentId);
    }

    /**
     * Get tourist content by dynamic content ID - delegates to TouristContentController
     * @deprecated Use TouristContentController directly
     */
    public function getTouristContent(int $dynamicContentId): JsonResponse
    {
        return $this->touristContentController->getTouristContent($dynamicContentId);
    }

    /**
     * Update tourist content - delegates to TouristContentController
     * @deprecated Use TouristContentController directly
     */
    public function updateTouristContent(UpdateTouristContentRequest $request, int $touristId): JsonResponse
    {
        return $this->touristContentController->updateTouristContent($request, $touristId);
    }

    /**
     * Delete tourist content - delegates to TouristContentController
     * @deprecated Use TouristContentController directly
     */
    public function deleteTouristContent(int $touristId): JsonResponse
    {
        return $this->touristContentController->deleteTouristContent($touristId);
    }

    // ========================================
    // BUS STOP CONTENT METHODS (Delegated to BusStopContentController)
    // ========================================

    /**
     * Create bus stop content - delegates to BusStopContentController
     * @deprecated Use BusStopContentController directly
     */
    public function createBusStopContent(CreateBusStopContentRequest $request, int $dynamicContentId): JsonResponse
    {
        return $this->busStopContentController->createBusStopContent($request, $dynamicContentId);
    }

    /**
     * Get bus stop content by dynamic content ID - delegates to BusStopContentController
     * @deprecated Use BusStopContentController directly
     */
    public function getBusStopContent(int $dynamicContentId): JsonResponse
    {
        return $this->busStopContentController->getBusStopContent($dynamicContentId);
    }

    /**
     * Update bus stop content - delegates to BusStopContentController
     * @deprecated Use BusStopContentController directly
     */
    public function updateBusStopContent(UpdateBusStopContentRequest $request, int $busStopId): JsonResponse
    {
        return $this->busStopContentController->updateBusStopContent($request, $busStopId);
    }

    /**
     * Delete bus stop content - delegates to BusStopContentController
     * @deprecated Use BusStopContentController directly
     */
    public function deleteBusStopContent(int $busStopId): JsonResponse
    {
        return $this->busStopContentController->deleteBusStopContent($busStopId);
    }

    /**
     * Delete gallery image by ID
     */
    public function deleteGalleryImage(int $imageId): JsonResponse
    {
        try {
            $galleryImage = ContentGalleryImage::whereHas('contentMultimedia.dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($imageId);

            // Delete the file if it exists
            if ($galleryImage->image_path && file_exists(storage_path('app/public/' . $galleryImage->image_path))) {
                unlink(storage_path('app/public/' . $galleryImage->image_path));
            }

            $galleryImage->delete();

            return response()->json([
                'data' => null,
                'message' => 'Imagen eliminada exitosamente',
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Imagen no encontrada',
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
     * Get multimedia content by dynamic content ID
     */
    public function getMultimediaContent(int $dynamicContentId): JsonResponse
    {
        try {
            \Log::info('🔍 getMultimediaContent called', [
                'dynamicContentId' => $dynamicContentId,
                'userId' => Auth::id(),
                'user' => Auth::user()?->email
            ]);
            
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Get or create multimedia content
            $multimedia = ContentMultimedia::with(['galleryImages' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])->firstOrCreate(
                ['dynamic_content_id' => $dynamicContentId],
                ['settings' => []]
            );

            return response()->json([
                'data' => $multimedia,
                'message' => 'Contenido multimedia obtenido exitosamente',
                'status' => 200
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido dinámico no encontrado',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error interno del servidor',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Subir archivo de audio a contenido multimedia
     */
    public function uploadAudioFile(Request $request, int $multimediaId): JsonResponse
    {
        try {
            $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,m4a,aac,ogg|max:10240', // 10MB max
                'type' => 'required|string|in:file_upload'
            ]);

            // Verificar que el multimedia existe y pertenece al usuario
            $multimedia = ContentMultimedia::where('id', $multimediaId)
                ->whereHas('dynamicContent', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $audioFile = $request->file('audio');
            $fileName = time() . '_' . $audioFile->getClientOriginalName();
            $filePath = $audioFile->storeAs('audio', $fileName, 'public');

            // Actualizar el registro de multimedia
            $multimedia->update([
                'audio_file' => $filePath,
                'audio_type' => 'file_upload',
                'audio_url' => asset('storage/' . $filePath)
            ]);

            return response()->json([
                'data' => [
                    'audio_url' => asset('storage/' . $filePath),
                    'audio_file' => $filePath,
                    'audio_type' => 'file_upload'
                ],
                'message' => 'Archivo de audio subido exitosamente',
                'status' => 200
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido multimedia no encontrado',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error al subir archivo de audio: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Subir archivo de video a contenido multimedia
     */
    public function uploadVideoFile(Request $request, int $multimediaId): JsonResponse
    {
        try {
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,webm,mkv|max:51200', // 50MB max
                'type' => 'required|string|in:file_upload'
            ]);

            // Verificar que el multimedia existe y pertenece al usuario
            $multimedia = ContentMultimedia::where('id', $multimediaId)
                ->whereHas('dynamicContent', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $videoFile = $request->file('video');
            $fileName = time() . '_' . $videoFile->getClientOriginalName();
            $filePath = $videoFile->storeAs('videos', $fileName, 'public');

            // Actualizar el registro de multimedia
            $multimedia->update([
                'video_file' => $filePath,
                'video_type' => 'file_upload',
                'video_url' => asset('storage/' . $filePath)
            ]);

            return response()->json([
                'data' => [
                    'video_url' => asset('storage/' . $filePath),
                    'video_file' => $filePath,
                    'video_type' => 'file_upload'
                ],
                'message' => 'Archivo de video subido exitosamente',
                'status' => 200
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido multimedia no encontrado',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error al subir archivo de video: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Subir imagen de perfil
     */
    public function uploadProfileImage(Request $request, int $profileId): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'type' => 'required|string|in:file_upload'
            ]);

            // Verificar que el perfil existe y pertenece al usuario
            $profile = ContentProfile::where('id', $profileId)
                ->whereHas('dynamicContent', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $imageFile = $request->file('image');
            $fileName = time() . '_' . $imageFile->getClientOriginalName();
            $filePath = $imageFile->storeAs('profiles', $fileName, 'public');

            // Get or create the ContentMultimedia for this profile
            $multimedia = ContentMultimedia::firstOrCreate(
                ['dynamic_content_id' => $profile->dynamic_content_id],
                ['settings' => []]
            );

            // Update the multimedia settings to include the profile image
            $settings = $multimedia->settings ?? [];
            $settings['profile_image'] = $filePath; // Store relative path, not full URL
            
            $multimedia->update([
                'settings' => $settings
            ]);

            return response()->json([
                'data' => [
                    'url' => asset('storage/' . $filePath),
                    'file_path' => $filePath
                ],
                'message' => 'Imagen de perfil subida exitosamente',
                'status' => 200
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Perfil no encontrado',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error al subir imagen de perfil: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Subir imagen a galería de multimedia
     */
    public function uploadGalleryImage(Request $request, int $multimediaId): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'alt_text' => 'nullable|string|max:255',
                'type' => 'required|string|in:file_upload'
            ]);

            // Verificar que el multimedia existe y pertenece al usuario
            $multimedia = ContentMultimedia::where('id', $multimediaId)
                ->whereHas('dynamicContent', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $imageFile = $request->file('image');
            $fileName = time() . '_' . $imageFile->getClientOriginalName();
            $filePath = $imageFile->storeAs('gallery', $fileName, 'public');

            // Crear registro en galería de imágenes
            $galleryImage = ContentGalleryImage::create([
                'content_multimedia_id' => $multimediaId,
                'image_path' => $filePath,
                'image_url' => asset('storage/' . $filePath),
                'alt_text' => $request->alt_text ?? 'Imagen de galería',
                'type' => 'upload'
            ]);

            return response()->json([
                'data' => [
                    'id' => $galleryImage->id,
                    'image_url' => asset('storage/' . $filePath),
                    'image_path' => $filePath,
                    'alt_text' => $galleryImage->alt_text,
                    'type' => 'upload'
                ],
                'message' => 'Imagen subida a galería exitosamente',
                'status' => 200
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'data' => null,
                'message' => 'Contenido multimedia no encontrado',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Error al subir imagen a galería: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
