<?php

namespace App\Services\Renderers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ContentRendererInterface
{
    /**
     * Render content as JSON API response
     */
    public function render(Request $request, array $tokenData): JsonResponse;

    /**
     * Prepare data specific for this content type
     */
    public function prepareData(array $tokenData): array;
}
