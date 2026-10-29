<?php

namespace App\Modules\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * درگاه پرداخت زرین‌پال با REST API v4
 * 
 * قوانین پیاده‌سازی شده:
 * - ۱۴.۳.۱: تأیید سمت سرور
 * - ۱۴.۳.۴: تطبیق مبلغ
 * - ۱۴.۳.۵: بررسی امضا (در callback)
 */
class ZarinpalGateway implements GatewayInterface
{
    private string $merchantId;
    private string $callbackUrl;
    private bool $sandbox;
    private string $secretKey; // برای signature verification

    private string $baseUrl = 'https://api.zarinpal.com/pg/v4';
    private string $paymentUrl = 'https://www.zarinpal.com/pg/StartPay/';

    public function __construct()
    {
        $this->merchantId = config('services.zarinpal.merchant_id', '');
        $this->callbackUrl = config('services.zarinpal.callback_url', '');
        $this->sandbox = config('services.zarinpal.sandbox', false);
        $this->secretKey = config('services.zarinpal.secret_key', ''); // برای signature

        if ($this->sandbox) {
            $this->baseUrl = 'https://sandbox.zarinpal.com/pg/v4';
            $this->paymentUrl = 'https://sandbox.zarinpal.com/pg/StartPay/';
        }
    }

    public function getName(): string
    {
        return 'zarinpal';
    }

    /**
     * ایجاد تراکنش
     */
    public function purchase(float $amount, string $description, array $metadata = []): array
    {
        if (empty($this->merchantId)) {
            return [
                'success' => false,
                'error' => 'merchant_id_missing',
                'message' => 'Merchant ID زرین‌پال تنظیم نشده است',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post("{$this->baseUrl}/payment/request.json", [
                    'merchant_id' => $this->merchantId,
                    'amount' => (int) $amount,
                    'description' => $description,
                    'callback_url' => $this->callbackUrl,
                    'metadata' => $metadata,
                ]);

            $data = $response->json();

            Log::info('Zarinpal purchase request', [
                'amount' => $amount,
                'metadata' => $metadata,
                'response' => $data,
            ]);

            if ($response->failed() || ($data['data']['code'] ?? 0) !== 100) {
                Log::error('Zarinpal purchase failed', [
                    'response' => $data,
                    'amount' => $amount,
                ]);

                return [
                    'success' => false,
                    'error' => 'purchase_failed',
                    'message' => $data['errors']['message'] ?? 'ایجاد تراکنش ناموفق بود',
                    'raw' => $data,
                ];
            }

            $authority = $data['data']['authority'];

            return [
                'success' => true,
                'authority' => $authority,
                'payment_url' => $this->paymentUrl . $authority,
                'fee' => $data['data']['fee'] ?? 0,
                'fee_type' => $data['data']['fee_type'] ?? '',
            ];

        } catch (\Throwable $e) {
            Log::error('Zarinpal purchase exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'network_error',
                'message' => 'خطا در ارتباط با زرین‌پال',
            ];
        }
    }

    /**
     * تأیید پرداخت (سمت سرور - قانون ۱۴.۳.۱)
     * 
     * این متد باید از سمت سرور فراخوانی شود، نه از callback کاربر
     */
    public function verify(string $authority, float $expectedAmount): array
    {
        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post("{$this->baseUrl}/payment/verify.json", [
                    'merchant_id' => $this->merchantId,
                    'authority' => $authority,
                    'amount' => (int) $expectedAmount,
                ]);

            $data = $response->json();
            $code = $data['data']['code'] ?? 0;

            Log::info('Zarinpal verify response', [
                'authority' => $authority,
                'expected_amount' => $expectedAmount,
                'code' => $code,
                'response' => $data,
            ]);

            // کدهای موفق: 100 (پرداخت موفق) و 101 (قبلاً تایید شده)
            if (in_array($code, [100, 101])) {
                $verifiedAmount = $data['data']['amount'] ?? $expectedAmount;

                // قانون ۱۴.۳.۴: بررسی تطابق مبلغ
                $amountMatched = abs($verifiedAmount - $expectedAmount) < 1;

                if (!$amountMatched) {
                    Log::warning('Zarinpal amount mismatch', [
                        'authority' => $authority,
                        'expected' => $expectedAmount,
                        'verified' => $verifiedAmount,
                    ]);

                    return [
                        'success' => false,
                        'error' => 'amount_mismatch',
                        'message' => 'عدم تطابق مبلغ پرداختی با مبلغ سفارش',
                        'expected_amount' => $expectedAmount,
                        'verified_amount' => $verifiedAmount,
                        'reference_id' => (string) ($data['data']['ref_id'] ?? ''),
                    ];
                }

                return [
                    'success' => true,
                    'reference_id' => (string) ($data['data']['ref_id'] ?? ''),
                    'card_pan' => $data['data']['card_pan'] ?? '',
                    'card_hash' => $data['data']['card_hash'] ?? '',
                    'fee' => $data['data']['fee'] ?? 0,
                    'amount' => $verifiedAmount,
                    'message' => 'پرداخت با موفقیت تأیید شد',
                ];
            }

            Log::warning('Zarinpal verify failed', [
                'authority' => $authority,
                'code' => $code,
                'message' => $data['errors']['message'] ?? '',
            ]);

            return [
                'success' => false,
                'error' => 'verify_failed',
                'message' => $data['errors']['message'] ?? 'تایید پرداخت ناموفق بود',
                'code' => $code,
            ];

        } catch (\Throwable $e) {
            Log::error('Zarinpal verify exception', [
                'error' => $e->getMessage(),
                'authority' => $authority,
            ]);

            return [
                'success' => false,
                'error' => 'network_error',
                'message' => 'خطا در تایید پرداخت',
            ];
        }
    }

