<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Modules\Payment\Models\ExchangeSetting;
use App\Modules\Payment\Services\ExchangeService;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExchangeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.settings.exchange-settings';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'تنظیمات صرافی';
    protected static \UnitEnum|string|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'تنظیمات نرخ ارز';
    protected static ?string $slug = 'settings/exchange';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = ExchangeSetting::getCurrent();
        $this->form->fill([
            'api_key' => $settings->api_key,
            'use_live_rates' => $settings->use_live_rates,
            'cache_duration' => $settings->cache_duration,
            'manual_rates' => $this->formatManualRates($settings->manual_rates ?? []),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('تنظیمات API')
                    ->description('اتصال به BrsApi.ir برای دریافت نرخ زنده ارز')
                    ->schema([
                        TextInput::make('api_key')->label('کلید API')->password()->revealable()
                            ->placeholder('کلید API خود را وارد کنید')->helperText('از سایت brsapi.ir دریافت کنید')->maxLength(255),
                        Toggle::make('use_live_rates')->label('استفاده از نرخ زنده')
                            ->helperText('اگر فعال باشد، نرخ‌ها از API دریافت می‌شوند.')->default(true),
                        TextInput::make('cache_duration')->label('مدت کش (ثانیه)')->numeric()
                            ->minValue(60)->maxValue(86400)->default(3600)->helperText('پیش‌فرض: 3600 ثانیه (1 ساعت)'),
                    ])
                    ->columns(2),
                Section::make('نرخ‌های دستی (Fallback)')
                    ->description('این نرخ‌ها زمانی استفاده می‌شوند که API در دسترس نباشد')
                    ->schema([
                        KeyValue::make('manual_rates')->label('نرخ ارزها (به ریال)')
                            ->keyLabel('کد ارز')->valueLabel('نرخ (ریال)')->addActionLabel('افزودن ارز جدید')
                            ->helperText('مثال: USD = 600000 یعنی 1 دلار = 600,000 ریال')->reorderable()->columnSpan('full'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_rates')->label('بروزرسانی نرخ‌ها')->color('success')->icon('heroicon-o-arrow-path')
                ->action('refreshRates')->requiresConfirmation()->modalHeading('بروزرسانی نرخ ارز')
                ->modalDescription('آیا مطمئن هستید که می‌خواهید نرخ‌ها را از API بروزرسانی کنید؟')->modalSubmitActionLabel('بله، بروزرسانی کن'),
            Action::make('save')->label('ذخیره تنظیمات')->color('primary')->icon('heroicon-o-check')->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = ExchangeSetting::getCurrent();
        $manualRates = [];
        foreach ($data['manual_rates'] ?? [] as $code => $rate) {
            $code = strtoupper(trim((string) $code));
            $rate = (float) str_replace([',', '،'], '', (string) $rate);
            if ($code !== '' && $rate > 0) {
                $manualRates[$code] = $rate;
            }
        }
        $settings->update([
            'api_key' => $data['api_key'] ?? null,
            'use_live_rates' => $data['use_live_rates'] ?? true,
            'cache_duration' => (int) ($data['cache_duration'] ?? 3600),
            'manual_rates' => $manualRates,
        ]);
        ExchangeService::clearCache();
        Notification::make()->title('تنظیمات ذخیره شد')->body('تنظیمات صرافی با موفقیت ذخیره شد.')->success()->send();
    }

    public function refreshRates(): void
    {
        $result = ExchangeService::refreshRates();
        if (($result['success'] ?? false)) {
            Notification::make()->title('نرخ‌ها بروزرسانی شدند')->body(($result['count'] ?? 0) . ' ارز با موفقیت بروزرسانی شد.')->success()->send();
        } else {
            Notification::make()->title('خطا در بروزرسانی')->body($result['message'] ?? 'خطای ناشناخته')->danger()->send();
        }
    }

    private function formatManualRates(array $rates): array
    {
        $out = [];
        foreach ($rates as $code => $rate) {
            $out[$code] = number_format((float) $rate, 0, '.', '');
        }
        return $out;
    }

    protected function getViewData(): array
    {
        $settings = ExchangeSetting::getCurrent();
        return [
            'currentRates' => ExchangeService::getExchangeRates(),
            'lastUpdated' => $settings->last_updated_at,
            'supportedCurrencies' => ExchangeService::getSupportedCurrencies(),
        ];
    }
}
