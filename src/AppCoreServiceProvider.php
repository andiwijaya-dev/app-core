<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\ModelExecute;
use Andiwijaya\AppCore\Console\Commands\Ping;
use Andiwijaya\AppCore\Console\Commands\ScheduledTaskRun;
use Andiwijaya\AppCore\Console\Commands\TestEmail;
use Andiwijaya\AppCore\Exceptions\Handler;
use Andiwijaya\AppCore\Responses\HTMLResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
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
    $this->commands([
      ModelExecute::class,
      TestEmail::class,
      Ping::class,
      ScheduledTaskRun::class
    ]);

    $this->app->singleton(
      ExceptionHandler::class,
      Handler::class
    );
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(){

    $this->loadViewsFrom(__DIR__ . '/views', 'andiwijaya');
    $this->loadViewsFrom(storage_path('app'), 'app');

    $this->publishes(
      [
        __DIR__.'/database/default/' => database_path(),
        __DIR__.'/lang/' => resource_path('lang'),
        __DIR__.'/websocket/' => base_path(),
      ]
    );

    Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
      $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
    });
  }

}