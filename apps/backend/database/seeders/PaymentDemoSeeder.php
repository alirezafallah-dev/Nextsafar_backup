<?php
namespace Database\Seeders;
use App\Models\User;
use App\Modules\Booking\Models\Booking;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class PaymentDemoSeeder extends Seeder {
    public function run(): void {
        $user = User::where('email','admin@nextsafar.com')->first() ?? User::first();
        if (!$user) { $this->command->warn('❌ کاربری نیست'); return; }
        $booking = Booking::where('user_id',$user->id)->first();
        if (!$booking) {
            $booking = Booking::create([
                'user_id'=>$user->id,'booking_code'=>'NS-'.strtoupper(Str::random(8)),
                'booking_type'=>'visa','status'=>'awaiting_payment','item_title'=>'ویزای ترکیه - توریستی',
                'total_amount'=>15000000,'discount_amount'=>0,'passenger_count'=>1,
            ]);
        }
        $samples = [
            ['status'=>'success','gateway'=>'zarinpal','amount'=>15000000,'currency'=>'IRR'],
            ['status'=>'pending','gateway'=>'zarinpal','amount'=>8500000,'currency'=>'IRR'],
            ['status'=>'failed','gateway'=>'idpay','amount'=>12000000,'currency'=>'IRR'],
            ['status'=>'refunded','gateway'=>'zarinpal','amount'=>5000000,'currency'=>'IRR'],
        ];
        foreach ($samples as $i => $s) {
            Payment::create([
                'booking_id'=>$booking->id,'user_id'=>$user->id,'gateway'=>$s['gateway'],
                'amount'=>$s['amount'],'currency'=>$s['currency'],'status'=>$s['status'],
                'authority_code'=>Str::random(32),'transaction_id'=>'TXN-'.strtoupper(Str::random(12)),
                'reference_id'=>$s['status']==='success'?Str::random(20):null,
                'card_number'=>$s['status']==='success'?'6037-9911-2233-445'.$i:null,
                'card_hash'=>$s['status']==='success'?hash('sha256','card-'.$i):null,
                'ip_address'=>'185.116.90.'.(10+$i),
                'user_agent'=>'Mozilla/5.0 (X11; Linux x86_64) Chrome/155.0.0.0',
                'gateway_request'=>['amount'=>$s['amount'],'callback'=>'https://api.nextsafar.local/payment/callback'],
                'gateway_response'=>$s['status']==='success'?['Status'=>100,'Authority'=>Str::random(32)]:['Status'=>-1,'Message'=>'تراکنش ناموفق'],
                'callback_data'=>$s['status']==='success'?['RefId'=>Str::random(20)]:null,
                'description'=>'پرداخت '.$booking->item_title,
                'paid_at'=>$s['status']==='success'?now()->subDays($i):null,
            ]);
        }
        $this->command->info('✅ 4 پرداخت نمونه ساخته شد.');
    }
}
