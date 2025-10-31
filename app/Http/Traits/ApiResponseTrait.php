<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponseTrait
{
    /**
     * Success response with data
     */
    protected function successResponse($data = null, string $message = 'Operación exitosa', int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * Success response with resource
     */
    protected function successResourceResponse(JsonResource $resource, string $message = 'Operación exitosa', int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $resource,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * Created response
     */
    protected function createdResponse($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Error interno del servidor', int $status = 500, array $errors = []): JsonResponse
    {
        $response = [
            'data' => null,
            'message' => $message,
            'status' => $status,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse(array $errors, string $message = 'Datos de validación incorrectos'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'No autorizado'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}