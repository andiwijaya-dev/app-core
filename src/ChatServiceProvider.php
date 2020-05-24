<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\ChatDiscussionNotifyUnsent;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->commands([
      ChatDiscussionNotifyUnsent::class
    ]);
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(){

    $this->publishes([ __DIR__.'/database/chat/' => database_path() ], 'chat');
  }

}