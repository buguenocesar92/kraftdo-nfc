<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBusStopContentRequest;
use App\Http\Requests\UpdateBusStopContentRequest;
use App\Http\Resources\BusStopContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\BusStopContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BusStopContentController extends Controller
{
    use ApiResponseTrait;

    protected BusStopContentService $busStopContentService;

    public function __construct(BusStopContentService $busStopContentService)
    {
        $this->busStopContentService = $busStopContentService;
    }

    /**
     * Create bus stop content for a dynamic content
     */
    public function createBusStopContent(CreateBusStopContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            $busStopContent = $this->busStopContentService->createBusStopContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->successResponse(
                new BusStopContentResource($busStopContent),
                'Contenido de parada de bus creado exitosamente',
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
     * Get bus stop content by dynamic content ID
     */
    public function getBusStopContent(int $dynamicContentId): JsonResponse
    {
        try {
            $busStopContent = $this->busStopContentService->getBusStopContent($dynamicContentId);

            return $this->successResponse(
                new BusStopContentResource($busStopContent),
                'Contenido de parada de bus obtenido exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de parada de bus no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update bus stop content
     */
    public function updateBusStopContent(UpdateBusStopContentRequest $request, int $busStopId): JsonResponse
    {
        try {
            $busStopContent = $this->busStopContentService->updateBusStopContent(
                $busStopId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                new BusStopContentResource($busStopContent),
                'Contenido de parada de bus actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de parada de bus no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete bus stop content
     */
    public function deleteBusStopContent(int $busStopId): JsonResponse
    {
        try {
            $this->busStopContentService->deleteBusStopContent($busStopId);

            return $this->successResponse(
                null,
                'Contenido de parada de bus eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de parada de bus no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}