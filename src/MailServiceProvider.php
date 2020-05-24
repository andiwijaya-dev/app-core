<?php

namespace Andiwijaya\AppCore;


class MailServiceProvider extends \Illuminate\Mail\MailServiceProvider
{
  protected function registerSwiftTransport()
  {
    $this->app->singleton('swift.transport', function () {
      return new CustomTransportManager($this->app);
    });
  }
}