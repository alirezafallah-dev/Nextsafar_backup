<?php

namespace App\Modules\TripPlanner\Services\Providers;

use App\Modules\TripPlanner\Contracts\TripProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements TripProviderInterface
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models';
    private const VALID_MODELS = [
        'gemini-3.7-flash',
        'gemini-3.6-flash',
        'gemini-3.5-flash',
        'gemini-3.5-pro',
        'gemini-3.0-flash',
        'gemini-3.0-pro',
        'gemini-2.5-flash',
        'gemini-2.5-flash',
    ];

    public function __construct(
        private ?string $apiKey = null,
        private ?string $model = null,
        private int $timeout = 75
    ) {
        $this->apiKey = $apiKey ?? config('services.trip_planner.gemini_key');
        $this->model = $model ?? config('services.trip_planner.gemini_model', self::DEFAULT_MODEL);
        
        if (!in_array($this->model, self::VALID_MODELS)) {
            $this->model = self::DEFAULT_MODEL;
        }
    }

    public function getName(): string
    {
        return 'gemini';
    }

    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateJsonWithSchema(string $prompt, array $schema, array $options = []): array
    {
        $startTime = microtime(true);

        if (!$this->hasApiKey()) {
            return $this->error('Gemini API key is not configured', $startTime);
        }

        $url = self::API_URL . '/' . $this->model . ':generateContent?key=' . urlencode($this->apiKey);
        
        $timeout = $options['timeout'] ?? $this->timeout;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4096;

        try {
            $response = Http::timeout($timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $schema,
                    ],
                ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                Log::warning('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->error("HTTP {$response->status()}: " . substr($response->body(), 0, 200), $startTime);
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$text) {
                return $this->error('Empty response from Gemini', $startTime);
            }

            $json = json_decode($text, true);
            
            if (!is_array($json)) {
                // Try to extract JSON from text
                if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                    $json = json_decode($matches[0], true);
                }
            }

            if (!is_array($json)) {
                return $this->error('Invalid JSON response', $startTime);
            }

            return [
                'success' => true,
                'data' => $json,
                'error' => null,
                'usage' => [
                    'duration_ms' => $duration,
                    'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
                    'model' => $this->model,
                    'provider' => 'gemini',
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->error('Exception: ' . $e->getMessage(), $startTime);
        }
    }

    public function testConnection(): array
    {
        if (!$this->hasApiKey()) {
            return [
                'success' => false,
                'message' => 'API key not configured',
            ];
        }

        try {
            $response = Http::timeout(10)->get(
                self::API_URL . '/' . $this->model . '?key=' . urlencode($this->apiKey)
            );

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'model' => $this->model,
                ];
            }

            return [
                'success' => false,
                'message' => 'HTTP ' . $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function error(string $message, float $startTime): array
    {
        return [
            'success' => false,
            'data' => null,
            'error' => $message,
            'usage' => [
                'duration_ms' => (int) ((microtime(true) - $startTime) * 1000),
                'provider' => 'gemini',
            ],
        ];
    }

    public static function getModelsList(): array
    {
        return self::VALID_MODELS;
    }
}
