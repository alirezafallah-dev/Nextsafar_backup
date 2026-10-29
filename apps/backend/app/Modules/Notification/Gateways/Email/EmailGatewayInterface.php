<?php

namespace App\Modules\Notification\Gateways\Email;

interface EmailGatewayInterface
{
    public function send(string $to, string $subject, string $body, array $options = []): array;
    public function getName(): string;
    public function isAvailable(): bool;
}
