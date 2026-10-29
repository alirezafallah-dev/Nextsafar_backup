<?php

namespace App\Modules\Notification\Gateways\Sms;

use App\Core\Logging\AppLogger;
use Illuminate\Support\Str;

/**
 * LogSmsGateway - فقط log می‌کند (برای development و تست)
 * 
 * این gateway در محیط development استفاده می‌شود و پیام‌ها را:
 * 1. در لاگ ذخیره می‌کند
 * 2. در دیتابیس به عنوان notification log ثبت می‌کند
 * 3. هیچ پیام واقعی ارسال نمی‌کند
 */
class LogSmsGateway implements SmsGatewayInterface
{
    public function getName(): string
    {
        return 'log';
    }

    public function isAvailable(): bool
    {
        return true; // همیشه در دسترس
    }

    public function send(string $to, string $message, array $options = []): array
    {
        $messageId = 'LOG-' . strtoupper(Str::random(16));

        // لاگ کردن پیام
        AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->info('sms.sent_via_log', [
            'message_id' => $messageId,
            'to' => $to,
            'message' => $message,
            'options' => $options,
            'gateway' => $this->getName(),
        ]);

        // اگر در حالت debug هستیم، پیام را در console هم چاپ کن
        if (app()->environment('local', 'testing')) {
            info("📨 [SMS LOG] To: {$to} | Message: {$message}");
        }

        return [
            'success' => true,
            'message_id' => $messageId,
            'error' => null,
            'raw_response' => [
                'gateway' => 'log',
                'logged_at' => now()->toIso8601String(),
                'mode' => 'development',
            ],
        ];
    }

    public function getStatus(string $messageId): array
    {
        return [
            'status' => 'delivered', // همیشه delivered
            'message_id' => $messageId,
            'delivered_at' => now()->toIso8601String(),
        ];
    }

    public function getBalance(): array
    {
        return [
            'balance' => 999999999, // اعتبار نامحدود
            'currency' => 'IRR',
            'note' => 'Log mode - no real balance',
        ];
    }
}
