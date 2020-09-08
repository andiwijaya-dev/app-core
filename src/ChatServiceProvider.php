<?php

namespace Andiwijaya\AppCore;


use Andiwijaya\AppCore\Console\Commands\ChatDiscussionCheck;
use Andiwijaya\AppCore\Console\Commands\ChatDiscussionGreeting;
use Andiwijaya\AppCore\Console\Commands\ChatDiscussionNotifyUnsent;
use Andiwijaya\AppCore\Console\Commands\ChatDiscussionSendNotification;
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
      ChatDiscussionCheck::class,
      ChatDiscussionGreeting::class,
      ChatDiscussionNotifyUnsent::class,
      ChatDiscussionSendNotification::class
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