<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\WebCacheClear;
use Andiwijaya\AppCore\Console\Commands\WebCacheLoad;
use Andiwijaya\AppCore\Middleware\WebCacheExcludedMiddleware;
use Andiwijaya\AppCore\Middleware\WebCacheMiddleware;
use Andiwijaya\AppCore\Services\WebCacheService;
use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class WebCacheServiceProvider extends ServiceProvider
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
      WebCacheClear::class,
      WebCacheLoad::class
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
  public function boot(Request $request){

    if(config('webcache.enabled') &&
      in_array($request->getHttpHost(), config('webcache.hosts', [])) &&
      !$this->app->runningInConsole() &&
      $request->method() == 'GET' &&
      !$request->has('webcache-reload')){

      if(Cache::has(WebCache::getKey($request))){

        global $kernel;

        $response = Response::create(Cache::get(WebCache::getKey($this->app->request)));
        $response->send();

        $kernel->terminate($request, $response);

        exit();

      }
    }

    $this->app['router']->aliasMiddleware('web-cache-excluded', WebCacheExcludedMiddleware::class);

    $this->app['router']->pushMiddlewareToGroup('web', WebCacheMiddleware::class);

    $this->publishes(
      [
        __DIR__.'/database/webcache/' => database_path(),
        __DIR__.'/config/webcache/' => config_path(),
      ],
      'webcache'
    );
  }

}