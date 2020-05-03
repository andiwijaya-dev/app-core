<?php

namespace Andiwijaya\AppCore\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade{

  protected static function getFacadeAccessor(){ return 'Auth'; }

}