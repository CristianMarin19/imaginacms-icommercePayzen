<?php

namespace Modules\Icommercepayzen\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Icommercepayzen\Events\Handlers\RegisterIcommercepayzenSidebar;

class IcommercepayzenServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterIcommercepayzenSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('icommercepayzens', Arr::dot(trans('icommercepayzen::icommercepayzens')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('icommercepayzen', 'permissions');
        $this->publishConfig('icommercepayzen', 'config');
        $this->publishConfig('icommercepayzen', 'crud-fields');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Icommercepayzen\Repositories\IcommercePayzenRepository',
            function () {
                $repository = new \Modules\Icommercepayzen\Repositories\Eloquent\EloquentIcommercePayzenRepository(new \Modules\Icommercepayzen\Entities\IcommercePayzen());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercepayzen\Repositories\Cache\CacheIcommercePayzenDecorator($repository);
            }
        );
// add bindings

    }

    
}
