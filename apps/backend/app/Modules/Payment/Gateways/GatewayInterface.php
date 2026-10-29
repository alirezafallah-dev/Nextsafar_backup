<?php

namespace App\Modules\Payment\Gateways;

interface GatewayInterface
{
    /**
     * نام درگاه
     */
    public function getName(): string;

    /**
     * ایجاد تراکنش (purchase)
     */
    public function purchase(float $amount, string $description, array $metadata = []): array;

    /**
     * تأیید پرداخت (verify) - سمت سرور
     * 
     * @param string $authority شناسه تراکنش
     * @param float $expectedAmount مبلغ مورد انتظار (برای تطبیق)
     * @return array نتیجه تأیید
     */
    public function verify(string $authority, float $expectedAmount): array;

    /**
     * بررسی امضای callback (قانون ۱۴.۳.۵)
     * 
     * @param array $callbackData داده‌های callback
     * @param string $signature امضای ارسال شده
     * @return bool آیا امضا معتبر است؟
     */
    public function verifyCallbackSignature(array $callbackData, ?string $signature): bool;

    /**
     * استعلام وضعیت تراکنش (بدون تأیید)
     */
    public function inquire(string $authority): array;

    /**
     * استرداد وجه (refund)
     */
    public function refund(string $referenceId, float $amount): array;
}
