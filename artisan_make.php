<?php
// one-time generator to create skeleton files
$base = __DIR__ . '/backend';
@mkdir("$base/app/Http/Controllers/Api/V1", 0777, true);
@mkdir("$base/app/Middleware", 0777, true);
@mkdir("$base/database/migrations", 0777, true);
@mkdir("$base/routes", 0777, true);

// routes
$r = <<<'PHP'
<?php

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    // CORS preflight
    $router->options('{any:.*}', function () {
        return response('OK', 200);
    });
    
    // Catalog
    $router->get('courses', 'Api\V1\CatalogController@index');
    $router->get('courses/{id}', 'Api\V1\CatalogController@show');

    // Preorder + Razorpay
    $router->post('preorders', 'Api\V1\PreorderController@store');
    $router->get('orders/{orderId}/status', 'Api\V1\OrdersController@status');
    $router->post('payments/webhook/razorpay', 'Api\V1\WebhookController@razorpay');

    // Certificates
    $router->get('certificates/{certNumber}', 'Api\V1\CertificatesController@verify');

    // Authenticated (JWT)
    $router->group(['middleware' => 'jwt'], function () use ($router) {
        $router->get('me/dashboard', 'Api\V1\MeController@dashboard');
        $router->get('lessons/{id}/content', 'Api\V1\LessonsController@content');
        $router->post('progress', 'Api\V1\LessonsController@progress');
    });
});
PHP;
file_put_contents("$base/routes/web.php", $r);

// public/index.php for Lumen
$pub = <<<'PHP'
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

$app = require __DIR__.'/../bootstrap/app.php';

$app->routeMiddleware([
  'jwt' => App\Http\Middleware\JwtMiddleware::class
]);

$app->run();
PHP;
if (!is_dir("$base/public")) mkdir("$base/public",0777,true);
file_put_contents("$base/public/index.php",$pub);

// bootstrap/app.php tweaks for routing file
$boot = file_get_contents("$base/bootstrap/app.php");
if (strpos($boot,"withFacades")===false) {
  $lines = explode("\n", $boot);
  $newLines = [];
  foreach ($lines as $line) {
    $newLines[] = $line;
    if (strpos($line, '$app = new') !== false && strpos($line, 'Application') !== false) {
      $newLines[] = "";
      $newLines[] = '$app->withFacades();';
      $newLines[] = '$app->withEloquent();';
    }
  }
  $boot = implode("\n", $newLines);
}
if (strpos($boot,"$app->router->group")===false) {
  $boot .= "\n\n\$app->router->group(['namespace' => 'App\\Http\\Controllers'], function (\$router) {\n    require __DIR__.'/../routes/web.php';\n});\n\nreturn \$app;\n";
} else if (strpos($boot, "return \$app;") === false) {
  $boot .= "\nreturn \$app;\n";
}
file_put_contents("$base/bootstrap/app.php", $boot);

echo "Artisan make complete!\n";
