<?php

namespace Andiwijaya\AppCore\Jobs;

use Andiwijaya\AppCore\Models\WebCache;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Ixudra\Curl\Facades\Curl;

class WebCacheLoad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type, $key;

    const TYPE_ALL = 1;
    const TYPE_KEY = 2;
    const TYPE_TAG = 3;


    public function __construct($type, $key = null){

      $this->type = $type;
      $this->key = $key;

    }

    public function handle(){

      switch($this->type){

        case self::TYPE_ALL:
          $this->handleAll();
          break;

        case self::TYPE_KEY:
          $this->handleKey($this->key);
          break;

        case self::TYPE_TAG:
          $this->handleTag($this->key);
          break;

      }

    }

    public function handleAll(){

      WebCache::chunkById(30, function($items){
        $keys = [];
        foreach($items as $item)
          $keys[] = $item->key;
        WebCacheLoadKeys::dispatch($keys);
      });

    }

    public function handleKey($key){

      WebCache::where('key', $key)
        ->chunkById(30, function($items){
          $keys = [];
          foreach($items as $item)
            $keys[] = $item->key;
          WebCacheLoadKeys::dispatch($keys);
        });

    }

    public function handleTag($tag){

      WebCache::search($tag)
        ->chunkById(30, function($items){
          $keys = [];
          foreach($items as $item)
            $keys[] = $item->key;
          WebCacheLoadKeys::dispatch($keys);
        });

    }

}
