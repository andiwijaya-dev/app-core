<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\ModelExecute;
use Andiwijaya\AppCore\Console\Commands\WebCacheClear;
use Andiwijaya\AppCore\Middleware\WebCacheMiddleware;
use Andiwijaya\AppCore\Services\WebCacheService;
use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
        WebCacheClear::class,
        ModelExecute::class,
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
      
      if(env('WEB_HISTORY') && !$this->app->runningInConsole()){

        global $kernel, $request;

        if(env('WEB_HISTORY_URL') == '/' . $request->path()){

          try{

            $session_id = Crypt::decrypt($request->cookies->get(strtolower(env('APP_NAME')) . '_session'), false);
            $type = $request->get('type');
            $path = $request->get('path');
            $referer = $request->get('referer');
            $remote_ip = $request->server('REMOTE_ADDR');
            $user_agent = $request->server('HTTP_USER_AGENT');

            if($type > 0 && !empty(trim($path))){
              DB::statement("INSERT INTO web_history(`type`, session_id, path, referer, remote_ip, user_agent) VALUES (?, ?, ?, ?, ?, ?)", [
                $type,
                $session_id,
                $path,
                $referer,
                $remote_ip,
                $user_agent
              ]);
            }

          }
          catch(\Exception $ex){

            throw $ex;

          }

          $response = Response::create('');
          $response->send();
          $kernel->terminate($request, $response);
          exit();

        }

      }

      if(env('WEB_CACHE') &&
        !$this->app->runningInConsole() &&
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