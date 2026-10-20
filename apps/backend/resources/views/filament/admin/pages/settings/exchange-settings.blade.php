<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            {{ $this->form }}
        </div>
        <div class="space-y-6">
            <x-filament::section heading="نرخ‌های فعلی">
                <x-slot name="description">
                    @if($lastUpdated)
                        آخرین بروزرسانی: {{ $lastUpdated->diffForHumans() }}
                    @else
                        هنوز نرخ زنده‌ای دریافت نشده
                    @endif
                </x-slot>
                @forelse(($currentRates ?? []) as $code => $rate)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="font-medium text-gray-900">{{ $code }}</span>
                        <span class="text-sm text-gray-600 font-mono">{{ number_format((float) $rate, 0, '.', ',') }} ریال</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">نرخی موجود نیست. نرخ دستی وارد کنید یا بروزرسانی را بزنید.</p>
                @endforelse
            </x-filament::section>

            <x-filament::section heading="ارزهای پشتیبانی‌شده">
                <div class="flex flex-wrap gap-2">
                    @foreach(($supportedCurrencies ?? []) as $code => $label)
                        <x-filament::badge color="gray">{{ is_string($code) ? $code : $label }}</x-filament::badge>
                    @endforeach
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
