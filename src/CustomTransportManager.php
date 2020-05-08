<?php

namespace Andiwijaya\AppCore;

use Andiwijaya\AppCore\Models\Setting;
use Illuminate\Mail\TransportManager;

class CustomTransportManager extends TransportManager{

  public function __construct($app)
  {
    $this->app = $app;

    if($setting = Setting::where('key', 'smtp')->first()){

      $this->app['config']['mail'] = [
        'driver'        => config('mail.driver'),
        'host'          => $setting->value['host'] ?? config('mail.host'),
        'port'          => $setting->value['port'] ?? config('mail.port'),
        'from'          => [
          'address'   => config('mail.from.address'),
          'name'      => config('mail.from.name')
        ],
        'encryption'    => config('mail.encryption'),
        'username'      => $setting->value['username'] ?? config('mail.username'),
        'password'      => $setting->value['password'] ?? config('mail.password'),
        'sendmail'      => config('mail.sendmail'),
        'pretend'       => config('mail.pretend')
      ];
    }
  }

}