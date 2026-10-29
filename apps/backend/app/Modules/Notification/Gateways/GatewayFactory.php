<?php

namespace App\Modules\Notification\Gateways;

use App\Modules\Notification\Gateways\Sms\SmsGatewayInterface;
use App\Modules\Notification\Gateways\Sms\LogSmsGateway;
use App\Modules\Notification\Gateways\Sms\KavenegarGateway;
use App\Modules\Notification\Gateways\Email\EmailGatewayInterface;
use App\Modules\Notification\Gateways\Email\LaravelMailGateway;
use App\Modules\Notification\Gateways\Internal\InternalGatewayInterface;
use App\Modules\Notification\Gateways\Internal\DatabaseInternalGateway;
use RuntimeException;

/**
 * GatewayFactory - ایجاد gateway مناسب بر اساس config
 * 
 * استراتژی: فقط با تغییر config می‌توان gateway را عوض کرد
 */
class GatewayFactory
{
    private static ?SmsGatewayInterface $smsGateway = null;
    private static ?EmailGatewayInterface $emailGateway = null;
    private static ?InternalGatewayInterface $internalGateway = null;

    /**
     * دریافت SMS Gateway بر اساس config
     */
    public static function sms(): SmsGatewayInterface
    {
        if (self::$smsGateway !== null) {
            return self::$smsGateway;
        }

        $gateway = config('services.sms_gateway', 'log');

        self::$smsGateway = match ($gateway) {
            'log' => new LogSmsGateway(),
            'kavenegar' => new KavenegarGateway(),
            default => throw new RuntimeException("Unknown SMS gateway: {$gateway}"),
        };

        return self::$smsGateway;
    }

    /**
     * دریافت Email Gateway
     */
    public static function email(): EmailGatewayInterface
    {
        if (self::$emailGateway === null) {
            self::$emailGateway = new LaravelMailGateway();
        }

        return self::$emailGateway;
    }

    /**
     * دریافت Internal Gateway
     */
    public static function internal(): InternalGatewayInterface
    {
        if (self::$internalGateway === null) {
            self::$internalGateway = new DatabaseInternalGateway();
        }

        return self::$internalGateway;
    }

    /**
     * Reset gateways (برای تست)
     */
    public static function reset(): void
    {
        self::$smsGateway = null;
        self::$emailGateway = null;
        self::$internalGateway = null;
    }
}
