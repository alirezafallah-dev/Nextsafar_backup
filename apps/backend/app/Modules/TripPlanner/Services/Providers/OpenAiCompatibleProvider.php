<?php

namespace App\Modules\TripPlanner\Services\Providers;

use App\Modules\TripPlanner\Contracts\TripProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class OpenAiCompatibleProvider implements TripProviderInterface
{
    abstract protected function getApiUrl(): string;
    abstract protected function getDefaultModel(): string;
    abstract protected function getConfigKeyName(): string;

    public function __construct(
        private ?string $apiKey = null,
        private ?string $model = null,
        private int $timeout = 60
    ) {
        $key = $this->getConfigKeyName();
        $this->apiKey = $apiKey ?? config("services.trip_planner.{$key}_key");
        $this->model = $model ?? config("services.trip_planner.{$key}_model", $this->getDefaultModel());
    }

    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateJsonWithSchema(string $prompt, array $schema, array $options = []): array
    {
        $startTime = microtime(true);

        if (!$this->hasApiKey()) {
            return $this->error('No API key', $startTime);
        }

        $fullPrompt = $prompt . "\n\nخروجی باید فقط یک JSON معتبر با این ساختار باشد:\n" . 
                     json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->getApiUrl(), [
                    'model' => $this->model,
                    'messages' => [['role' => 'user', 'content' => $fullPrompt]],
                    'temperature' => $options['temperature'] ?? 0.7,
                    'max_tokens' => $options['max_tokens'] ?? 4096,
                    'response_format' => ['type' => 'json_object'],
                ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                return $this->error("HTTP {$response->status()}", $startTime);
            }

            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? null;

            if (!$text) {
                return $this->error('Empty response', $startTime);
            }

            $json = json_decode($text, true);
            
            if (!is_array($json) && preg_match('/\{[\s\S]*\}/', $text, $m)) {
                $json = json_decode($m[0], true);
            }

            if (!is_array($json)) {
                return $this->error('Invalid JSON', $startTime);
            }

            return [
                'success' => true,
                'data' => $json,
                'error' => null,
                'usage' => [
                    'duration_ms' => $duration,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                    'model' => $this->model,
                    'provider' => $this->getName(),
                ],
            ];

        } catch (\Exception $e) {
            return $this->error('Exception: ' . $e->getMessage(), $startTime);
        }
    }

    public function testConnection(): array
    {
        return ['success' => $this->hasApiKey(), 'message' => $this->hasApiKey() ? 'OK' : 'No key'];
    }

    private function error(string $msg, float $start): array
    {
        return [
            'success' => false,
            'data' => null,
            'error' => $msg,
            'usage' => ['duration_ms' => (int)((microtime(true) - $start) * 1000), 'provider' => $this->getName()],
        ];
    }
}
