<?php

namespace App\Modules\TripPlanner\Services\Providers;

use App\Modules\TripPlanner\Contracts\TripProviderInterface;
use Illuminate\Support\Facades\Log;

class ProviderChain
{
    private array $providers = [];

    public function __construct(?array $order = null)
    {
        $default = $order ?? config('services.trip_planner.provider_order', ['gemini', 'groq', 'qwen']);
        
        foreach ($default as $name) {
            $this->providers[$name] = $this->create($name);
        }
    }

    public function hasAnyProvider(): bool
    {
        foreach ($this->providers as $p) {
            if ($p->hasApiKey()) return true;
        }
        return false;
    }

    public function generateJsonWithSchema(string $prompt, array $schema, array $options = []): array
    {
        $last = [
            'success' => false,
            'data' => null,
            'error' => 'No AI provider configured',
            'usage' => [],
        ];

        foreach ($this->providers as $name => $provider) {
            if (!$provider->hasApiKey()) continue;

            $result = $provider->generateJsonWithSchema($prompt, $schema, $options);
            if ($result['success']) return $result;

            $last = $result;
            Log::warning("TripProvider [{$name}] failed", ['error' => $result['error'] ?? 'unknown']);
        }

        return $last;
    }

    public function testAllProviders(): array
    {
        $results = [];
        foreach ($this->providers as $name => $p) {
            $results[$name] = [
                'has_key' => $p->hasApiKey(),
                'test' => $p->hasApiKey() ? $p->testConnection() : ['skipped' => true],
            ];
        }
        return $results;
    }

    private function create(string $name): TripProviderInterface
    {
        return match($name) {
            'gemini' => new GeminiProvider(),
            'groq' => new GroqProvider(),
            'qwen' => new QwenProvider(),
            default => throw new \InvalidArgumentException("Unknown provider: {$name}"),
        };
    }
}
