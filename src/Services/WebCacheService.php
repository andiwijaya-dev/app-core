<?php

namespace Andiwijaya\AppCore\Services;


use Andiwijaya\AppCore\Models\WebCache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Agent\Agent;

class WebCacheService{

  protected $path;
  protected $tags = [];

  protected $agent;

  public function __construct()
  {
    $this->agent = new Agent();
  }

  public function getKey(Request $request){

    $device = $this->agent->isMobile() ? 'mobile' : ($this->agent->isTablet() ? 'tablet' : 'desktop');

    return implode(' ', [
      $request->method(),
      $request->fullUrl(),
      $request->wantsJson() ? 'json' : ($request->ajax() ? 'xhr' : 'normal'),
      $device
    ]);

  }

  public function tag($tag){

    $this->tags[] = $tag;

  }

  public function store(Request $request, Response $response){

    array_unshift($this->tags, $request->path());

    if(($response instanceof Response || $response instanceof JsonResponse)){

      $key = $this->getKey($request);

      Cache::forever($key, $response->content());

      if(Schema::hasTable('web_cache')){

        WebCache::updateOrCreate(
          [ 'key'=>$key ],
          [
            'tag'=>implode(' ', $this->tags)
          ]
        );

      }
      else{

        Log::warning("Table 'web_cache' doesn't exists, clear by key featured is not available.");

      }

    }
    else{

      Log::warning("Unable to create cache from response type of " . get_class($response));

    }

  }



  public function clearAll($clearDB = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      $count = WebCache::count();

      Artisan::call('cache:clear');

      if($clearDB)
        WebCache::truncate();

    }

    // Remove all caches

    //$count = count($cleared_keys); // Get count of cache cleared for returning purpose

    // Create jobs to reload cache url
    /*file_put_contents(storage_path('logs/web-cache.log'), "[CLEAR ALL @" . Carbon::now()->toDateTimeString() . "]" . PHP_EOL);
    do{
      $keys = array_splice($cleared_keys, 0, 3);
      WebCacheLoad::dispatch($keys);
    }
    while(count($cleared_keys) > 0);*/

    return $count;

  }

  public function clearByTag($tag, $clearDB = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      WebCache::search($tag)
        ->chunkById(1000, function($items) use(&$count){

          foreach($items as $item){

            Cache::forget($item->key);
            $count++;

          }

        });

      if($clearDB)
        WebCache::search($tag)->delete();

    }

    return $count;

  }

  public function clearByKey($key, $clearDB = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      WebCache::where('key', $key)
        ->chunkById(1000, function($items) use(&$count){

          foreach($items as $item){

            Cache::forget($item->key);
            $count++;

          }

        });

      if($clearDB)
        WebCache::where('key', $key)->delete();

    }

    return $count;

  }

}