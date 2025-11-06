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