<?php
namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware {
  public function handle($request, Closure $next) {
    $auth = $request->header('Authorization');
    if (!$auth || !preg_match('/Bearer\s+(.+)/',$auth,$m)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    try {
      $token = $m[1];
      $secret = env('JWT_SECRET', env('APP_KEY'));
      if (!$secret) {
        return response()->json(['error' => 'Server configuration error'], 500);
      }
      $payload = JWT::decode($token, new Key($secret, 'HS256'));
      $request->attributes->set('jwt_user_id', $payload->sub ?? null);
    } catch (\Throwable $e) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    return $next($request);
  }
}
