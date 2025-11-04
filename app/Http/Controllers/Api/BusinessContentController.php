<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBusinessContentRequest;
use App\Http\Requests\UpdateBusinessContentRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
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

    // ========================================
    // BUSINESS PRODUCTS METHODS
    // ========================================

    /**
     * Get products for a business
     */
    public function getBusinessProducts(int $businessId): JsonResponse
    {
        try {
            $products = $this->businessContentService->getBusinessProducts($businessId);

            return $this->successResponse(
                $products,
                'Productos obtenidos exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Negocio no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Create product for a business
     */
    public function createBusinessProduct(CreateProductRequest $request, int $businessId): JsonResponse
    {
        try {
            $product = $this->businessContentService->createBusinessProduct(
                $businessId,
                $request->validated()
            );

            return $this->successResponse(
                $product,
                'Producto creado exitosamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Negocio no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Update business product
     */
    public function updateBusinessProduct(UpdateProductRequest $request, int $productId): JsonResponse
    {
        try {
            $product = $this->businessContentService->updateBusinessProduct(
                $productId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                $product,
                'Producto actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Producto no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete business product
     */
    public function deleteBusinessProduct(int $productId): JsonResponse
    {
        try {
            $this->businessContentService->deleteBusinessProduct($productId);

            return $this->successResponse(
                null,
                'Producto eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Producto no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}