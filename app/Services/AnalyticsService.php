<?php

namespace App\Services;

use App\Models\NfcAnalytic;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    /**
     * Record analytics for token access asynchronously
     */
    public function recordAccess(array $tokenData): void
    {
        $token = $tokenData['token'];
        $dynamicContent = $tokenData['dynamicContent'];

        // In production environment, this should be queued
        try {
            NfcAnalytic::recordAccess(
                $dynamicContent->content_id,
                $token->content_type,
                $token->id
            );

            // Invalidate analytics cache after recording
            NfcCacheService::invalidateAnalyticsCache($dynamicContent->content_id);
        } catch (\Exception $e) {
            // Log error but don't interrupt the response
            Log::warning('Analytics recording failed', [
                'content_id' => $dynamicContent->content_id,
                'token_id' => $token->token_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get analytics data for content
     */
    public function getAnalyticsData(string $contentId): array
    {
        try {
            return NfcCacheService::getAnalyticsData($contentId);
        } catch (\Exception $e) {
            Log::warning('Failed to retrieve analytics data', [
                'content_id' => $contentId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
