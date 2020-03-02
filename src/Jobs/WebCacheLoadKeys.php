<?php

namespace Andiwijaya\AppCore\Jobs;

use Andiwijaya\AppCore\Models\WebCache;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Ixudra\Curl\Facades\Curl;

class WebCacheLoadKeys implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $keys;


    public function __construct($keys){

      $this->keys = $keys;

    }

    public function handle(){

      foreach($this->keys as $key)
        $this->handleOne($key);

    }

    private function handleOne($key){

      list($method, $url, $type, $device) = explode(' ', $key);

      $user_agent = $device == 'mobile' ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1' :
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

      Curl::to($url)
        ->withHeader("User-Agent: {$user_agent}")
        ->get();

    }

}
