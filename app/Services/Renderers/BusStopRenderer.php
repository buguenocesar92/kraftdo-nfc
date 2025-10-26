<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusStopRenderer implements ContentRendererInterface
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

        $busStopData = $content['bus_stop'] ?? null;

        // Handle serialized data structure
        if ($busStopData && is_array($busStopData)) {
            $contentBusStop = array_values($busStopData)[0] ?? null;
        } else {
            $contentBusStop = $busStopData;
        }

        $routes = $contentBusStop['routes'] ?? [];
        $utilityPhones = $contentBusStop['utility_phones'] ?? $contentBusStop['utilityPhones'] ?? [];

        return [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'content' => $dynamicContent, // Alias for view compatibility
            'contentBusStop' => $contentBusStop,
            'routes' => $routes,
            'utilityPhones' => $utilityPhones,
        ];
    }
}
