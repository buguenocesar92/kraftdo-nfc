<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileRenderer implements ContentRendererInterface
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

        $contentProfile = $content['profile'];
        $contentMultimedia = $content['multimedia'] ?? null;
        $galleryImages = $contentMultimedia?->galleryImages ?? collect();
        $socialLinks = $contentProfile?->socialLinks ?? collect();

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'contentProfile' => $contentProfile,
            'contentMultimedia' => $contentMultimedia,
            'galleryImages' => $galleryImages,
            'socialLinks' => $socialLinks,
        ];
    }
}
