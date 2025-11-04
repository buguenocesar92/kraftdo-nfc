<?php

namespace App\Http\Middleware;

use App\Services\ContentObservabilityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ObservabilityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log request start for API endpoints
        if ($request->is('api/*')) {
            $this->logRequestStart($request);
        }

        $response = $next($request);

        // Log performance metrics for API endpoints
        if ($request->is('api/*')) {
            ContentObservabilityService::logApiPerformance(
                $request,
                $startTime,
                $response->getStatusCode()
            );
        }

        // Log specific content operations
        $this->logContentOperations($request, $response);

        return $response;
    }

    /**
     * Log request start details
     */
    private function logRequestStart(Request $request): void
    {
        // Only log for authenticated API requests to avoid spam
        if (auth()->check()) {
            ContentObservabilityService::logUserBehavior('api_request', [
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'has_params' => !empty($request->query()),
                'content_length' => strlen($request->getContent()),
            ]);
        }
    }

    /**
     * Log content-specific operations based on route patterns
     */
    private function logContentOperations(Request $request, Response $response): void
    {
        $path = $request->path();
        $method = $request->method();
        $statusCode = $response->getStatusCode();

        // Only log successful operations
        if ($statusCode < 200 || $statusCode >= 300) {
            return;
        }

        // Extract content type and ID from various route patterns
        $patterns = [
            // New dedicated routes: /api/events/123, /api/tourists/456
            'api/events/(\d+)' => 'EVENT',
            'api/tourists/(\d+)' => 'TOURIST', 
            'api/bus-stops/(\d+)' => 'BUS_STOP',
            
            // Legacy routes: /api/content/event/123
            'api/content/event/(\d+)' => 'EVENT',
            'api/content/tourist/(\d+)' => 'TOURIST',
            
            // Dynamic content routes
            'api/content/dynamic/(\d+)' => 'DYNAMIC',
        ];

        foreach ($patterns as $pattern => $contentType) {
            if (preg_match("#^{$pattern}#", $path, $matches)) {
                $contentId = isset($matches[1]) ? (int) $matches[1] : null;
                
                if ($contentId) {
                    $this->logContentOperation($method, $contentType, $contentId, $request);
                }
                break;
            }
        }
    }

    /**
     * Log specific content operation
     */
    private function logContentOperation(string $method, string $contentType, int $contentId, Request $request): void
    {
        switch ($method) {
            case 'GET':
                ContentObservabilityService::logContentAccess($contentType, $contentId);
                break;
                
            case 'POST':
                // For POST, contentId might be the dynamic content ID for creation
                $data = $request->all();
                ContentObservabilityService::logContentCreation($contentType, $contentId, $data);
                break;
                
            case 'PUT':
            case 'PATCH':
                $changes = $request->all();
                ContentObservabilityService::logContentUpdate($contentType, $contentId, $changes);
                break;
                
            case 'DELETE':
                ContentObservabilityService::logContentDeletion($contentType, $contentId);
                break;
        }
    }
}