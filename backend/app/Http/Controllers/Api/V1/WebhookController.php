<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WebhookController extends BaseController {
  public function razorpay(Request $r) {
    $payload = $r->getContent();
    $sig = $r->header('X-Razorpay-Signature');
    $secret = env('RAZORPAY_WEBHOOK_SECRET');
    
    if (!$secret) {
      \Log::error('RAZORPAY_WEBHOOK_SECRET not configured');
      return response()->json(['ok'=>false, 'error' => 'Server configuration error'], 500);
    }
    
    $expected = hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($expected, $sig)) {
      return response()->json(['ok'=>false, 'error' => 'Invalid signature'],400);
    }

    $event = json_decode($payload,true);
    $orderId = data_get($event,'payload.payment.entity.order_id');
    $paymentId = data_get($event,'payload.payment.entity.id');
    $status = data_get($event,'payload.payment.entity.status');

    if (!$orderId || $status !== 'captured') {
      return response()->json(['ok'=>true]);
    }

    return DB::transaction(function() use ($orderId,$paymentId,$event) {
      $po = DB::table('preorders')->where('razorpay_order_id',$orderId)->lockForUpdate()->first();
      if (!$po) return response()->json(['ok'=>true]);
      if ($po->status === 'paid') return response()->json(['ok'=>true]);

      $webhookAmount = data_get($event,'payload.payment.entity.amount');
      $webhookCurrency = data_get($event,'payload.payment.entity.currency');
      
      if ($webhookAmount != $po->amount || strtoupper($webhookCurrency) != strtoupper($po->currency)) {
        \Log::error('Webhook amount mismatch', [
          'expected' => ['amount' => $po->amount, 'currency' => $po->currency],
          'received' => ['amount' => $webhookAmount, 'currency' => $webhookCurrency]
        ]);
        return response()->json(['ok'=>false, 'error' => 'Amount mismatch'], 400);
      }

      $user = DB::table('users')->where('email',$po->email)->first();
      $pw = null;
      if (!$user) {
        $pw = bin2hex(random_bytes(6));
        $uid = DB::table('users')->insertGetId([
          'name'=>$po->name,
          'email'=>$po->email,
          'phone'=>$po->mobile,
          'whatsapp'=>$po->whatsapp,
          'password_hash'=>password_hash($pw, PASSWORD_BCRYPT),
          'role'=>'student',
          'created_at'=>now(),
          'updated_at'=>now()
        ]);
        $user = DB::table('users')->where('id',$uid)->first();
      }

      if ($po->product_type === 'course') {
        DB::table('enrollments')->updateOrInsert(
          ['user_id'=>$user->id, 'course_id'=>$po->product_id],
          ['status'=>'active','started_at'=>now(),'updated_at'=>now()]
        );
      } else {
        DB::table('entitlements')->updateOrInsert(
          ['user_id'=>$user->id,'ebook_id'=>$po->product_id],
          ['status'=>'active','granted_at'=>now(),'updated_at'=>now()]
        );
      }

      DB::table('payments')->updateOrInsert(
        ['provider'=>'razorpay','provider_order_id'=>$orderId],
        [
          'preorder_id'=>$po->id,
          'provider_payment_id'=>$paymentId,
          'status'=>'captured',
          'amount'=>$po->amount,
          'currency'=>$po->currency,
          'webhook_payload'=>json_encode($event),
          'updated_at'=>now(),
          'created_at'=>now()
        ]
      );

      DB::table('preorders')->where('id',$po->id)->update(['status'=>'paid','updated_at'=>now()]);

      try {
        $msg = "Welcome to Elearn!\nEmail: {$user->email}";
        if ($pw) $msg .= "\nPassword: $pw";
        Mail::raw($msg, function ($m) use ($user) {
          $m->to($user->email)->subject('Your access is ready');
        });
      } catch (\Throwable $e) {}

      return response()->json(['ok'=>true]);
    });
  }
}
