<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class PreorderController extends BaseController {
  public function store(Request $r) {
    $this->validate($r, [
      'product_type' => 'required|in:course,ebook',
      'product_id'   => 'required|integer',
      'name'         => 'required|string|max:120',
      'email'        => 'required|email',
      'mobile'       => 'required|string|max:20',
      'whatsapp'     => 'nullable|string|max:20'
    ]);

    if ($r->product_type === 'course') {
      $product = DB::table('courses')->where('id', $r->product_id)->whereNull('deleted_at')->first();
      if (!$product) {
        return response()->json(['error' => 'Course not found'], 404);
      }
    } else {
      return response()->json(['error' => 'Ebook support not yet implemented'], 400);
    }

    $amount = $product->price;
    $currency = $product->currency;

    $poId = DB::table('preorders')->insertGetId([
      'product_type'=>$r->product_type,
      'product_id'=>$r->product_id,
      'name'=>$r->name,
      'email'=>$r->email,
      'mobile'=>$r->mobile,
      'whatsapp'=>$r->whatsapp ?: $r->mobile,
      'amount'=>$amount,
      'currency'=>$currency,
      'status'=>'created',
      'ip'=>$r->ip(),
      'ua'=>$r->userAgent(),
      'created_at'=>now(),
      'updated_at'=>now()
    ]);

    $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    $order = $api->order->create([
      'amount'=>$amount,
      'currency'=>$currency,
      'receipt'=>(string)$poId,
      'notes'=>[
        'product_type'=>$r->product_type,
        'product_id'=>$r->product_id,
        'email'=>$r->email
      ]
    ]);

    DB::table('preorders')->where('id',$poId)->update(['razorpay_order_id'=>$order['id']]);

    return response()->json([
      'preorder_id'=>$poId,
      'razorpay_order_id'=>$order['id'],
      'amount'=>$order['amount'],
      'currency'=>$order['currency'],
      'key_id'=>env('RAZORPAY_KEY_ID')
    ]);
  }
}
