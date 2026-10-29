<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user has required permission(s)
 * 
 * Usage: ->middleware('permission:tours.create,users.edit')
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $user = auth()->user();

        // Super admin bypasses all permission checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasAnyPermission($permissions)) {
            throw new ApiException(
                errorCode: ApiErrorCode::FORBIDDEN,
                userMessage: 'شما دسترسی لازم برای این عملیات را ندارید.',
                httpStatus: 403,
                details: [
                    'required_permissions' => $permissions,
                ],
            );
        }

        return $next($request);
    }
}
