<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGiftContentRequest;
use App\Http\Requests\UpdateGiftContentRequest;
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
}