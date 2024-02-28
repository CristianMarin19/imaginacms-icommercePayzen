<?php

use Illuminate\Routing\Router;

$router->group(['prefix'=>'icommercepayzen'],function (Router $router){
       
    $router->get('/{eUrl}', [
        'as' => 'icommercepayzen',
        'uses' => 'PublicController@index',
    ]);

    $router->get('/payment/response/{orderId}', [
        'as' => 'icommercepayzen.response',
        'uses' => 'PublicController@response',
    ]);
  
       
});