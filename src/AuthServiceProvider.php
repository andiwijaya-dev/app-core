<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Middleware\AuthMiddleware;
use Andiwijaya\AppCore\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton('Auth', function () {
      return new AuthService();
    });
  }

  public function provides()
  {
    return [ 'Auth' ];
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(Request $request, ResponseFactory $response){

    $this->app['router']->aliasMiddleware('auth.web', AuthMiddleware::class);

    $this->publishes(
      [
        __DIR__ . '/database/auth/' => database_path(),
        __DIR__.'/views/auth/' => resource_path('views'),
        __DIR__.'/config/auth/' => config_path(),
      ],
      'auth'
    );
  }

}