<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessRenderer implements ContentRendererInterface
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

        $contentBusiness = $content['business'];
        $contentMultimedia = $content['multimedia'] ?? null;
        $galleryImages = $contentMultimedia?->galleryImages ?? collect();
        $socialLinks = $contentBusiness?->socialLinks ?? collect();

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'contentBusiness' => $contentBusiness,
            'contentMultimedia' => $contentMultimedia,
            'galleryImages' => $galleryImages,
            'socialLinks' => $socialLinks,
        ];
    }
}