    /**
     * بررسی امضای callback (قانون ۱۴.۳.۵)
     * 
     * توجه: زرین‌پال در نسخه فعلی API از signature استفاده نمی‌کند
     * اما این متد برای سازگاری با درگاه‌های دیگر و آینده پیاده‌سازی شده
     */
    public function verifyCallbackSignature(array $callbackData, ?string $signature): bool
    {
        // زرین‌پال در حال حاضر از signature استفاده نمی‌کند
        // اما برای سازگاری با درگاه‌های دیگر، این متد پیاده‌سازی شده
        
        if (empty($this->secretKey)) {
            // اگر secret key تنظیم نشده، signature check را رد می‌کنیم
            return true;
        }

        if (empty($signature)) {
            return false;
        }

        // ساخت signature مورد انتظار
        $payload = json_encode($callbackData, JSON_UNESCAPED_UNICODE);
        $expectedSignature = hash_hmac('sha256', $payload, $this->secretKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * استعلام وضعیت تراکنش (بدون تأیید)
     */
    public function inquire(string $authority): array
    {
        try {
            // زرین‌پال API جداگانه‌ای برای inquire ندارد
            // اما می‌توانیم از verify استفاده کنیم بدون تغییر وضعیت
            // در عمل، verify فقط یک بار موفق می‌شود (code 101 برای تکرار)

            return [
                'success' => true,
                'message' => 'برای زرین‌پال از verify استفاده کنید',
                'authority' => $authority,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'inquire_failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * استرداد وجه
     * 
     * توجه: زرین‌پال API عمومی برای refund ندارد
     * این عملیات باید از پنل زرین‌پال انجام شود
     */
    public function refund(string $referenceId, float $amount): array
    {
        Log::warning('Zarinpal refund not supported via API', [
            'reference_id' => $referenceId,
            'amount' => $amount,
        ]);

        return [
            'success' => false,
            'error' => 'refund_not_supported',
            'message' => 'استرداد وجه در زرین‌پال باید از پنل مدیریت انجام شود',
            'reference_id' => $referenceId,
        ];
    }
}
