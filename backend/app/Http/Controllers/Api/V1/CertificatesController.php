<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class CertificatesController extends BaseController {
  public function verify($certNumber) {
    $c = DB::table('certificates')->where('cert_number',$certNumber)->first();
    if(!$c) return response()->json(['valid'=>false],404);
    return response()->json(['valid'=>!$c->revoked_at,'data'=>$c]);
  }
}
