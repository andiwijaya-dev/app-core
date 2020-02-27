<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\WebCacheClear;
use Andiwijaya\AppCore\Middleware\WebCacheMiddleware;
use Andiwijaya\AppCore\Services\WebCacheService;
use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppCoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton('WebCache', function () {
        return new WebCacheService();
      });

      $this->commands([
        WebCacheClear::class
      ]);
    }

    public function provides()
    {
      return [ 'WebCache' ];
    }

  /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(){

      if(!$this->app->runningInConsole() &&
        $this->app->request->method() == 'GET' &&
        Cache::has(WebCache::getKey($this->app->request))){
        global $kernel, $request;
        $response = Response::create(Cache::get(WebCache::getKey($this->app->request)));
        $response->send();
        $kernel->terminate($request, $response);
        exit();
      }

      $this->loadViewsFrom(__DIR__ . '/views', 'andiwijaya');

      //$this->loadMigrationsFrom(__DIR__.'/database/migrations');

      $this->loadRoutesFrom(__DIR__.'/routes.php');

      $this->app['router']->aliasMiddleware('web-cache', WebCacheMiddleware::class);

      $this->publishes(
        [
          __DIR__.'/public' => public_path('vendor/andiwijaya')
        ],
        'assets'
      );

      $this->publishes(
        [
          __DIR__.'/database/migrations' => app_path('database/migrations')
        ],
        'migrations'
      );

    }

}