<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user has required role(s)
 * 
 * Usage: ->middleware('role:admin,editor')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $user = auth()->user();

        if (!$user->hasRole($roles)) {
            throw new ApiException(
                errorCode: ApiErrorCode::FORBIDDEN,
                userMessage: 'شما نقش لازم برای دسترسی به این بخش را ندارید.',
                httpStatus: 403,
                details: [
                    'required_roles' => $roles,
                    'user_roles' => $user->roles()->pluck('slug')->toArray(),
                ],
            );
        }

        return $next($request);
    }
}
