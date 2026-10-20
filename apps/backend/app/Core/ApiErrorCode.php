<?php

namespace App\Core;

/**
 * Standard API Error Codes based on project specification (Section 24.3)
 * Each code is a unique string to be machine-readable by frontend.
 */
enum ApiErrorCode: string
{
    // 1. Validation Errors (422)
    case VALIDATION_FAILED = 'VALIDATION_FAILED';
    case INVALID_INPUT = 'INVALID_INPUT';
    case INVALID_PHONE = 'INVALID_PHONE';
    case INVALID_EMAIL = 'INVALID_EMAIL';

    // 2. Authentication Errors (401)
    case UNAUTHENTICATED = 'UNAUTHENTICATED';
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    case TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    case TOKEN_INVALID = 'TOKEN_INVALID';
    case OTP_EXPIRED = 'OTP_EXPIRED';
    case OTP_RATE_LIMITED = 'OTP_RATE_LIMITED';

    // 3. Authorization Errors (403)
    case FORBIDDEN = 'FORBIDDEN';
    case INSUFFICIENT_PERMISSIONS = 'INSUFFICIENT_PERMISSIONS';

    // 4. Not Found (404)
    case NOT_FOUND = 'NOT_FOUND';
    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';

    // 5. Price Change (409)
    case PRICE_CHANGED = 'PRICE_CHANGED';
    case PRICE_EXPIRED = 'PRICE_EXPIRED';

    // 6. Availability (409)
    case OUT_OF_STOCK = 'OUT_OF_STOCK';
    case CAPACITY_FULL = 'CAPACITY_FULL';
    case NO_AVAILABILITY = 'NO_AVAILABILITY';

    // 7. Provider Errors (502)
    case PROVIDER_ERROR = 'PROVIDER_ERROR';
    case PROVIDER_TIMEOUT = 'PROVIDER_TIMEOUT';
    case PROVIDER_UNAVAILABLE = 'PROVIDER_UNAVAILABLE';

    // 8. Payment Gateway Errors (502)
    case GATEWAY_ERROR = 'GATEWAY_ERROR';
    case GATEWAY_TIMEOUT = 'GATEWAY_TIMEOUT';
    case PAYMENT_FAILED = 'PAYMENT_FAILED';
    case PAYMENT_VERIFICATION_FAILED = 'PAYMENT_VERIFICATION_FAILED';
    case DUPLICATE_PAYMENT = 'DUPLICATE_PAYMENT';

    // 9. Server Errors (500)
    case SERVER_ERROR = 'SERVER_ERROR';
    case DATABASE_ERROR = 'DATABASE_ERROR';
    case QUEUE_ERROR = 'QUEUE_ERROR';

    // 10. Rate Limiting (429)
    case RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';
    case TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';

    // Business specific
    case BOOKING_EXPIRED = 'BOOKING_EXPIRED';
    case BOOKING_ALREADY_CANCELLED = 'BOOKING_ALREADY_CANCELLED';
    case DOCUMENT_UPLOAD_FAILED = 'DOCUMENT_UPLOAD_FAILED';
    case SYNC_FAILED = 'SYNC_FAILED';
}