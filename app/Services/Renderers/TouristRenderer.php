<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TouristRenderer implements ContentRendererInterface
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

        $contentTourist = $content['tourist'];
        $nearbySpots = $contentTourist?->activeNearbySpots ?? collect();

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'content' => $dynamicContent, // For compatibility with tourist view
            'tourist' => $contentTourist,
            'mapData' => $contentTourist?->getMapData() ?? [],
            'nearbySpots' => $nearbySpots,
        ];
    }
}
