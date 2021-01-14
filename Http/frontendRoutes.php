<?php

use Illuminate\Routing\Router;

$locale = LaravelLocalization::setLocale() ?: App::getLocale();

if (!App::runningInConsole()) {
    $categoryRepository = app('Modules\Blog\Repositories\CategoryRepository');
    $categories = $categoryRepository->getItemsBy(json_decode(json_encode(['filter' => [], 'include' => [], 'take' => null])));
    foreach ($categories as $category) {

        /** @var Router $router */
        $router->group(['prefix' => $category->slug], function (Router $router) use ($locale, $category) {

            $router->get('/', [
                'as' => $locale . '.blog.category.' . $category->slug,
                'uses' => 'PublicController@index',
                'middleware' => config('encore.blog.config.middleware'),
            ]);
            $router->get('{slug}', [
                'as' => $locale . '.blog.' . $category->slug . '.post',
                'uses' => 'PublicController@show',
                'middleware' => config('encore.blog.config.middleware'),
            ]);
        });
    }
}
/** @var Router $router */
$router->group(['prefix' => trans('blog::tag.uri')], function (Router $router) use ($locale) {
    $router->get('{slug}', [
        'as' => $locale . '.blog.tag.slug',
        'uses' => 'PublicController@tag',
        'middleware' => config('encore.blog.config.middleware'),
    ]);
});
