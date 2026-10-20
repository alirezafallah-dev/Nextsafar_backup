<?php

namespace App\Modules\Notification\Gateways\Sms;

use App\Core\Logging\AppLogger;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * KavenegarGateway - درگاه پیامک کاوه‌نگار (Production)
 * 
 * مستندات: https://kavenegar.com/rest.html
 * 
 * TODO: در production با API key واقعی تست شود
 */
class KavenegarGateway implements SmsGatewayInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.kavenegar.com/v1';
    private ?string $sender;

    public function __construct()
    {
        $this->apiKey = config('services.kavenegar.api_key', '');
        $this->sender = config('services.kavenegar.sender');
    }

    public function getName(): string
    {
        return 'kavenegar';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function send(string $to, string $message, array $options = []): array
    {
        if (!$this->isAvailable()) {
            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Kavenegar API key not configured',
                'raw_response' => null,
            ];
        }

        try {
            $endpoint = "{$this->baseUrl}/{$this->apiKey}/sms/send.json";

            $payload = [
                'receptor' => $to,
                'message' => $message,
                'sender' => $options['sender'] ?? $this->sender,
                'date' => $options['send_at'] ?? null,
                'type' => $options['type'] ?? 1, // 1 = flash, 0 = normal
            ];

            // حذف فیلدهای خالی
            $payload = array_filter($payload);

            $startTime = microtime(true);

            $response = Http::timeout(30)
                ->acceptJson()
                ->post($endpoint, $payload);

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            $data = $response->json();

            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->info('kavenegar.response', [
                'to' => $to,
                'status_code' => $response->status(),
                'duration_ms' => $durationMs,
                'response' => $data,
            ]);

            // بررسی موفقیت
            if ($response->successful() && ($data['return']['status'] ?? 0) === 200) {
                $entries = $data['entries'] ?? [];
                $messageId = $entries[0]['messageid'] ?? null;

                return [
                    'success' => true,
                    'message_id' => (string) $messageId,
                    'error' => null,
                    'raw_response' => $data,
                    'cost' => $entries[0]['cost'] ?? null,
                ];
            }

            // خطا از کاوه‌نگار
            $errorMessage = $data['return']['message'] ?? 'Unknown error from Kavenegar';

            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('kavenegar.send_failed', [
                'to' => $to,
                'error' => $errorMessage,
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $errorMessage,
                'raw_response' => $data,
            ];

        } catch (Throwable $e) {
            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('kavenegar.exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Network error: ' . $e->getMessage(),
                'raw_response' => null,
            ];
        }
    }

    public function getStatus(string $messageId): array
    {
        if (!$this->isAvailable()) {
            return ['status' => 'unknown', 'error' => 'API key not configured'];
        }

        try {
            $endpoint = "{$this->baseUrl}/{$this->apiKey}/sms/status.json";

            $response = Http::timeout(15)
                ->acceptJson()
                ->get($endpoint, ['messageid' => $messageId]);

            $data = $response->json();

            if ($response->successful() && !empty($data['entries'])) {
                $entry = $data['entries'][0];
                // تبدیل status code کاوه‌نگار به status داخلی
                $status = $this->mapStatus($entry['status'] ?? 0);

                return [
                    'status' => $status,
                    'message_id' => $messageId,
                    'raw_status' => $entry['status'] ?? null,
                    'delivered_at' => $entry['date'] ?? null,
                ];
            }

            return ['status' => 'unknown', 'message_id' => $messageId];

        } catch (Throwable $e) {
            return ['status' => 'unknown', 'error' => $e->getMessage()];
        }
    }

    public function getBalance(): array
    {
        if (!$this->isAvailable()) {
            return ['balance' => 0, 'error' => 'API key not configured'];
        }

        try {
            $endpoint = "{$this->baseUrl}/{$this->apiKey}/account/info.json";

            $response = Http::timeout(15)
                ->acceptJson()
                ->get($endpoint);

            $data = $response->json();

            if ($response->successful() && isset($data['entries'])) {
                return [
                    'balance' => (int) ($data['entries']['remaincredit'] ?? 0),
                    'currency' => 'IRR',
                    'expiration' => $data['entries']['expiredate'] ?? null,
                ];
            }

            return ['balance' => 0, 'error' => 'Invalid response'];

        } catch (Throwable $e) {
            return ['balance' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * تبدیل status code کاوه‌نگار به status داخلی
     */
    private function mapStatus(int $statusCode): string
    {
        // مستندات: https://kavenegar.com/rest.html#sms-status
        return match ($statusCode) {
            1 => 'queued',           // در صف ارسال
            2 => 'queued',           // در صف ارسال به مخابرات
            4 => 'sent',             // ارسال شده
            5 => 'sent',             // ارسال شده به مخابرات
            6 => 'failed',           // خطا در ارسال
            10 => 'delivered',       // تحویل داده شده
            11 => 'delivered',       // تحویل داده شده
            13 => 'failed',          // لغو شده
            14 => 'failed',          // مسدود
            100 => 'unknown',        // نامشخص
            default => 'unknown',
        };
    }
}
