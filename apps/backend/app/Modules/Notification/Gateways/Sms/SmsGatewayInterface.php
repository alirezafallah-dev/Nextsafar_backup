<?php

namespace App\Modules\Notification\Gateways\Sms;

/**
 * قرارداد درگاه ارسال پیامک
 * 
 * همه provider ها (Kavenegar, MeliPayamak, LogSms) باید این interface را پیاده‌سازی کنند
 */
interface SmsGatewayInterface
{
    /**
     * ارسال پیامک
     * 
     * @param string $to شماره گیرنده
     * @param string $message متن پیام
     * @param array $options تنظیمات اضافی
     * @return array نتیجه ارسال با کلیدهای:
     *               - success: bool
     *               - message_id: ?string (شناسه پیام در provider)
     *               - error: ?string
     *               - raw_response: ?array
     */
    public function send(string $to, string $message, array $options = []): array;

    /**
     * استعلام وضعیت پیام
     * 
     * @param string $messageId شناسه پیام
     * @return array وضعیت پیام
     */
    public function getStatus(string $messageId): array;

    /**
     * دریافت اعتبار باقی‌مانده
     * 
     * @return array ['balance' => int, 'currency' => string]
     */
    public function getBalance(): array;

    /**
     * نام provider
     */
    public function getName(): string;

    /**
     * آیا provider در دسترس است؟
     */
    public function isAvailable(): bool;
}
