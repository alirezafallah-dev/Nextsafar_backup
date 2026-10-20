<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // === دارایی‌ها (Assets) - مانده طبیعی: بدهکار ===
            ['code' => '1100', 'name' => 'موجودی نقد و بانک', 'name_en' => 'Cash and Bank', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 1],
            ['code' => '1110', 'name' => 'موجودی نزد درگاه‌ها', 'name_en' => 'Gateway Balance', 'type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1100', 'is_system' => true, 'sort_order' => 2],
            ['code' => '1200', 'name' => 'حساب‌های دریافتنی', 'name_en' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 3],
            
            // === بدهی‌ها (Liabilities) - مانده طبیعی: بستانکار ===
            ['code' => '2100', 'name' => 'حساب‌های پرداختنی', 'name_en' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 10],
            ['code' => '2110', 'name' => 'بدهی به پروایدرها', 'name_en' => 'Payable to Providers', 'type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2100', 'is_system' => true, 'sort_order' => 11],
            ['code' => '2200', 'name' => 'پیش‌دریافت از مشتریان', 'name_en' => 'Customer Advances', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 12],
            ['code' => '2300', 'name' => 'مالیات پرداختنی', 'name_en' => 'Tax Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 13],
            
            // === سرمایه (Equity) - مانده طبیعی: بستانکار ===
            ['code' => '3100', 'name' => 'سرمایه', 'name_en' => 'Capital', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 20],
            ['code' => '3200', 'name' => 'سود (زیان) انباشته', 'name_en' => 'Retained Earnings', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 21],
            
            // === درآمد (Revenue) - مانده طبیعی: بستانکار ===
            ['code' => '4100', 'name' => 'درآمد فروش تور', 'name_en' => 'Tour Sales Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 30],
            ['code' => '4110', 'name' => 'درآمد فروش هتل', 'name_en' => 'Hotel Sales Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 31],
            ['code' => '4120', 'name' => 'درآمد ویزا', 'name_en' => 'Visa Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 32],
            ['code' => '4200', 'name' => 'درآمد کارمزد', 'name_en' => 'Commission Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'sort_order' => 33],
            
            // === هزینه (Expense) - مانده طبیعی: بدهکار ===
            ['code' => '5100', 'name' => 'بهای تمام شده تور', 'name_en' => 'Tour COGS', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 40],
            ['code' => '5110', 'name' => 'بهای تمام شده هتل', 'name_en' => 'Hotel COGS', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 41],
            ['code' => '5200', 'name' => 'کارمزد درگاه', 'name_en' => 'Gateway Fees', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 42],
            ['code' => '5300', 'name' => 'هزینه استرداد', 'name_en' => 'Refund Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'sort_order' => 43],
        ];

        // ساخت حساب‌های پدر
        $parentMap = [];
        foreach ($accounts as $acc) {
            if (!isset($acc['parent_code'])) {
                $id = DB::table('ledger_accounts')->updateOrInsert(
                    ['code' => $acc['code']],
                    array_merge($acc, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
                $parentMap[$acc['code']] = DB::table('ledger_accounts')->where('code', $acc['code'])->value('id');
            }
        }

        // ساخت حساب‌های فرزند
        foreach ($accounts as $acc) {
            if (isset($acc['parent_code'])) {
                $parentId = $parentMap[$acc['parent_code']] ?? null;
                $data = $acc;
                unset($data['parent_code']);
                $data['parent_id'] = $parentId;
                $data['created_at'] = now();
                $data['updated_at'] = now();
                
                DB::table('ledger_accounts')->updateOrInsert(
                    ['code' => $acc['code']],
                    $data
                );
            }
        }

        $this->command?->info('✅ ' . count($accounts) . ' ledger accounts seeded.');
    }
}
