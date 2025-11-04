<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGiftContentRequest;
use App\Http\Requests\UpdateGiftContentRequest;
use App\Http\Requests\CreateGalleryItemRequest;
use App\Http\Resources\GiftContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\GiftContentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GiftContentController extends Controller
{
    use ApiResponseTrait;

    protected GiftContentService $giftContentService;

    public function __construct(GiftContentService $giftContentService)
    {
        $this->giftContentService = $giftContentService;
    }

    /**
     * Create gift content
     */
    public function store(CreateGiftContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            $giftContent = $this->giftContentService->createGiftContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->createdResponse(
                new GiftContentResource($giftContent),
                'Contenido de regalo creado exitosamente'
            );
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Contenido dinámico no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Get gift content by dynamic content ID
     */
    public function show(int $dynamicContentId): JsonResponse
    {
        try {
            $giftContent = $this->giftContentService->getGiftContent($dynamicContentId);

            return $this->successResourceResponse(
                new GiftContentResource($giftContent),
                'Contenido de regalo obtenido exitosamente'
            );
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Contenido de regalo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update gift content
     */
    public function update(UpdateGiftContentRequest $request, int $giftId): JsonResponse
    {
        try {
            $giftContent = $this->giftContentService->updateGiftContent(
                $giftId,
                $request->getUpdateData()
            );

            return $this->successResourceResponse(
                new GiftContentResource($giftContent),
                'Contenido de regalo actualizado exitosamente'
            );
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Contenido de regalo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete gift content
     */
    public function destroy(int $giftId): JsonResponse
    {
        try {
            $this->giftContentService->deleteGiftContent($giftId);

            return $this->successResponse(
                null,
                'Contenido de regalo eliminado exitosamente'
            );
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Contenido de regalo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    // ========================================
    // GIFT GALLERY METHODS
    // ========================================

    /**
     * Get gallery images for a gift
     */
    public function getGiftGallery(int $giftId): JsonResponse
    {
        try {
            $galleryImages = $this->giftContentService->getGiftGallery($giftId);

            return $this->successResponse(
                $galleryImages,
                'Galería obtenida exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Regalo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Create gallery item for a gift
     */
    public function createGiftGalleryItem(CreateGalleryItemRequest $request, int $giftId): JsonResponse
    {
        try {
            $galleryItem = $this->giftContentService->createGiftGalleryItem(
                $giftId,
                $request->validated()
            );

            return $this->successResponse(
                $galleryItem,
                'Elemento de galería creado exitosamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Regalo no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Delete gift gallery item
     */
    public function deleteGiftGalleryItem(int $itemId): JsonResponse
    {
        try {
            $this->giftContentService->deleteGiftGalleryItem($itemId);

            return $this->successResponse(
                null,
                'Elemento de galería eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Elemento de galería no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}