<?php

namespace Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Blog\Entities\Category;
use Modules\Blog\Entities\Post;
use Modules\Blog\Events\Handlers\RegisterBlogSidebar;
use Modules\Blog\Repositories\Cache\CacheCategoryDecorator;
use Modules\Blog\Repositories\Cache\CachePostDecorator;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Blog\Repositories\Eloquent\EloquentCategoryRepository;
use Modules\Blog\Repositories\Eloquent\EloquentPostRepository;
use Modules\Blog\Repositories\PostRepository;
use Modules\Blog\Services\FinderService;
use Modules\Core\Events\CollectingAssets;
use Modules\Core\Traits\CanGetSidebarClassForModule;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Tag\Repositories\TagManager;
use Illuminate\Support\Arr;


class BlogServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration, CanGetSidebarClassForModule;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(
            BuildingSidebar::class,
            $this->getSidebarClassForModule('blog', RegisterBlogSidebar::class)
        );

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('posts', Arr::dot(trans('blog::posts')));
            $event->load('categories', Arr::dot(trans('blog::categories')));
            // append translations

        });


    }

    public function boot()
    {
        $this->publishConfig('blog', 'config');
        $this->publishConfig('blog', 'permissions');
        $this->publishConfig('blog', 'settings');
        //$this->app[TagManager::class]->registerNamespace(new Post());
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->handleAssets();


    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function registerBindings()
    {

        $this->app->bind(FinderService::class, function () {
            return new FinderService();
        });


        $this->app->bind(
            PostRepository::class,
            function () {
                $repository = new EloquentPostRepository(new Post());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new CachePostDecorator($repository);
            }
        );
        $this->app->bind(
            CategoryRepository::class,
            function () {
                $repository = new EloquentCategoryRepository(new Category());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new CacheCategoryDecorator($repository);
            }
        );
// add bindings
    }

    /**
     * Require iCheck on edit and create pages
     */
    private function handleAssets()
    {
        $this->app['events']->listen(CollectingAssets::class, function (CollectingAssets $event) {
            if ($event->onRoutes(['*post*create', '*post*edit'])) {
                $event->requireCss('icheck.blue.css');
                $event->requireJs('icheck.js');
            }
        });
    }
}
