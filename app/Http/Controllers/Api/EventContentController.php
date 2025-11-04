<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventContentRequest;
use App\Http\Requests\UpdateEventContentRequest;
use App\Http\Resources\EventContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ContentEvent;
use App\Services\EventContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class EventContentController extends Controller
{
    use ApiResponseTrait;

    protected EventContentService $eventContentService;

    public function __construct(EventContentService $eventContentService)
    {
        $this->eventContentService = $eventContentService;
    }

    /**
     * Create event content for a dynamic content
     */
    public function createEventContent(CreateEventContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            // Check authorization to create events
            $this->authorize('create', ContentEvent::class);

            $eventContent = $this->eventContentService->createEventContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->successResponse(
                new EventContentResource($eventContent),
                'Contenido de evento creado exitosamente',
                201
            );
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('No tienes permisos para crear eventos');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido dinámico no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Get event content by dynamic content ID
     */
    public function getEventContent(int $dynamicContentId): JsonResponse
    {
        try {
            $eventContent = $this->eventContentService->getEventContent($dynamicContentId);

            // Check authorization to view this specific event
            $this->authorize('view', $eventContent);

            return $this->successResponse(
                new EventContentResource($eventContent),
                'Contenido de evento obtenido exitosamente'
            );
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('No tienes permisos para ver este evento');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de evento no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update event content
     */
    public function updateEventContent(UpdateEventContentRequest $request, int $eventId): JsonResponse
    {
        try {
            // Get event first to check authorization
            $eventContent = ContentEvent::findOrFail($eventId);
            $this->authorize('update', $eventContent);

            $eventContent = $this->eventContentService->updateEventContent(
                $eventId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                new EventContentResource($eventContent),
                'Contenido de evento actualizado exitosamente'
            );
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('No tienes permisos para actualizar este evento');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de evento no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete event content
     */
    public function deleteEventContent(int $eventId): JsonResponse
    {
        try {
            // Get event first to check authorization
            $eventContent = ContentEvent::findOrFail($eventId);
            $this->authorize('delete', $eventContent);

            $this->eventContentService->deleteEventContent($eventId);

            return $this->successResponse(
                null,
                'Contenido de evento eliminado exitosamente'
            );
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('No tienes permisos para eliminar este evento');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de evento no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}