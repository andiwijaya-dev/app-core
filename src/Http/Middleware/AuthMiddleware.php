<?php

namespace Andiwijaya\AppCore\Middleware;

use Andiwijaya\AppCore\Facades\Auth;
use Andiwijaya\AppCore\Facades\WebCache;

class AuthMiddleware{

  public function handle($request, $next){

    try{

      Auth::load();

      if(Auth::user()->require_password_change && $request->path() != 'set-password')
        return redirect('set-password');
    }
    catch(\Exception $ex){

      return redirect('/login');
    }

    return $next($request);

  }

}