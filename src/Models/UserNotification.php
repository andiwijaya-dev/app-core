<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class UserNotification extends Model{

  protected $table = 'user_notification';

  protected $fillable = [ 'status', 'title', 'body', 'target', 'user_id' ];

  protected $attributes = [
    'status'=>self::STATUS_UNREAD
  ];

  const STATUS_UNREAD = 1;
  const STATUS_READ = 0;

  public function user(){

    return $this->hasOne('Andiwijaya\AppCore\Models\User', 'id', 'user_id');

  }


  public function save(array $options = [])
  {
    $return = parent::save($options);

    $this->pushNotification();

    return $return;
  }

  public function pushNotification(){

    if(redis_available()) {

      $currentCursor = '0';
      $k = [];
      do {
        $response = Redis::scan($currentCursor, 'MATCH', 'notification*', 'COUNT', 100);
        $currentCursor = $response[0];
        $k = array_merge($k, $response[1]);
      } while ($currentCursor !== '0');
      exc($k);


      Redis::publish('notification-1', json_encode($this));

      /*$keys = Redis::keys('name');

      foreach ($keys as $key){
        \Illuminate\Support\Facades\Log::info("Push notification to {$key}, title: {$this->title}");

        Redis::publish($key, json_encode($this));
      }*/

    }

  }

}