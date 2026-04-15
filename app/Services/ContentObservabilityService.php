<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ContentObservabilityService
{
    /**
     * Log content creation with detailed metrics
     */
    public static function logContentCreation(string $contentType, int $contentId, array $data = []): void
    {
        Log::info("Content created", [
            'event' => 'content.created',
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => auth()->id(),
            'fields_count' => count($data),
            'has_coordinates' => isset($data['latitude']) && isset($data['longitude']),
            'created_at' => now()->toISOString(),
            'request_ip' => request()->ip(),
        ]);

        // Log specific metrics by content type
        match (strtoupper($contentType)) {
            'EVENT' => self::logEventMetrics('created', $contentId, $data),
            default => null,
        };
    }

    /**
     * Log content access with performance metrics
     */
    public static function logContentAccess(string $contentType, int $contentId, float $responseTime = null): void
    {
        Log::info("Content accessed", [
            'event' => 'content.accessed',
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => auth()->id(),
            'response_time_ms' => $responseTime ? round($responseTime * 1000, 2) : null,
            'accessed_at' => now()->toISOString(),
            'request_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log content update operations
     */
    public static function logContentUpdate(string $contentType, int $contentId, array $changes = []): void
    {
        Log::info("Content updated", [
            'event' => 'content.updated',
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => auth()->id(),
            'fields_changed' => array_keys($changes),
            'changes_count' => count($changes),
            'updated_at' => now()->toISOString(),
        ]);

        // Log specific metrics by content type
        match (strtoupper($contentType)) {
            'EVENT' => self::logEventMetrics('updated', $contentId, $changes),
            default => null,
        };
    }

    /**
     * Log content deletion
     */
    public static function logContentDeletion(string $contentType, int $contentId): void
    {
        Log::info("Content deleted", [
            'event' => 'content.deleted',
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => auth()->id(),
            'deleted_at' => now()->toISOString(),
        ]);
    }

    /**
     * Log API performance metrics
     */
    public static function logApiPerformance(Request $request, float $startTime, int $statusCode = 200): void
    {
        $responseTime = microtime(true) - $startTime;
        
        Log::info("API performance", [
            'event' => 'api.performance',
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'status_code' => $statusCode,
            'response_time_ms' => round($responseTime * 1000, 2),
            'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'user_id' => auth()->id(),
            'request_size_bytes' => strlen($request->getContent()),
            'timestamp' => now()->toISOString(),
        ]);

        // Log slow requests as warnings
        if ($responseTime > 2.0) {
            Log::warning("Slow API request detected", [
                'endpoint' => $request->path(),
                'response_time_ms' => round($responseTime * 1000, 2),
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Log cache operations
     */
    public static function logCacheOperation(string $operation, string $key, bool $hit = null, float $duration = null): void
    {
        Log::debug("Cache operation", [
            'event' => 'cache.operation',
            'operation' => $operation, // hit, miss, set, delete
            'cache_key' => $key,
            'hit' => $hit,
            'duration_ms' => $duration ? round($duration * 1000, 2) : null,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log security events
     */
    public static function logSecurityEvent(string $eventType, array $context = []): void
    {
        Log::warning("Security event", array_merge([
            'event' => 'security.' . $eventType,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ], $context));
    }

    /**
     * Log user behavior analytics
     */
    public static function logUserBehavior(string $action, array $context = []): void
    {
        Log::info("User behavior", array_merge([
            'event' => 'user.' . $action,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
        ], $context));
    }

    /**
     * Log event-specific metrics
     */
    private static function logEventMetrics(string $action, int $eventId, array $data): void
    {
        $metrics = [
            'event' => "content.event.{$action}",
            'event_id' => $eventId,
            'has_location' => !empty($data['event_location']),
            'has_dates' => !empty($data['event_start_date']),
            'has_organizer' => !empty($data['event_organizer']),
            'has_price' => !empty($data['ticket_price']),
            'has_registration' => !empty($data['registration_url']),
        ];

        if ($action === 'created') {
            $metrics['event_future'] = isset($data['event_start_date']) && 
                                     strtotime($data['event_start_date']) > time();
        }

        Log::info("Event content metrics", $metrics);
    }

    /**
     * Get aggregated metrics for dashboard
     */
    public static function getContentMetrics(string $period = '24h'): array
    {
        // This is a simplified version. In production, you'd integrate with
        // proper metrics collection system like Prometheus, CloudWatch, etc.
        
        return [
            'period' => $period,
            'content_operations' => [
                'created' => 0, // Would come from metrics backend
                'updated' => 0,
                'deleted' => 0,
                'accessed' => 0,
            ],
            'performance' => [
                'avg_response_time_ms' => 0,
                'slow_requests_count' => 0,
                'error_rate_percent' => 0,
            ],
            'cache' => [
                'hit_rate_percent' => 0,
                'miss_count' => 0,
                'evictions' => 0,
            ],
            'users' => [
                'active_users' => 0,
                'new_registrations' => 0,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Export logs in structured format for analysis
     */
    public static function exportLogs(string $startDate, string $endDate, array $filters = []): array
    {
        // This would integrate with log aggregation service
        return [
            'export_id' => uniqid('log_export_'),
            'period' => compact('startDate', 'endDate'),
            'filters' => $filters,
            'format' => 'json',
            'estimated_records' => 0,
            'download_url' => null, // Would be generated
            'expires_at' => now()->addHours(24)->toISOString(),
        ];
    }
}
