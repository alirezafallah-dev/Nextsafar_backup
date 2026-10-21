<?php

namespace App\Modules\TripPlanner\Services;

class PromptBuilder
{
    public function build(array $input): string
    {
        $destination = $input['destination'];
        $country = $input['country'] ?? 'نامشخص';
        $days = $input['days'] ?? 3;
        $budgetLevel = $input['budget_level'] ?? 'medium';
        $interests = $input['interests'] ?? [];
        $travelers = $input['travelers'] ?? 2;
        $startDate = $input['start_date'] ?? 'در اولین فرصت';

        $interestsList = !empty($interests) ? implode('، ', $interests) : 'عمومی';
        
        $budgetDescription = match($budgetLevel) {
            'economy' => 'اقتصادی (ارزان‌ترین گزینه‌ها)',
            'medium' => 'متوسط (تعادل بین کیفیت و قیمت)',
            'luxury' => 'لوکس (بهترین هتل‌ها و رستوران‌ها)',
            default => 'متوسط',
        };

        return <<<PROMPT
شما یک برنامه‌ریز سفر حرفه‌ای هستید. یک برنامه سفر دقیق برای این سفر طراحی کنید:

## اطلاعات سفر:
- مقصد: {$destination}
- کشور: {$country}
- تعداد روزها: {$days}
- سطح بودجه: {$budgetDescription}
- تعداد مسافران: {$travelers} نفر
- تاریخ شروع: {$startDate}
- علاقه‌مندی‌ها: {$interestsList}

## دستورالعمل‌ها:
1. برنامه روزانه با جزئیات (صبح، ظهر، بعدازظهر، شب)
2. برای هر فعالیت زمان و هزینه حدودی
3. هتل‌های پیشنهادی مطابق بودجه
4. رستوران‌های محلی متنوع
5. نکات مهم سفر
6. تفکیک بودجه (اقامت، خوراک، حمل‌ونقل، فعالیت)
7. همه مبالغ به USD
8. پاسخ به فارسی

خروجی باید مطابق JSON Schema باشد.
PROMPT;
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'trip_title' => ['type' => 'string'],
                'trip_summary' => ['type' => 'string'],
                'days_plan' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'day' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'activities' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'time' => ['type' => 'string'],
                                        'title' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'estimated_cost_usd' => ['type' => 'number'],
                                    ],
                                    'required' => ['time', 'title'],
                                ],
                            ],
                        ],
                        'required' => ['day', 'title'],
                    ],
                ],
                'total_budget_min' => ['type' => 'integer'],
                'total_budget_max' => ['type' => 'integer'],
                'suggested_hotels' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'stars' => ['type' => 'integer'],
                            'price_per_night_usd' => ['type' => 'number'],
                        ],
                        'required' => ['name'],
                    ],
                ],
                'suggested_restaurants' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'cuisine' => ['type' => 'string'],
                        ],
                        'required' => ['name'],
                    ],
                ],
                'suggested_tours' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'price_usd' => ['type' => 'number'],
                        ],
                        'required' => ['name'],
                    ],
                ],
            ],
            'required' => ['trip_title', 'trip_summary', 'days_plan'],
        ];
    }
}
