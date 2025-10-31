<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBusinessContentRequest;
use App\Http\Requests\UpdateBusinessContentRequest;
use App\Http\Resources\BusinessContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\BusinessContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BusinessContentController extends Controller
{
    use ApiResponseTrait;

    protected BusinessContentService $businessContentService;

    public function __construct(BusinessContentService $businessContentService)
    {
        $this->businessContentService = $businessContentService;
    }

    /**
     * Create business content for a dynamic content
     */
    public function createBusinessContent(CreateBusinessContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            $businessContent = $this->businessContentService->createBusinessContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->successResponse(
                new BusinessContentResource($businessContent),
                'Contenido de negocio creado exitosamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido dinámico no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Get business content by dynamic content ID
     */
    public function getBusinessContent(int $dynamicContentId): JsonResponse
    {
        try {
            $businessContent = $this->businessContentService->getBusinessContent($dynamicContentId);

            return $this->successResponse(
                new BusinessContentResource($businessContent),
                'Contenido de negocio obtenido exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de negocio no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update business content
     */
    public function updateBusinessContent(UpdateBusinessContentRequest $request, int $businessId): JsonResponse
    {
        try {
            $businessContent = $this->businessContentService->updateBusinessContent(
                $businessId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                new BusinessContentResource($businessContent),
                'Contenido de negocio actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de negocio no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete business content
     */
    public function deleteBusinessContent(int $businessId): JsonResponse
    {
        try {
            $this->businessContentService->deleteBusinessContent($businessId);

            return $this->successResponse(
                null,
                'Contenido de negocio eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de negocio no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}