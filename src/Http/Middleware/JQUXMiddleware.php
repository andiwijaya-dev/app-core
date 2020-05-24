<?php

namespace Andiwijaya\AppCore\Middleware;

use Andiwijaya\AppCore\Facades\Auth;
use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Support\Facades\Session;

class JQUXMiddleware{

  public function handle($request, $next){

    // Convert money/number value
    if($request->method() == 'POST'){

      $post = $request->post();
      $filtered = [];
      $unsets = [];
      foreach($post as $key=>$value){

        if(strpos($key, '|') !== false){

          $keys = explode('|', $key);

          if(isset($keys[1]) && is_scalar($value)){
            switch($keys[1]){

              case 'money':
              case 'number':
                $value = str_replace(',', '', $value);
                break;

              case 'date':
                $value = date('Y-m-d', strtotime($value));
                break;
            }
          }

          $filtered[$keys[0]] = $value;
          $unsets[$key] = 1;
        }
      }

      $request->merge($filtered);

      foreach($unsets as $key=>$_)
        $request->offsetUnset($key);
    }

    return $next($request);
  }

}