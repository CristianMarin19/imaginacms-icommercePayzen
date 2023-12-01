<?php

use Illuminate\Routing\Router;

$router->group(['prefix'=>'icommercepayzen'],function (Router $router){
       
    $router->get('/{eUrl}', [
        'as' => 'icommercepayzen',
        'uses' => 'PublicController@index',
    ]);
  
       
});