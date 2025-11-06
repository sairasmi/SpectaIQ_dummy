<?php
namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeController extends BaseController {
  public function dashboard(Request $request) {
    $userId = $request->attributes->get('jwt_user_id');
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $user = DB::table('users')->where('id', $userId)->first();
    $enrollments = DB::table('enrollments')
      ->join('courses', 'enrollments.course_id', '=', 'courses.id')
      ->where('enrollments.user_id', $userId)
      ->select('courses.*', 'enrollments.status', 'enrollments.started_at')
      ->get();
    
    return response()->json([
      'user' => $user,
      'enrollments' => $enrollments
    ]);
  }
}
