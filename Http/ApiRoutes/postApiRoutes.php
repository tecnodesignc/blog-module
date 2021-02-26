<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'posts'], function (Router $router) {
    //Route create
    $router->post('/', [
        'as' => 'api.blog.post.create',
        'uses' => 'PostApiController@create',
        'middleware' => ['auth:api']
    ]);

    //Route index
    $router->get('/', [
        'as' => 'api.blog.post.index',
        'uses' => 'PostApiController@index',
        //'middleware' => ['auth:api']
    ]);

    //Route show
    $router->get('/{criteria}', [
        'as' => 'api.blog.post.show',
        'uses' => 'PostApiController@show',
        //'middleware' => ['auth:api']
    ]);

    //Route update
    $router->put('/{criteria}', [
        'as' => 'api.blog.post.update',
        'uses' => 'PostApiController@update',
        'middleware' => ['auth:api']
    ]);

    //Route delete
    $router->delete('/{criteria}', [
        'as' => 'api.blog.post.delete',
        'uses' => 'PostApiController@delete',
        'middleware' => ['auth:api']
    ]);

});
