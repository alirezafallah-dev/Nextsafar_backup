<?php

namespace App\Http\Middleware;

use App\Core\Logging\AppLogger;
use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to log admin panel access and sensitive operations
 */
class LogAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && $request->is('api/v1/admin/*')) {
            $user = auth()->user();
            
            AppLogger::admin()
                ->withUser($user->id)
                ->withRequest()
                ->info('admin.access', [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $response->getStatusCode(),
                    'role' => $user->getPrimaryRole()?->slug,
                ]);

            // Log write operations
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $resourceType = $this->extractResourceType($request->path());
                
                AdminActivityLog::log(
                    action: strtolower($request->method()),
                    resourceType: $resourceType,
                    resourceId: $this->extractResourceId($request->path()),
                    isSensitive: in_array($resourceType, ['users', 'roles', 'payments', 'refunds']),
                );
            }
        }

        return $response;
    }

    private function extractResourceType(string $path): string
    {
        $parts = explode('/', $path);
        $adminIndex = array_search('admin', $parts);
        return $parts[$adminIndex + 1] ?? 'unknown';
    }

    private function extractResourceId(string $path): ?int
    {
        $parts = explode('/', $path);
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                return (int) $part;
            }
        }
        return null;
    }
}
