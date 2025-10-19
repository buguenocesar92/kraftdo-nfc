<?php

namespace App\Services;

use App\Enums\ContentType;
use App\Services\Renderers\BusinessGroupRenderer;
use App\Services\Renderers\BusinessRenderer;
use App\Services\Renderers\BusStopRenderer;
use App\Services\Renderers\ContentRendererInterface;
use App\Services\Renderers\GiftRenderer;
use App\Services\Renderers\ProfileRenderer;
use App\Services\Renderers\TouristRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TokenService
{
    public function __construct(
        private NfcCacheService $cacheService
    ) {
    }

    /**
     * Get token with content using cache
     */
    public function getTokenWithContent(string $tokenId): ?array
    {
        return NfcCacheService::getTokenWithContent($tokenId);
    }

    /**
     * Validate token and content type
     */
    public function validateToken(?array $cachedData): bool
    {
        if (! $cachedData) {
            return false;
        }

        $token = $cachedData['token'];

        // Validate content type is supported
        if (! ContentType::isSupported($token->content_type)) {
            return false;
        }

        return true;
    }

    /**
     * Render response as JSON API
     */
    public function renderResponse(Request $request, array $tokenData): JsonResponse
    {
        $token = $tokenData['token'];
        $contentType = ContentType::fromString($token->content_type);

        $renderer = $this->getRenderer($contentType);

        return $renderer->render($request, $tokenData);
    }

    /**
     * Get appropriate renderer for content type
     */
    private function getRenderer(ContentType $contentType): ContentRendererInterface
    {
        return match ($contentType) {
            ContentType::GIFT => new GiftRenderer(),
            ContentType::PROFILE => new ProfileRenderer(),
            ContentType::BUSINESS, ContentType::MENU => new BusinessRenderer(),
            ContentType::TOURIST => new TouristRenderer(),
            ContentType::BUS_STOP => new BusStopRenderer(),
            ContentType::BUSINESS_GROUP => new BusinessGroupRenderer(),
        };
    }

    /**
     * Handle not found responses as JSON
     */
    public function handleNotFound(Request $request, string $message = 'Token no encontrado'): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => $message,
            'status' => 404,
        ], 404);
    }

    /**
     * Handle inactive token responses as JSON
     * @param mixed $token
     */
    public function handleInactiveToken(Request $request, $token): JsonResponse
    {
        return response()->json([
            'data' => $token,
            'message' => 'Token inactivo',
            'status' => 200,
        ]);
    }
}
