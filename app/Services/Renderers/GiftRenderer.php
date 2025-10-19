<?php

namespace App\Services\Renderers;

use App\Helpers\ThemeHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GiftRenderer implements ContentRendererInterface
{
    public function render(Request $request, array $tokenData): JsonResponse
    {
        $data = $this->prepareData($tokenData);

        return response()->json([
            'data' => $data,
            'message' => 'Token obtenido exitosamente',
            'status' => 200,
        ]);
    }

    public function prepareData(array $tokenData): array
    {
        $token = $tokenData['token'];
        $dynamicContent = $tokenData['dynamicContent'];
        $content = $tokenData['content'];

        $contentGift = $content['gift'];
        $contentMultimedia = $content['multimedia'] ?? null;
        $galleryImages = $contentMultimedia?->galleryImages ?? collect();

        // Get and cache theme configuration
        $theme = $contentMultimedia?->settings['theme'] ?? 'love';
        $themeConfig = Cache::remember("theme_config:{$theme}", 3600, function () use ($theme) {
            return ThemeHelper::getThemeConfig($theme);
        });

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'contentGift' => $contentGift,
            'contentMultimedia' => $contentMultimedia,
            'galleryImages' => $galleryImages,
            'theme' => $themeConfig,
        ];
    }
}
