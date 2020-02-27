<?php

namespace Andiwijaya\AppCore\Middleware;

use Andiwijaya\AppCore\Facades\WebCache;

class WebCacheMiddleware{

  public function handle($request, $next){

    $response = $next($request);

    WebCache::store($request, $response);

    return $response;

  }

}