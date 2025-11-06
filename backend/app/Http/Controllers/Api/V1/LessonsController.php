<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class LessonsController extends BaseController {
  public function content($id) { 
    return response()->json(['url'=>'SIGNED_URL_PLACEHOLDER', 'lesson_id' => $id]);
  }
  
  public function progress(Request $request) { 
    return response()->json(['ok'=>true]);
  }
}
