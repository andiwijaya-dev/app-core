<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\ModelExecute;
use Andiwijaya\AppCore\Console\Commands\TestEmail;
use Andiwijaya\AppCore\Console\Commands\WebCacheClear;
use Andiwijaya\AppCore\Console\Commands\WebCacheLoad;
use Andiwijaya\AppCore\Middleware\AuthMiddleware;
use Andiwijaya\AppCore\Middleware\WebCacheExcludedMiddleware;
use Andiwijaya\AppCore\Middleware\WebCacheMiddleware;
use Andiwijaya\AppCore\Services\AuthService;
use Andiwijaya\AppCore\Services\WebCacheService;
use Andiwijaya\AppCore\Facades\WebCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Sheet;

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

    $this->app->singleton('Auth', function () {
      return new AuthService();
    });

    $this->commands([
      WebCacheClear::class,
      WebCacheLoad::class,
      ModelExecute::class,
      TestEmail::class
    ]);
  }

  public function provides()
  {
    return [ 'WebCache', 'Auth' ];
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(Request $request, ResponseFactory $response){

    if(env('WEB_HISTORY') && !$this->app->runningInConsole()){

      if(env('WEB_HISTORY_URL') == '/' . $request->path()){

        global $kernel;

        try{

          $session_id = Crypt::decrypt($request->cookies->get(strtolower(env('APP_NAME')) . '_session'), false);
          $type = $request->input('type');
          $path = $request->input('path');
          $referrer = $request->input('referrer', $request->input('referer'));
          $remote_ip = $request->server('REMOTE_ADDR');
          $user_agent = $request->server('HTTP_USER_AGENT');
          $created_at = Carbon::now()->format('Y-m-d H:i:s');

          if($type > 0 && !empty(trim($path))){
            DB::statement("INSERT INTO web_history(`type`, session_id, path, referrer, remote_ip, user_agent, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
              $type,
              $session_id,
              $path,
              $referrer,
              $remote_ip,
              $user_agent,
              $created_at,
              $created_at
            ]);
          }

        }
        catch(\Exception $ex){

          throw $ex;

        }

        $kernel->terminate($request, $response);

        exit();

      }

    }

    if(env('WEB_CACHE') &&
      env('WEB_CACHE_HOST') == $request->getHttpHost() &&
      !$this->app->runningInConsole() &&
      $this->app->request->method() == 'GET' &&
      !$request->has('webcache-reload'))
    {

      if(Cache::has(WebCache::getKey($request))){

        global $kernel;

        $response = Response::create(Cache::get(WebCache::getKey($this->app->request)));
        $response->send();

        $kernel->terminate($request, $response);

        exit();

      }

    }

    $this->loadViewsFrom(__DIR__ . '/views', 'andiwijaya');

    $this->loadViewsFrom(storage_path('app'), 'app');

    //$this->loadMigrationsFrom(__DIR__.'/database/migrations');

    $this->loadRoutesFrom(__DIR__.'/routes.php');

    $this->app['router']->aliasMiddleware('web-cache-excluded', WebCacheExcludedMiddleware::class);

    $this->app['router']->aliasMiddleware('auth.web', AuthMiddleware::class);

    $this->app['router']->pushMiddlewareToGroup('web', WebCacheMiddleware::class);

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

    Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
      $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
    });
  }

}