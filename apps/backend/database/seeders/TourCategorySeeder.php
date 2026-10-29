<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'طبیعت‌گردی', 'slug' => 'nature', 'icon' => '🌿', 'color' => '#10b981', 'sort_order' => 1],
            ['name' => 'فرهنگی و تاریخی', 'slug' => 'cultural', 'icon' => '🏛️', 'color' => '#f59e0b', 'sort_order' => 2],
            ['name' => 'ماجراجویی', 'slug' => 'adventure', 'icon' => '🏔️', 'color' => '#ef4444', 'sort_order' => 3],
            ['name' => 'ساحلی', 'slug' => 'beach', 'icon' => '🏖️', 'color' => '#06b6d4', 'sort_order' => 4],
            ['name' => 'زیارتی', 'slug' => 'pilgrimage', 'icon' => '🕌', 'color' => '#8b5cf6', 'sort_order' => 5],
            ['name' => 'درمانی', 'slug' => 'medical', 'icon' => '💊', 'color' => '#ec4899', 'sort_order' => 6],
            ['name' => 'خانوادگی', 'slug' => 'family', 'icon' => '👨‍👩‍👧‍👦', 'color' => '#14b8a6', 'sort_order' => 7],
            ['name' => 'لوکس', 'slug' => 'luxury', 'icon' => '💎', 'color' => '#eab308', 'sort_order' => 8],
            ['name' => 'اقتصادی', 'slug' => 'budget', 'icon' => '💰', 'color' => '#84cc16', 'sort_order' => 9],
            ['name' => 'ماه عسل', 'slug' => 'honeymoon', 'icon' => '💑', 'color' => '#f43f5e', 'sort_order' => 10],
        ];

        foreach ($categories as $cat) {
            DB::table('tour_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command?->info('✅ ' . count($categories) . ' tour categories seeded.');
    }
}
