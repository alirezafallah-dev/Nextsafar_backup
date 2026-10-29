<?php

namespace App\Filament\Admin\Pages;

use App\Modules\TripPlanner\Models\TripPlan;
use App\Modules\TripPlanner\Services\TripPlannerService;
use App\Modules\TripPlanner\Services\Providers\ProviderChain;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AiTripPlannerSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'تنظیمات AI Trip Planner';
    protected static ?string $title = 'تنظیمات AI Trip Planner';
    protected static \UnitEnum|string|null $navigationGroup = 'هوش مصنوعی';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'ai-trip-planner-settings';

    public ?array $data = [];
    public array $stats = [];

    public function getView(): string
    {
        return 'filament.pages.ai-trip-planner-settings';
    }

    public function mount(): void
    {
        $this->data = [
            'provider_order' => implode(',', config('services.trip_planner.provider_order', ['gemini', 'groq', 'qwen'])),
            'gemini_key' => config('services.trip_planner.gemini_key'),
            'gemini_model' => config('services.trip_planner.gemini_model'),
            'groq_key' => config('services.trip_planner.groq_key'),
            'groq_model' => config('services.trip_planner.groq_model'),
            'qwen_key' => config('services.trip_planner.qwen_key'),
            'qwen_model' => config('services.trip_planner.qwen_model'),
        ];

        $this->loadStats();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🤖 Provider Chain (ترتیب Fallback)')
                    ->description('اگر provider اول شکست بخورد، از provider بعدی استفاده می‌شود')
                    ->schema([
                        Select::make('provider_order')
                            ->label('ترتیب Provider ها')
                            ->options([
                                'gemini,groq,qwen' => '🥇 Gemini → 🥈 Groq → 🥉 Qwen (پیشنهادی)',
                                'groq,gemini,qwen' => '🥇 Groq → 🥈 Gemini → 🥉 Qwen (سریع‌تر)',
                                'gemini' => 'فقط Gemini',
                                'groq' => 'فقط Groq',
                                'qwen' => 'فقط Qwen',
                            ])
                            ->required(),
                    ]),

                Section::make('🟢 Gemini (Google AI Studio)')
                    ->description('🔗 دریافت کلید: https://aistudio.google.com/apikey')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('gemini_key')
                                ->label('API Key')
                                ->password()
                                ->revealable()
                                ->placeholder('AIza...')
                                ->maxLength(100),
                            Select::make('gemini_model')
                                ->label('مدل')
                                ->options([
                                    'gemini-3.7-flash' => 'Gemini 3.7 Flash (جدیدترین - پیشنهادی)',
                                    'gemini-3.6-flash' => 'Gemini 3.6 Flash',
                                    'gemini-3.5-flash' => 'Gemini 3.5 Flash',
                                    'gemini-3.5-pro' => 'Gemini 3.5 Pro (کیفیت بالاتر)',
                                    'gemini-3.0-flash' => 'Gemini 3.0 Flash',
                                    'gemini-3.0-pro' => 'Gemini 3.0 Pro',
                                    'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                                ])
                                ->default('gemini-3.7-flash'),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('🟡 Groq (Fast LLM - Llama 3.3)')
                    ->description('🔗 دریافت کلید: https://console.groq.com/keys')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('groq_key')
                                ->label('API Key')
                                ->password()
                                ->revealable()
                                ->placeholder('gsk_...')
                                ->maxLength(100),
                            Select::make('groq_model')
                                ->label('مدل')
                                ->options([
                                    'llama-3.3-70b-versatile' => 'Llama 3.3 70B (پیشنهادی)',
                                    'llama-3.1-70b-versatile' => 'Llama 3.1 70B',
                                    'mixtral-8x7b-32768' => 'Mixtral 8x7B',
                                ])
                                ->default('llama-3.3-70b-versatile'),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('🔵 Qwen (Alibaba)')
                    ->description('🔗 دریافت کلید: https://dashscope.console.aliyun.com/')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('qwen_key')
                                ->label('API Key')
                                ->password()
                                ->revealable()
                                ->placeholder('sk-...')
                                ->maxLength(100),
                            Select::make('qwen_model')
                                ->label('مدل')
                                ->options([
                                    'qwen-plus' => 'Qwen Plus (پیشنهادی)',
                                    'qwen-turbo' => 'Qwen Turbo (سریع‌تر)',
                                    'qwen-max' => 'Qwen Max (کیفیت بالاتر)',
                                ])
                                ->default('qwen-plus'),
                        ]),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function loadStats(): void
    {
        $this->stats = [
            'total_plans' => TripPlan::count(),
            'completed_plans' => TripPlan::where('status', 'completed')->count(),
            'failed_plans' => TripPlan::where('status', 'failed')->count(),
            'total_tokens' => TripPlan::sum('tokens_used'),
            'avg_generation_time' => TripPlan::where('status', 'completed')->avg('generation_time_ms') ?? 0,
            'plans_today' => TripPlan::whereDate('created_at', today())->count(),
            'plans_this_week' => TripPlan::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    public function testProvider(string $provider): void
    {
        try {
            $chain = app(ProviderChain::class);
            $results = $chain->testAllProviders();
            $result = $results[$provider] ?? null;

            if (!$result) {
                Notification::make()->title('Provider یافت نشد')->danger()->send();
                return;
            }

            if ($result['has_key']) {
                Notification::make()
                    ->title("✅ {$provider} پیکربندی شده")
                    ->body('API key تنظیم شده است. برای تست واقعی از دکمه Generate استفاده کنید.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title("❌ {$provider} پیکربندی نشده")
                    ->body('لطفاً API key را در فایل .env تنظیم کنید')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('خطا')->body($e->getMessage())->danger()->send();
        }
    }

    public function testGenerate(): void
    {
        try {
            $service = app(TripPlannerService::class);

            if (!$service->hasActiveProviders()) {
                Notification::make()
                    ->title('❌ هیچ provider فعال نیست')
                    ->body('لطفاً حداقل یک API key در .env تنظیم کنید')
                    ->danger()
                    ->send();
                return;
            }

            $testInput = [
                'destination' => 'Istanbul',
                'country' => 'Turkey',
                'days' => 2,
                'budget_level' => 'medium',
                'interests' => ['history', 'food'],
                'travelers' => 2,
                'start_date' => now()->addDays(30)->format('Y-m-d'),
            ];

            $startTime = microtime(true);
            $plan = $service->generate($testInput, null, 'admin-test-' . uniqid(), '127.0.0.1');
            $duration = (int)((microtime(true) - $startTime) * 1000);

            Notification::make()
                ->title('✅ تست موفق!')
                ->body("برنامه سفر تستی تولید شد\nعنوان: {$plan->trip_title}\nزمان: {$duration}ms\nتوکن: {$plan->tokens_used}")
                ->success()
                ->persistent()
                ->send();

            $this->loadStats();

        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ تست ناموفق')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_plans')
                ->label('مشاهده برنامه‌ها')
                ->icon('heroicon-o-list-bullet')
                ->url(route('filament.admin.resources.trip-plans.index')),
        ];
    }
}
