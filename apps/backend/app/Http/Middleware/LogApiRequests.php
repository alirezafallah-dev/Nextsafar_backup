<?php

namespace App\Http\Middleware;

use App\Core\Logging\AppLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Automatically logs all API requests and responses.
 * Based on project spec Section 28.3 (Log requirements).
 */
class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Generate request ID if not present
        if (! $request->headers->has('X-Request-Id')) {
            $requestId = (string) str()->uuid();
            $request->headers->set('X-Request-Id', $requestId);
        } else {
            $requestId = $request->header('X-Request-Id');
        }

        // Continue to next middleware/controller
        $response = $next($request);

        // Log the request AFTER it's processed (so we have status code)
        if ($request->is('api/*')) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            AppLogger::api()
                ->withRequest()
                ->withUser()
                ->info('api.request.completed', [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $response->getStatusCode(),
                    'duration_ms' => $durationMs,
                    'query_params' => $this->safeQueryParams($request),
                ]);
        }

        // Attach request ID to response
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }

    /**
     * Filter sensitive query parameters before logging.
     */
    private function safeQueryParams(Request $request): array
    {
        $params = $request->query();
        $sensitive = ['token', 'password', 'secret', 'key', 'auth'];

        foreach ($sensitive as $key) {
            if (isset($params[$key])) {
                $params[$key] = '***REDACTED***';
            }
        }

        return $params;
    }
}
