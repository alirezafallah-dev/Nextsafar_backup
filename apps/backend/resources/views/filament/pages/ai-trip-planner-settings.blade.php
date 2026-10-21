<x-filament-panels::page>
    {{-- آمار کلی --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">کل برنامه‌ها</p>
                    <p class="text-2xl font-bold">{{ number_format($this->stats['total_plans'] ?? 0) }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">موفق</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($this->stats['completed_plans'] ?? 0) }}</p>
                </div>
                <div class="text-3xl">✅</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">ناموفق</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($this->stats['failed_plans'] ?? 0) }}</p>
                </div>
                <div class="text-3xl">❌</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">کل توکن</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($this->stats['total_tokens'] ?? 0) }}</p>
                </div>
                <div class="text-3xl">🔋</div>
            </div>
        </x-filament::section>
    </div>

    {{-- آمار بیشتر --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">امروز</p>
                    <p class="text-xl font-bold">{{ number_format($this->stats['plans_today'] ?? 0) }}</p>
                </div>
                <div class="text-2xl">📅</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">این هفته</p>
                    <p class="text-xl font-bold">{{ number_format($this->stats['plans_this_week'] ?? 0) }}</p>
                </div>
                <div class="text-2xl">📆</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">میانگین زمان</p>
                    <p class="text-xl font-bold">{{ number_format(($this->stats['avg_generation_time'] ?? 0) / 1000, 2) }}s</p>
                </div>
                <div class="text-2xl">⏱️</div>
            </div>
        </x-filament::section>
    </div>

    {{-- وضعیت Provider ها --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">
            <span>🔌 وضعیت Provider ها</span>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(['gemini' => '🟢 Gemini', 'groq' => '🟡 Groq', 'qwen' => '🔵 Qwen'] as $provider => $label)
                @php
                    $hasKey = !empty(config("services.trip_planner.{$provider}_key"));
                    $model = config("services.trip_planner.{$provider}_model", 'نامشخص');
                @endphp
                <div class="p-4 rounded-lg {{ $hasKey ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' }}">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold">{{ $label }}</h3>
                        @if($hasKey)
                            <span class="px-2 py-1 text-xs bg-green-500 text-white rounded">فعال</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-400 text-white rounded">غیرفعال</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        مدل: <code class="text-xs">{{ $model }}</code>
                    </p>
                    <x-filament::button
                        size="sm"
                        color="{{ $hasKey ? 'success' : 'gray' }}"
                        wire:click="testProvider('{{ $provider }}')"
                        icon="heroicon-o-signal"
                    >
                        تست اتصال
                    </x-filament::button>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- دکمه‌های عملیاتی --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">
            <span>🧪 تست‌های سریع</span>
        </x-slot>

        <div class="flex flex-wrap gap-3">
            <x-filament::button
                color="primary"
                size="lg"
                wire:click="testGenerate"
                icon="heroicon-o-play"
            >
                🎯 تست تولید برنامه سفر (Istanbul, 2 روز)
            </x-filament::button>

            <x-filament::button
                color="info"
                size="lg"
                tag="a"
                href="{{ route('filament.admin.resources.trip-plans.index') }}"
                icon="heroicon-o-list-bullet"
            >
                📋 مشاهده همه برنامه‌ها
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- فرم تنظیمات --}}
    <form wire:submit="save">
        {{ $this->form }}
    </form>

    {{-- راهنما --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <span>📚 راهنما</span>
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <h3>🔑 نحوه دریافت API Keys:</h3>
            <ol>
                <li><strong>Gemini:</strong> <a href="https://aistudio.google.com/apikey" target="_blank">aistudio.google.com/apikey</a> - رایگان</li>
                <li><strong>Groq:</strong> <a href="https://console.groq.com/keys" target="_blank">console.groq.com/keys</a> - سریع‌ترین</li>
                <li><strong>Qwen:</strong> <a href="https://dashscope.console.aliyun.com/" target="_blank">dashscope.console.aliyun.com</a> - Alibaba</li>
            </ol>

            <h3>💡 توصیه‌ها:</h3>
            <ul>
                <li>حداقل ۲ provider فعال کنید (fallback بهتر)</li>
                <li>API keys را در فایل <code>.env</code> تنظیم کنید</li>
                <li>پس از تغییر .env، دستور <code>php artisan config:clear</code> را اجرا کنید</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
