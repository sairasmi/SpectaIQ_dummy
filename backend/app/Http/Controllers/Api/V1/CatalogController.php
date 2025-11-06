<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends BaseController {
  public function index(Request $r) {
    $q = DB::table('courses')->select('id','title','summary','price','currency','cover_url','status')->whereNull('deleted_at');
    if ($r->q) $q->where('title','like','%'.$r->q.'%');
    return response()->json(['data'=>$q->orderBy('id','desc')->limit(50)->get()]);
  }
  
  public function show($id) {
    $c = DB::table('courses')->where('id',$id)->whereNull('deleted_at')->first();
    if (!$c) return response()->json(['error'=>'Not found'],404);
    return response()->json($c);
  }
}
