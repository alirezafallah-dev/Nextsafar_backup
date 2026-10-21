<?php

namespace App\Modules\TripPlanner\Services;

use App\Modules\TripPlanner\Models\TripPlan;
use App\Modules\TripPlanner\Services\Providers\ProviderChain;
use App\Modules\TripPlanner\Services\PromptBuilder;
use Illuminate\Support\Facades\Log;

class TripPlannerService
{
    public function __construct(
        private ProviderChain $providerChain,
        private PromptBuilder $promptBuilder
    ) {}

    public function generate(array $input, ?int $userId = null, ?string $sessionId = null, ?string $ip = null): TripPlan
    {
        $startTime = microtime(true);

        $tripPlan = TripPlan::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'destination' => $input['destination'],
            'country' => $input['country'] ?? null,
            'days' => $input['days'] ?? 3,
            'budget_level' => $input['budget_level'] ?? 'medium',
            'interests' => $input['interests'] ?? null,
            'travelers' => $input['travelers'] ?? 2,
            'start_date' => $input['start_date'] ?? null,
            'status' => 'pending',
            'days_plan' => [],
            'expires_at' => now()->addDays(7),
        ]);

        try {
            if (!$this->providerChain->hasAnyProvider()) {
                throw new \Exception('No AI provider configured');
            }

            $prompt = $this->promptBuilder->build($input);
            $schema = $this->promptBuilder->getSchema();

            $result = $this->providerChain->generateJsonWithSchema($prompt, $schema, [
                'timeout' => 75,
                'temperature' => 0.7,
                'max_tokens' => 4096,
            ]);

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'AI error');
            }

            $data = $result['data'];
            
            $tripPlan->update([
                'trip_title' => $data['trip_title'] ?? null,
                'trip_summary' => $data['trip_summary'] ?? null,
                'days_plan' => $data['days_plan'] ?? [],
                'total_budget_min' => $data['total_budget_min'] ?? null,
                'total_budget_max' => $data['total_budget_max'] ?? null,
                'suggested_hotels' => $data['suggested_hotels'] ?? null,
                'suggested_tours' => $data['suggested_tours'] ?? null,
                'suggested_restaurants' => $data['suggested_restaurants'] ?? null,
                'model_used' => $result['usage']['model'] ?? null,
                'tokens_used' => $result['usage']['total_tokens'] ?? 0,
                'generation_time_ms' => $durationMs,
                'status' => 'completed',
            ]);

            return $tripPlan->fresh();

        } catch (\Exception $e) {
            Log::error('Trip plan generation failed', [
                'trip_plan_id' => $tripPlan->id,
                'error' => $e->getMessage(),
            ]);

            $tripPlan->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'attempts' => $tripPlan->attempts + 1,
                'generation_time_ms' => (int) ((microtime(true) - $startTime) * 1000),
            ]);

            throw $e;
        }
    }

    public function get(int $id, ?int $userId = null, ?string $sessionId = null): ?TripPlan
    {
        $query = TripPlan::query();
        if ($userId) $query->where('user_id', $userId);
        elseif ($sessionId) $query->where('session_id', $sessionId);
        return $query->find($id);
    }

    public function hasActiveProviders(): bool
    {
        return $this->providerChain->hasAnyProvider();
    }

    public function testProviders(): array
    {
        return $this->providerChain->testAllProviders();
    }
}
