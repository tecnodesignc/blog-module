<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'categories'], function (Router $router) {

    //Route create
    $router->post('/', [
        'as' => 'api.blog.category.create',
        'uses' => 'CategoryApiController@create',
        'middleware' => ['auth:api']
    ]);

    //Route index
    $router->get('/', [
        'as' => 'api.blog.category.get.items.by',
        'uses' => 'CategoryApiController@index',
        //'middleware' => ['auth:api']
    ]);

    //Route show
    $router->get('/{criteria}', [
        'as' => 'api.blog.category.get.item',
        'uses' => 'CategoryApiController@show',
        //'middleware' => ['auth:api']
    ]);

    //Route update
    $router->put('/{criteria}', [
        'as' => 'api.blog.category.update',
        'uses' => 'CategoryApiController@update',
        'middleware' => ['auth:api']
    ]);

    //Route delete
    $router->delete('/{criteria}', [
        'as' => 'api.blog.category.delete',
        'uses' => 'CategoryApiController@delete',
        'middleware' => ['auth:api']
    ]);
});
