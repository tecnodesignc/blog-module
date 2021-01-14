<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/blog/v1'], function (Router $router) {
    require('ApiRoutes/categoryApiRoutes.php');
    require('ApiRoutes/postApiRoutes.php');

});
