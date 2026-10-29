<?php

namespace Database\Seeders;

use App\Modules\Visa\Enums\VisaCategory;
use App\Modules\Visa\Models\Country;
use App\Modules\Visa\Models\VisaDocumentChecklist;
use App\Modules\Visa\Models\VisaRule;
use App\Modules\Visa\Models\VisaType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VisaBaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCountries();
        $this->seedVisaTypes();
        $this->seedChecklists();
        $this->seedRules();
    }

    private function seedCountries(): void
    {
        $countries = [
            ['code' => 'TR', 'code3' => 'TUR', 'name' => 'ترکیه', 'name_en' => 'Turkey', 'capital' => 'آنکارا', 'continent' => 'Asia', 'flag_emoji' => '🇹🇷', 'phone_code' => '+90', 'currency' => 'TRY', 'visa_status' => 'visa_free', 'is_popular' => true, 'sort' => 1],
            ['code' => 'AE', 'code3' => 'ARE', 'name' => 'امارات', 'name_en' => 'UAE', 'capital' => 'ابوظبی', 'continent' => 'Asia', 'flag_emoji' => '🇦🇪', 'phone_code' => '+971', 'currency' => 'AED', 'visa_status' => 'evisa', 'is_popular' => true, 'sort' => 2],
            ['code' => 'GE', 'code3' => 'GEO', 'name' => 'گرجستان', 'name_en' => 'Georgia', 'capital' => 'تفلیس', 'continent' => 'Asia', 'flag_emoji' => '🇬🇪', 'phone_code' => '+995', 'currency' => 'GEL', 'visa_status' => 'visa_free', 'is_popular' => true, 'sort' => 3],
            ['code' => 'AM', 'code3' => 'ARM', 'name' => 'ارمنستان', 'name_en' => 'Armenia', 'capital' => 'ایروان', 'continent' => 'Asia', 'flag_emoji' => '🇦🇲', 'phone_code' => '+374', 'currency' => 'AMD', 'visa_status' => 'visa_free', 'is_popular' => true, 'sort' => 4],
            ['code' => 'TH', 'code3' => 'THA', 'name' => 'تایلند', 'name_en' => 'Thailand', 'capital' => 'بانکوک', 'continent' => 'Asia', 'flag_emoji' => '🇹🇭', 'phone_code' => '+66', 'currency' => 'THB', 'visa_status' => 'evisa', 'is_popular' => true, 'sort' => 5],
            ['code' => 'RU', 'code3' => 'RUS', 'name' => 'روسیه', 'name_en' => 'Russia', 'capital' => 'مسکو', 'continent' => 'Europe', 'flag_emoji' => '🇷🇺', 'phone_code' => '+7', 'currency' => 'RUB', 'visa_status' => 'evisa', 'is_popular' => true, 'sort' => 6],
            ['code' => 'CN', 'code3' => 'CHN', 'name' => 'چین', 'name_en' => 'China', 'capital' => 'پکن', 'continent' => 'Asia', 'flag_emoji' => '🇨🇳', 'phone_code' => '+86', 'currency' => 'CNY', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 7],
            ['code' => 'IN', 'code3' => 'IND', 'name' => 'هند', 'name_en' => 'India', 'capital' => 'دهلی', 'continent' => 'Asia', 'flag_emoji' => '🇮🇳', 'phone_code' => '+91', 'currency' => 'INR', 'visa_status' => 'evisa', 'is_popular' => true, 'sort' => 8],
            ['code' => 'IT', 'code3' => 'ITA', 'name' => 'ایتالیا', 'name_en' => 'Italy', 'capital' => 'رم', 'continent' => 'Europe', 'flag_emoji' => '🇮🇹', 'phone_code' => '+39', 'currency' => 'EUR', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 9],
            ['code' => 'FR', 'code3' => 'FRA', 'name' => 'فرانسه', 'name_en' => 'France', 'capital' => 'پاریس', 'continent' => 'Europe', 'flag_emoji' => '🇫🇷', 'phone_code' => '+33', 'currency' => 'EUR', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 10],
            ['code' => 'DE', 'code3' => 'DEU', 'name' => 'آلمان', 'name_en' => 'Germany', 'capital' => 'برلین', 'continent' => 'Europe', 'flag_emoji' => '🇩🇪', 'phone_code' => '+49', 'currency' => 'EUR', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 11],
            ['code' => 'ES', 'code3' => 'ESP', 'name' => 'اسپانیا', 'name_en' => 'Spain', 'capital' => 'مادرید', 'continent' => 'Europe', 'flag_emoji' => '🇪🇸', 'phone_code' => '+34', 'currency' => 'EUR', 'visa_status' => 'required', 'is_popular' => false, 'sort' => 12],
            ['code' => 'CA', 'code3' => 'CAN', 'name' => 'کانادا', 'name_en' => 'Canada', 'capital' => 'اتاوا', 'continent' => 'North America', 'flag_emoji' => '🇨🇦', 'phone_code' => '+1', 'currency' => 'CAD', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 13],
            ['code' => 'US', 'code3' => 'USA', 'name' => 'آمریکا', 'name_en' => 'USA', 'capital' => 'واشنگتن', 'continent' => 'North America', 'flag_emoji' => '🇺🇸', 'phone_code' => '+1', 'currency' => 'USD', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 14],
            ['code' => 'GB', 'code3' => 'GBR', 'name' => 'انگلستان', 'name_en' => 'UK', 'capital' => 'لندن', 'continent' => 'Europe', 'flag_emoji' => '🇬🇧', 'phone_code' => '+44', 'currency' => 'GBP', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 15],
            ['code' => 'AU', 'code3' => 'AUS', 'name' => 'استرالیا', 'name_en' => 'Australia', 'capital' => 'کانبرا', 'continent' => 'Oceania', 'flag_emoji' => '🇦🇺', 'phone_code' => '+61', 'currency' => 'AUD', 'visa_status' => 'required', 'is_popular' => true, 'sort' => 16],
            ['code' => 'MY', 'code3' => 'MYS', 'name' => 'مالزی', 'name_en' => 'Malaysia', 'capital' => 'کوالالامپور', 'continent' => 'Asia', 'flag_emoji' => '🇲🇾', 'phone_code' => '+60', 'currency' => 'MYR', 'visa_status' => 'visa_free', 'is_popular' => true, 'sort' => 17],
            ['code' => 'OM', 'code3' => 'OMN', 'name' => 'عمان', 'name_en' => 'Oman', 'capital' => 'مسقط', 'continent' => 'Asia', 'flag_emoji' => '🇴🇲', 'phone_code' => '+968', 'currency' => 'OMR', 'visa_status' => 'evisa', 'is_popular' => false, 'sort' => 18],
            ['code' => 'QA', 'code3' => 'QAT', 'name' => 'قطر', 'name_en' => 'Qatar', 'capital' => 'دوحه', 'continent' => 'Asia', 'flag_emoji' => '🇶🇦', 'phone_code' => '+974', 'currency' => 'QAR', 'visa_status' => 'visa_free', 'is_popular' => false, 'sort' => 19],
            ['code' => 'AZ', 'code3' => 'AZE', 'name' => 'آذربایجان', 'name_en' => 'Azerbaijan', 'capital' => 'باکو', 'continent' => 'Asia', 'flag_emoji' => '🇦🇿', 'phone_code' => '+994', 'currency' => 'AZN', 'visa_status' => 'evisa', 'is_popular' => false, 'sort' => 20],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(
                ['code' => $c['code']],
                [
                    'code3' => $c['code3'],
                    'name' => $c['name'],
                    'name_en' => $c['name_en'],
                    'capital' => $c['capital'],
                    'continent' => $c['continent'],
                    'flag_emoji' => $c['flag_emoji'],
                    'phone_code' => $c['phone_code'],
                    'currency' => $c['currency'],
                    'visa_status_for_iranians' => $c['visa_status'],
                    'slug' => Str::slug($c['name'], '_'),
                    'is_active' => true,
                    'is_popular' => $c['is_popular'],
                    'sort_order' => $c['sort'],
                ]
            );
        }

        $this->command?->info('✅ ' . count($countries) . ' countries seeded');
    }

    private function seedVisaTypes(): void
    {
        $visaTypes = [
            // امارات
            ['country_code' => 'AE', 'title' => 'ویزا توریستی دبی', 'category' => 'tourist', 'proc_min' => 3, 'proc_max' => 5, 'validity' => 60, 'max_stay' => 30, 'entry' => 'single', 'base' => 5000000, 'embassy' => 3500000, 'featured' => true],
            ['country_code' => 'AE', 'title' => 'ویزا توریستی دبی (چندبار ورود)', 'category' => 'tourist', 'proc_min' => 5, 'proc_max' => 7, 'validity' => 60, 'max_stay' => 30, 'entry' => 'multiple', 'base' => 8000000, 'embassy' => 6000000, 'featured' => false],
            ['country_code' => 'AE', 'title' => 'ویزا تجاری دبی', 'category' => 'business', 'proc_min' => 7, 'proc_max' => 14, 'validity' => 90, 'max_stay' => 90, 'entry' => 'multiple', 'base' => 15000000, 'embassy' => 8000000, 'featured' => false],
            
            // روسیه
            ['country_code' => 'RU', 'title' => 'ویزا توریستی روسیه', 'category' => 'tourist', 'proc_min' => 7, 'proc_max' => 10, 'validity' => 30, 'max_stay' => 30, 'entry' => 'single', 'base' => 7000000, 'embassy' => 5000000, 'featured' => true],
            ['country_code' => 'RU', 'title' => 'ویزا الکترونیکی روسیه', 'category' => 'tourist', 'proc_min' => 4, 'proc_max' => 7, 'validity' => 60, 'max_stay' => 16, 'entry' => 'single', 'base' => 3500000, 'embassy' => 2500000, 'featured' => true],
            
            // چین
            ['country_code' => 'CN', 'title' => 'ویزا توریستی چین', 'category' => 'tourist', 'proc_min' => 10, 'proc_max' => 15, 'validity' => 90, 'max_stay' => 30, 'entry' => 'single', 'base' => 12000000, 'embassy' => 7000000, 'featured' => true],
            ['country_code' => 'CN', 'title' => 'ویزا تجاری چین', 'category' => 'business', 'proc_min' => 14, 'proc_max' => 21, 'validity' => 180, 'max_stay' => 90, 'entry' => 'multiple', 'base' => 20000000, 'embassy' => 12000000, 'featured' => false],
            
            // هند
            ['country_code' => 'IN', 'title' => 'ویزا الکترونیکی هند', 'category' => 'tourist', 'proc_min' => 3, 'proc_max' => 5, 'validity' => 365, 'max_stay' => 90, 'entry' => 'multiple', 'base' => 4500000, 'embassy' => 3000000, 'featured' => true],
            
            // ایتالیا (شنگن)
            ['country_code' => 'IT', 'title' => 'ویزا توریستی ایتالیا (شنگن)', 'category' => 'tourist', 'proc_min' => 15, 'proc_max' => 30, 'validity' => 90, 'max_stay' => 90, 'entry' => 'single', 'base' => 25000000, 'embassy' => 15000000, 'featured' => true],
            ['country_code' => 'IT', 'title' => 'ویزا تحصیلی ایتالیا', 'category' => 'student', 'proc_min' => 30, 'proc_max' => 60, 'validity' => 365, 'max_stay' => 365, 'entry' => 'multiple', 'base' => 30000000, 'embassy' => 18000000, 'featured' => false],
            
            // فرانسه
            ['country_code' => 'FR', 'title' => 'ویزا توریستی فرانسه (شنگن)', 'category' => 'tourist', 'proc_min' => 15, 'proc_max' => 30, 'validity' => 90, 'max_stay' => 90, 'entry' => 'single', 'base' => 28000000, 'embassy' => 16000000, 'featured' => true],
            
            // آلمان
            ['country_code' => 'DE', 'title' => 'ویزا توریستی آلمان (شنگن)', 'category' => 'tourist', 'proc_min' => 15, 'proc_max' => 30, 'validity' => 90, 'max_stay' => 90, 'entry' => 'single', 'base' => 27000000, 'embassy' => 15000000, 'featured' => true],
            ['country_code' => 'DE', 'title' => 'ویزا کاری آلمان', 'category' => 'work', 'proc_min' => 45, 'proc_max' => 90, 'validity' => 365, 'max_stay' => 365, 'entry' => 'multiple', 'base' => 40000000, 'embassy' => 20000000, 'featured' => false],
            
            // کانادا
            ['country_code' => 'CA', 'title' => 'ویزا توریستی کانادا', 'category' => 'tourist', 'proc_min' => 30, 'proc_max' => 90, 'validity' => 3650, 'max_stay' => 180, 'entry' => 'multiple', 'base' => 50000000, 'embassy' => 30000000, 'featured' => true],
            ['country_code' => 'CA', 'title' => 'ویزا تحصیلی کانادا', 'category' => 'student', 'proc_min' => 60, 'proc_max' => 120, 'validity' => 1460, 'max_stay' => 1460, 'entry' => 'multiple', 'base' => 60000000, 'embassy' => 35000000, 'featured' => false],
            
            // انگلستان
            ['country_code' => 'GB', 'title' => 'ویزا توریستی انگلستان', 'category' => 'tourist', 'proc_min' => 21, 'proc_max' => 45, 'validity' => 180, 'max_stay' => 180, 'entry' => 'multiple', 'base' => 45000000, 'embassy' => 25000000, 'featured' => true],
            
            // استرالیا
            ['country_code' => 'AU', 'title' => 'ویزا توریستی استرالیا', 'category' => 'tourist', 'proc_min' => 30, 'proc_max' => 60, 'validity' => 365, 'max_stay' => 90, 'entry' => 'multiple', 'base' => 48000000, 'embassy' => 28000000, 'featured' => true],
            
            // تایلند
            ['country_code' => 'TH', 'title' => 'ویزا توریستی تایلند', 'category' => 'tourist', 'proc_min' => 5, 'proc_max' => 10, 'validity' => 90, 'max_stay' => 60, 'entry' => 'single', 'base' => 3500000, 'embassy' => 2000000, 'featured' => true],
            
            // عمان
            ['country_code' => 'OM', 'title' => 'ویزا الکترونیکی عمان', 'category' => 'tourist', 'proc_min' => 3, 'proc_max' => 5, 'validity' => 30, 'max_stay' => 30, 'entry' => 'single', 'base' => 3000000, 'embassy' => 2000000, 'featured' => false],
        ];

        foreach ($visaTypes as $v) {
            $country = Country::where('code', $v['country_code'])->first();
            if (!$country) continue;

            $slug = Str::slug($v['title'], '-');

            VisaType::updateOrCreate(
                ['slug' => $slug],
                [
                    'country_id' => $country->id,
                    'title' => $v['title'],
                    'category' => $v['category'],
                    'processing_days_min' => $v['proc_min'],
                    'processing_days_max' => $v['proc_max'],
                    'validity_days' => $v['validity'],
                    'max_stay_days' => $v['max_stay'],
                    'entry_type' => $v['entry'],
                    'base_price' => $v['base'],
                    'embassy_fee' => $v['embassy'],
                    'currency' => 'IRR',
                    'status' => 'published',
                    'is_featured' => $v['featured'],
                    'accepts_online_application' => true,
                    'short_description' => "ویزا {$v['title']} - پردازش {$v['proc_min']} تا {$v['proc_max']} روزه",
                ]
            );
        }

        $this->command?->info('✅ ' . count($visaTypes) . ' visa types seeded');
    }

    private function seedChecklists(): void
    {
        // چک‌لیست پیش‌فرض برای ویزاهای توریستی
        $touristChecklist = [
            [
                'title' => 'پاسپورت',
                'description' => 'پاسپورت با حداقل ۶ ماه اعتبار از تاریخ سفر. دو صفحه خالی کنار هم.',
                'category' => 'passport',
                'required' => true,
                'per_passenger' => true,
                'importance' => 'required',
                'mime' => ['image/jpeg', 'image/png', 'application/pdf'],
                'max_size' => 5 * 1024 * 1024, // 5MB
                'sort' => 1,
            ],
            [
                'title' => 'عکس پرسنلی',
                'description' => 'عکس ۳×۴ یا ۴×۶ با زمینه سفید، جدید (کمتر از ۶ ماه)',
                'category' => 'photo',
                'required' => true,
                'per_passenger' => true,
                'importance' => 'required',
                'mime' => ['image/jpeg', 'image/png'],
                'max_size' => 3 * 1024 * 1024,
                'sort' => 2,
            ],
            [
                'title' => 'کارت ملی',
                'description' => 'اسکن پشت و روی کارت ملی',
                'category' => 'id_card',
                'required' => true,
                'per_passenger' => true,
                'importance' => 'required',
                'mime' => ['image/jpeg', 'image/png', 'application/pdf'],
                'max_size' => 3 * 1024 * 1024,
                'sort' => 3,
            ],
            [
                'title' => 'شناسنامه',
                'description' => 'اسکن تمام صفحات شناسنامه',
                'category' => 'general',
                'required' => true,
                'per_passenger' => true,
                'importance' => 'required',
                'mime' => ['image/jpeg', 'image/png', 'application/pdf'],
                'max_size' => 5 * 1024 * 1024,
                'sort' => 4,
            ],
            [
                'title' => 'پرینت حساب بانکی',
                'description' => 'پرینت ۳ ماه آخر حساب با حداقل موجودی ۵۰ میلیون تومان',
                'category' => 'bank_statement',
                'required' => true,
                'per_passenger' => false,
                'importance' => 'required',
                'mime' => ['application/pdf'],
                'max_size' => 10 * 1024 * 1024,
                'sort' => 5,
            ],
            [
                'title' => 'گواهی اشتغال به کار',
                'description' => 'گواهی با ذکر سمت، تاریخ استخدام و حقوق',
                'category' => 'general',
                'required' => false,
                'per_passenger' => false,
                'importance' => 'recommended',
                'mime' => ['application/pdf', 'image/jpeg'],
                'max_size' => 5 * 1024 * 1024,
                'sort' => 6,
            ],
            [
                'title' => 'بیمه مسافرتی',
                'description' => 'بیمه مسافرتی با پوشش حداقل ۳۰,۰۰۰ یورو',
                'category' => 'insurance',
                'required' => true,
                'per_passenger' => true,
                'importance' => 'required',
                'mime' => ['application/pdf'],
                'max_size' => 5 * 1024 * 1024,
                'sort' => 7,
            ],
            [
                'title' => 'واچر هتل',
                'description' => 'تأییدیه رزرو هتل برای کل مدت اقامت',
                'category' => 'voucher',
                'required' => false,
                'per_passenger' => false,
                'importance' => 'recommended',
                'mime' => ['application/pdf'],
                'max_size' => 5 * 1024 * 1024,
                'sort' => 8,
            ],
        ];

        // برای همه ویزاهای توریستی چک‌لیست پیش‌فرض اضافه کن
        $touristVisas = VisaType::where('category', VisaCategory::TOURIST)->get();
        $count = 0;
        
        foreach ($touristVisas as $visa) {
            foreach ($touristChecklist as $item) {
                VisaDocumentChecklist::updateOrCreate(
                    ['visa_type_id' => $visa->id, 'title' => $item['title']],
                    [
                        'description' => $item['description'],
                        'document_category' => $item['category'],
                        'allowed_mime_types' => $item['mime'],
                        'max_file_size' => $item['max_size'],
                        'max_files' => 1,
                        'accept_multiple' => false,
                        'is_required' => $item['required'],
                        'is_per_passenger' => $item['per_passenger'],
                        'importance' => $item['importance'],
                        'sort_order' => $item['sort'],
                    ]
                );
                $count++;
            }
        }

        $this->command?->info("✅ {$count} checklist items seeded for tourist visas");
    }

    private function seedRules(): void
    {
        $defaultRules = [
            ['key' => 'passport_validity', 'title' => 'اعتبار پاسپورت', 'desc' => 'پاسپورت باید حداقل ۶ ماه از تاریخ ورود اعتبار داشته باشد', 'importance' => 'critical', 'mandatory' => true, 'value' => '6_months'],
            ['key' => 'blank_pages', 'title' => 'صفحات خالی', 'desc' => 'حداقل دو صفحه خالی کنار هم در پاسپورت', 'importance' => 'warning', 'mandatory' => true, 'value' => '2_blank_pages'],
            ['key' => 'photo_spec', 'title' => 'مشخصات عکس', 'desc' => 'عکس جدید (کمتر از ۶ ماه) با زمینه سفید', 'importance' => 'info', 'mandatory' => true, 'value' => 'recent_6_months'],
            ['key' => 'bank_balance', 'title' => 'موجودی بانکی', 'desc' => 'حداقل موجودی لازم برای هر نفر', 'importance' => 'warning', 'mandatory' => false, 'value' => 'min_50_million_toman'],
        ];

        $count = 0;
        foreach (VisaType::all() as $visa) {
            foreach ($defaultRules as $rule) {
                VisaRule::updateOrCreate(
                    ['visa_type_id' => $visa->id, 'rule_key' => $rule['key']],
                    [
                        'title' => $rule['title'],
                        'description' => $rule['desc'],
                        'value_type' => 'text',
                        'value' => $rule['value'],
                        'importance' => $rule['importance'],
                        'is_mandatory' => $rule['mandatory'],
                        'sort_order' => 0,
                    ]
                );
                $count++;
            }
        }

        $this->command?->info("✅ {$count} visa rules seeded");
    }
}
