<?php

namespace Andiwijaya\AppCore\Services;


use Andiwijaya\AppCore\Jobs\WebCacheLoad;
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

  public function store(Request $request, $response){

    if($request->method() !== 'GET') return;

    array_unshift($this->tags, 'path:' . $request->path());

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



  public function clearAll($clearDB = false, $recache = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      $count = WebCache::count();

      Artisan::call('cache:clear');

      if($clearDB)
        WebCache::truncate();

      else if($recache)
        WebCacheLoad::dispatch(WebCacheLoad::TYPE_ALL);

    }

    return $count;

  }

  public function clearByTag($tag, $clearDB = false, $recache = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      WebCache::search($tag)
        ->chunkById(1000, function($items) use(&$count, $clearDB, $recache){

          foreach($items as $item){

            Cache::forget($item->key);
            $count++;

          }

        });

      if($clearDB)
        WebCache::search($tag)->delete();

      else if($recache)
        WebCacheLoad::dispatch(WebCacheLoad::TYPE_TAG, $tag);

    }

    return $count;

  }

  public function clearByKey($key, $clearDB = false, $recache = false){

    $count = 0;

    if(Schema::hasTable('web_cache')){

      WebCache::where('key', $key)
        ->chunkById(1000, function($items) use(&$count, $clearDB, $recache){

          // Forget keys from cache
          foreach($items as $item){
            Cache::forget($item->key);
            $count++;
          }

        });

      if($clearDB)
        WebCache::where('key', $key)->delete();

      else if($recache)
        WebCacheLoad::dispatch(WebCacheLoad::TYPE_KEY, $key);

    }

    return $count;

  }

}