<?php

namespace App\Modules\TripPlanner\Services\Providers;

class QwenProvider extends OpenAiCompatibleProvider
{
    protected function getApiUrl(): string { return 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions'; }
    protected function getDefaultModel(): string { return 'qwen-plus'; }
    protected function getConfigKeyName(): string { return 'qwen'; }
    public function getName(): string { return 'qwen'; }
}
