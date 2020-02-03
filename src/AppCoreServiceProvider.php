<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\WebCache\Console\Commands\WebCacheClear;
use Andiwijaya\WebCache\Facades\WebCache;
use Andiwijaya\WebCache\Http\Middleware\WebCacheMiddleware;
use Andiwijaya\WebCache\Http\Middleware\WebCacheMiddlewareDisabled;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;

class AppCoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {



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

      $this->loadViewsFrom(__DIR__ . '/views', 'andiwijaya');

      $this->loadMigrationsFrom(__DIR__.'/database/migrations');

      $this->loadRoutesFrom(__DIR__.'/routes.php');

      $this->publishes([
        __DIR__.'/public' => public_path('vendor/andiwijaya')
      ], 'public');

    }

}