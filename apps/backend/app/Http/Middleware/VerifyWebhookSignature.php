<?php

namespace App\Http\Middleware;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verify webhook signature using HMAC-SHA256.
 * Based on security requirements for inter-service communication.
 */
class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next, string $secretKey = 'webhook'): Response
    {
        $signature = $request->header('X-Webhook-Signature');
        
        if (empty($signature)) {
            AppLogger::security()->error('webhook.missing_signature', [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            
            throw new ApiException(
                errorCode: ApiErrorCode::UNAUTHENTICATED,
                userMessage: 'امضای وب‌هوک ارسال نشده است.',
                httpStatus: 401,
            );
        }

        $secret = config("services.webhooks.{$secretKey}_secret");
        
        if (empty($secret)) {
            AppLogger::security()->critical('webhook.secret_not_configured', [
                'key' => $secretKey,
            ]);
            
            throw ApiException::serverError('وب‌هوک به درستی پیکربندی نشده است.');
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            AppLogger::security()->error('webhook.invalid_signature', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'expected' => substr($expectedSignature, 0, 8) . '...',
                'received' => substr($signature, 0, 8) . '...',
            ]);
            
            throw new ApiException(
                errorCode: ApiErrorCode::UNAUTHENTICATED,
                userMessage: 'امضای وب‌هوک معتبر نیست.',
                httpStatus: 401,
            );
        }

        AppLogger::security()->debug('webhook.signature_verified', [
            'ip' => $request->ip(),
            'path' => $request->path(),
        ]);

        return $next($request);
    }
}
