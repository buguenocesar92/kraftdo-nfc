<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Services\AnalyticsService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct(
        private TokenService $tokenService,
        private AnalyticsService $analyticsService
    ) {
    }

    /**
     * Show token content as JSON API
     */
    public function show(Request $request, string $tokenId): JsonResponse
    {
        // Get token with cached content
        $tokenData = $this->tokenService->getTokenWithContent($tokenId);

        if (! $this->tokenService->validateToken($tokenData)) {
            return $this->tokenService->handleNotFound($request);
        }

        $token = $tokenData['token'];

        // Validate content type is supported
        if (! ContentType::isSupported($token->content_type)) {
            return $this->tokenService->handleNotFound($request, 'Tipo de contenido no disponible');
        }

        // Handle inactive tokens
        if (! $token->is_active) {
            return $this->tokenService->handleInactiveToken($request, $token);
        }

        // Record analytics asynchronously
        $this->analyticsService->recordAccess($tokenData);

        // Render response based on content type
        return $this->tokenService->renderResponse($request, $tokenData);
    }

    /**
     * Show business products catalog as JSON API
     */
    public function showProducts(Request $request, string $tokenId): JsonResponse
    {
        // Get token data
        $tokenData = $this->tokenService->getTokenWithContent($tokenId);

        if (! $this->tokenService->validateToken($tokenData)) {
            return $this->tokenService->handleNotFound($request);
        }

        $token = $tokenData['token'];
        $content = $tokenData['content'];

        // Validate it's a business token
        if ($token->content_type !== ContentType::BUSINESS->value) {
            return $this->tokenService->handleNotFound($request, 'Esta página solo está disponible para negocios');
        }

        // Handle inactive tokens
        if (! $token->is_active) {
            return $this->tokenService->handleInactiveToken($request, $token);
        }

        $contentBusiness = $content['business'];

        // Verify catalog is enabled
        if (! $contentBusiness || ! $contentBusiness->catalog_enabled) {
            return $this->tokenService->handleNotFound($request, 'Catálogo no disponible para este negocio');
        }

        // Get products
        $products = $contentBusiness->products()->get();

        // Record analytics
        $this->analyticsService->recordAccess($tokenData);

        return response()->json([
            'data' => [
                'token' => $token,
                'dynamicContent' => $tokenData['dynamicContent'],
                'contentBusiness' => $contentBusiness,
                'products' => $products,
            ],
            'message' => 'Productos obtenidos exitosamente',
            'status' => 200,
        ]);
    }
}
