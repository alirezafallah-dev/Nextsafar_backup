<?php

namespace App\Modules\TripPlanner\Contracts;

interface TripProviderInterface
{
    public function getName(): string;
    public function hasApiKey(): bool;
    public function generateJsonWithSchema(string $prompt, array $schema, array $options = []): array;
    public function testConnection(): array;
}
