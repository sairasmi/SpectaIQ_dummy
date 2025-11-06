<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class OrdersController extends BaseController {
  public function status($orderId) {
    $po = DB::table('preorders')->where('razorpay_order_id',$orderId)->first();
    if (!$po) return response()->json(['error'=>'Not found'],404);
    return response()->json(['state'=>$po->status]);
  }
}
