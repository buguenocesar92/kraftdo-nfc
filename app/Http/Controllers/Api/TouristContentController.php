<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTouristContentRequest;
use App\Http\Requests\UpdateTouristContentRequest;
use App\Http\Resources\TouristContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\TouristContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TouristContentController extends Controller
{
    use ApiResponseTrait;

    protected TouristContentService $touristContentService;

    public function __construct(TouristContentService $touristContentService)
    {
        $this->touristContentService = $touristContentService;
    }

    /**
     * Create tourist content for a dynamic content
     */
    public function createTouristContent(CreateTouristContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            $touristContent = $this->touristContentService->createTouristContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->successResponse(
                new TouristContentResource($touristContent),
                'Contenido turístico creado exitosamente',
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
     * Get tourist content by dynamic content ID
     */
    public function getTouristContent(int $dynamicContentId): JsonResponse
    {
        try {
            $touristContent = $this->touristContentService->getTouristContent($dynamicContentId);

            return $this->successResponse(
                new TouristContentResource($touristContent),
                'Contenido turístico obtenido exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido turístico no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update tourist content
     */
    public function updateTouristContent(UpdateTouristContentRequest $request, int $touristId): JsonResponse
    {
        try {
            $touristContent = $this->touristContentService->updateTouristContent(
                $touristId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                new TouristContentResource($touristContent),
                'Contenido turístico actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido turístico no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete tourist content
     */
    public function deleteTouristContent(int $touristId): JsonResponse
    {
        try {
            $this->touristContentService->deleteTouristContent($touristId);

            return $this->successResponse(
                null,
                'Contenido turístico eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido turístico no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}