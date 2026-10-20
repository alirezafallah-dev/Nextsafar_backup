<?php

namespace App\Exceptions;

use App\Core\ApiErrorCode;
use Exception;
use Throwable;

/**
 * Standard API Exception.
 * Based on project spec Section 24.2 (Error Response).
 *
 * Structure:
 * {
 *   "success": false,
 *   "error": {
 *     "code": "VALIDATION_FAILED",
 *     "message": "User-friendly message",
 *     "details": {...},        // optional validation details
 *     "technical": "..."       // optional technical message for logs
 *   }
 * }
 */
class ApiException extends Exception
{
    public function __construct(
        protected ApiErrorCode $errorCode,
        string $userMessage = '',
        protected ?string $technicalMessage = null,
        protected array $details = [],
        protected int $httpStatus = 400,
        ?Throwable $previous = null,
    ) {
        parent::__construct($userMessage ?: $errorCode->value, 0, $previous);
    }

    public function getErrorCode(): ApiErrorCode
    {
        return $this->errorCode;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getUserMessage(): string
    {
        return $this->message;
    }

    public function getTechnicalMessage(): ?string
    {
        return $this->technicalMessage;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Convert to the standard error response array.
     */
    public function toArray(bool $includeTechnical = false): array
    {
        $error = [
            'code' => $this->errorCode->value,
            'message' => $this->getUserMessage(),
        ];

        if (!empty($this->details)) {
            $error['details'] = $this->details;
        }

        if ($includeTechnical && $this->technicalMessage !== null) {
            $error['technical'] = $this->technicalMessage;
        }

        return [
            'success' => false,
            'error' => $error,
        ];
    }

    // ============================================================
    // Factory methods for common scenarios
    // ============================================================

    public static function validationFailed(array $details = [], string $message = 'داده‌های ارسالی معتبر نیستند.'): self
    {
        return new self(
            errorCode: ApiErrorCode::VALIDATION_FAILED,
            userMessage: $message,
            details: $details,
            httpStatus: 422,
        );
    }

    public static function notFound(string $resource = 'منبع', ?string $id = null): self
    {
        $message = $id ? "{$resource} با شناسه {$id} یافت نشد." : "{$resource} مورد نظر یافت نشد.";
        return new self(
            errorCode: ApiErrorCode::NOT_FOUND,
            userMessage: $message,
            httpStatus: 404,
        );
    }

    public static function unauthenticated(string $message = 'ابتدا وارد حساب کاربری خود شوید.'): self
    {
        return new self(
            errorCode: ApiErrorCode::UNAUTHENTICATED,
            userMessage: $message,
            httpStatus: 401,
        );
    }

    public static function forbidden(string $message = 'شما دسترسی لازم برای این عملیات را ندارید.'): self
    {
        return new self(
            errorCode: ApiErrorCode::FORBIDDEN,
            userMessage: $message,
            httpStatus: 403,
        );
    }

    public static function rateLimited(int $retryAfterSeconds = 60): self
    {
        return new self(
            errorCode: ApiErrorCode::RATE_LIMIT_EXCEEDED,
            userMessage: 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند لحظه صبر کنید.',
            httpStatus: 429,
            details: ['retry_after' => $retryAfterSeconds],
        );
    }

    public static function serverError(string $userMessage = 'خطای داخلی سرور. لطفاً بعداً تلاش کنید.', ?Throwable $previous = null): self
    {
        return new self(
            errorCode: ApiErrorCode::SERVER_ERROR,
            userMessage: $userMessage,
            technicalMessage: $previous?->getMessage(),
            httpStatus: 500,
            previous: $previous,
        );
    }

    public static function priceChanged(array $priceInfo = []): self
    {
        return new self(
            errorCode: ApiErrorCode::PRICE_CHANGED,
            userMessage: 'قیمت این مورد تغییر کرده است. لطفاً قیمت جدید را بررسی و تأیید کنید.',
            details: $priceInfo,
            httpStatus: 409,
        );
    }

    public static function providerError(string $providerName, ?Throwable $previous = null): self
    {
        return new self(
            errorCode: ApiErrorCode::PROVIDER_ERROR,
            userMessage: 'در حال حاضر ارتباط با تأمین‌کننده برقرار نیست. لطفاً بعداً تلاش کنید.',
            technicalMessage: "Provider '{$providerName}' error: " . ($previous?->getMessage() ?? 'unknown'),
            httpStatus: 502,
            previous: $previous,
        );
    }

    public static function gatewayError(?Throwable $previous = null): self
    {
        return new self(
            errorCode: ApiErrorCode::GATEWAY_ERROR,
            userMessage: 'خطا در ارتباط با درگاه پرداخت. مبلغی کسر نشده است.',
            technicalMessage: $previous?->getMessage(),
            httpStatus: 502,
            previous: $previous,
        );
    }
}