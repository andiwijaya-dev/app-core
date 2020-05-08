<?php

namespace Andiwijaya\AppCore;

use Illuminate\Mail\MailServiceProvider;

class CustomMailServiceProvider extends MailServiceProvider
{
  protected function registerSwiftTransport()
  {
    $this->app->singleton('swift.transport', function () {
      return new CustomTransportManager($this->app);
    });
  }
}