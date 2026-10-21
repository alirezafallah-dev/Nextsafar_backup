<?php

namespace App\Modules\TripPlanner\Services\Providers;

class GroqProvider extends OpenAiCompatibleProvider
{
    protected function getApiUrl(): string { return 'https://api.groq.com/openai/v1/chat/completions'; }
    protected function getDefaultModel(): string { return 'llama-3.3-70b-versatile'; }
    protected function getConfigKeyName(): string { return 'groq'; }
    public function getName(): string { return 'groq'; }
}
