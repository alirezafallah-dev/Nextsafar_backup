<?php

namespace App\Console\Commands;

use App\Modules\Visa\Enums\VisaCategory;
use App\Modules\Visa\Enums\VisaStatus;
use App\Modules\Visa\Models\Country;
use App\Modules\Visa\Models\VisaDocumentChecklist;
use App\Modules\Visa\Models\VisaRule;
use App\Modules\Visa\Models\VisaType;
use Illuminate\Console\Command;

class TestVisaBase extends Command
{
    protected $signature = 'test:visa-base';
    protected $description = 'Test Visa base structure (Phase 5.2.1)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     🛂 Visa Base Structure Test (Phase 5.2.1) ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'countries_seeded' => $this->testCountries(),
            'visa_types_seeded' => $this->testVisaTypes(),
            'checklist_seeded' => $this->testChecklist(),
            'rules_seeded' => $this->testRules(),
            'relationships' => $this->testRelationships(),
            'scopes' => $this->testScopes(),
        ];

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║            📊 RESULTS                         ║');
        $this->info('╚══════════════════════════════════════════════╝');

        $passed = 0;
        foreach ($tests as $test => $result) {
            if ($result) {
                $this->info("  ✅ {$test}");
                $passed++;
            } else {
                $this->error("  ❌ {$test}");
            }
        }

        $this->newLine();
        $this->info("Passed: {$passed}/" . count($tests));

        return $passed === count($tests) ? 0 : 1;
    }

    private function testCountries(): bool
    {
        $count = Country::count();
        $popular = Country::popular()->count();
        $this->line("   Total countries: {$count}");
        $this->line("   Popular: {$popular}");
        
        $turkey = Country::findByCode('TR');
        $this->line("   Turkey found: " . ($turkey ? 'Yes' : 'No'));
        $this->line("   Turkey visa status: " . ($turkey?->visa_status_for_iranians?->label() ?? 'N/A'));
        
        return $count >= 20 && $popular >= 10 && $turkey !== null;
    }

    private function testVisaTypes(): bool
    {
        $count = VisaType::count();
        $published = VisaType::published()->count();
        $featured = VisaType::featured()->count();
        
        $this->line("   Total visa types: {$count}");
        $this->line("   Published: {$published}");
        $this->line("   Featured: {$featured}");
        
        // Test one visa with full details
        $dubai = VisaType::where('title', 'like', '%دبی%')->first();
        if ($dubai) {
            $this->line("   Dubai tourist: {$dubai->title}");
            $this->line("   Price: " . number_format($dubai->total_price));
            $this->line("   Processing: {$dubai->processing_time_label}");
            $this->line("   Country: {$dubai->country->name}");
        }
        
        return $count >= 15 && $published >= 15 && $featured >= 5;
    }

    private function testChecklist(): bool
    {
        $count = VisaDocumentChecklist::count();
        $required = VisaDocumentChecklist::where('is_required', true)->count();
        
        $this->line("   Total checklist items: {$count}");
        $this->line("   Required: {$required}");
        
        return $count >= 50 && $required >= 30;
    }

    private function testRules(): bool
    {
        $count = VisaRule::count();
        $this->line("   Total rules: {$count}");
        
        return $count >= 50;
    }

    private function testRelationships(): bool
    {
        $visa = VisaType::with(['country', 'checklist', 'rules'])->first();
        
        if (!$visa) return false;
        
        $this->line("   Visa: {$visa->title}");
        $this->line("   Country: {$visa->country?->name}");
        $this->line("   Checklist items: {$visa->checklist->count()}");
        $this->line("   Rules: {$visa->rules->count()}");
        
        return $visa->country !== null 
            && $visa->checklist->count() > 0 
            && $visa->rules->count() > 0;
    }

    private function testScopes(): bool
    {
        // Test various scopes
        $available = VisaType::available()->count();
        $featured = VisaType::featured()->available()->count();
        $tourist = VisaType::ofCategory(VisaCategory::TOURIST)->count();
        $dubai = VisaType::byCountrySlug('turkey')->count();
        
        $this->line("   Available visas: {$available}");
        $this->line("   Featured & available: {$featured}");
        $this->line("   Tourist visas: {$tourist}");
        $this->line("   Turkey visas: {$dubai}");
        
        return $available > 0 && $tourist > 0;
    }
}
