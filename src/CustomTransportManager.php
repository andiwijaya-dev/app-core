<?php

namespace Andiwijaya\AppCore;

use Andiwijaya\AppCore\Models\Config;
use Illuminate\Mail\TransportManager;

class CustomTransportManager extends TransportManager{

  public function __construct($app)
  {
    $this->app = $app;

    if($config = Config::where('key', 'smtp')->first()){

      $this->app['config']['mail'] = [
        'driver'        => config('mail.driver'),
        'host'          => $config->value['host'] ?? config('mail.host'),
        'port'          => $config->value['port'] ?? config('mail.port'),
        'from'          => [
          'address'   => config('mail.from.address'),
          'name'      => config('mail.from.name')
        ],
        'encryption'    => config('mail.encryption'),
        'username'      => $config->value['username'] ?? config('mail.username'),
        'password'      => $config->value['password'] ?? config('mail.password'),
        'sendmail'      => config('mail.sendmail'),
        'pretend'       => config('mail.pretend')
      ];
    }
  }

}