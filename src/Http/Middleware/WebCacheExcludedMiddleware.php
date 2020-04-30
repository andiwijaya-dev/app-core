<?php

namespace Andiwijaya\AppCore\Middleware;

use Andiwijaya\AppCore\Facades\WebCache;

class WebCacheExcludedMiddleware{

  public function handle($request, $next){

    return $next($request);

  }

}