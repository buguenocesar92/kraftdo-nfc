<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessGroupRenderer implements ContentRendererInterface
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

        $businessGroupData = $content['business_group'] ?? null;

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'content' => $dynamicContent, // Alias for compatibility
            'businessGroup' => $businessGroupData,
            'memberBusinesses' => $businessGroupData ? $businessGroupData->memberBusinesses : collect([]),
        ];
    }
}
