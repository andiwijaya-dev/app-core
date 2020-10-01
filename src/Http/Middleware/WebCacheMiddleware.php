<?php

namespace Andiwijaya\AppCore\Middleware;

use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Support\Facades\Route;

class WebCacheMiddleware{

  public function handle($request, $next){

    $response = $next($request);

    if(config('webcache.enabled') &&
      in_array($request->getHttpHost(), config('webcache.hosts', [])) &&
      $request->method() == 'GET' &&
      isset(($route = $request->route())->action['middleware']) && is_array($route->action['middleware']) &&
      !in_array('web-cache-excluded', $route->action['middleware'])){

      WebCache::store($request, $response);
    }

    return $response;
  }

}