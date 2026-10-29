<?php

namespace App\Modules\Notification\Gateways\Email;

use App\Core\Logging\AppLogger;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Throwable;

/**
 * LaravelMailGateway - ارسال ایمیل با Laravel Mail
 */
class LaravelMailGateway implements EmailGatewayInterface
{
    public function getName(): string
    {
        return 'laravel_mail';
    }

    public function isAvailable(): bool
    {
        return !empty(config('mail.default'));
    }

    public function send(string $to, string $subject, string $body, array $options = []): array
    {
        try {
            $fromEmail = $options['from_email'] ?? config('mail.from.address');
            $fromName = $options['from_name'] ?? config('mail.from.name');

            $startTime = microtime(true);

            Mail::raw($body, function (Message $message) use ($to, $subject, $fromEmail, $fromName, $options) {
                $message->to($to)
                    ->subject($subject)
                    ->from($fromEmail, $fromName);

                // HTML body اگر موجود باشد
                if (!empty($options['html_body'])) {
                    $message->html($options['html_body']);
                }

                // Attachments
                if (!empty($options['attachments'])) {
                    foreach ($options['attachments'] as $attachment) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null,
                        ]);
                    }
                }

                // CC و BCC
                if (!empty($options['cc'])) {
                    $message->cc($options['cc']);
                }
                if (!empty($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }
            });

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->info('email.sent', [
                'to' => $to,
                'subject' => $subject,
                'duration_ms' => $durationMs,
            ]);

            return [
                'success' => true,
                'message_id' => 'MAIL-' . uniqid(),
                'error' => null,
            ];

        } catch (Throwable $e) {
            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('email.failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
