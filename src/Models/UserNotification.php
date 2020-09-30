<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class UserNotification extends Model{

  const TARGET_ALL = -1;
  const TARGET_ALL_ONLINE = -2;

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

    return $return;
  }


  public static function notify($user_id, $title, $description = '', $target = '', $timing = null, $persist = false){

    $title = str_replace('"', '', $title);
    $description = str_replace('"', '', $description);
    $target = str_replace('"', '', $target);

    if(redis_available()) {

      Redis::publish('user-notif-' . $user_id, json_encode([
        'script'=>"$.notify({ title:\"{$title}\", description:\"{$description}\", target:\"{$target}\" });"
      ]));
    }

    if($persist){

      $instance = new UserNotification([
        'user_id'=>$user_id,
        'title'=>$title,
        'description'=>$description,
        'target'=>$target
      ]);

      $instance->save();
    }
  }

}