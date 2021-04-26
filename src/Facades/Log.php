<?php

namespace Andiwijaya\AppCore\Facades;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Andiwijaya\AppCore\Notifications\SlackNotification;
use App\Notifications\LogToSlackNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class Log{

  private static $settings = [];

  public static function make($message, $type = 'info', $settings = null){

    $detail = '';
    if(is_array($message)){
      $detail = $message[1] ?? '';
      $message = $message[0] ?? '';
    }

    $formattedMessage = implode("\t", [
      "[" . Carbon::now()->format('Y-m-d H:i:s') . "]",
      $message
    ]);

    if(isset(self::$settings['output']) || isset($settings['output'])){
      $output = $settings['output'] ?? self::$settings['output'];
      if(is_callable([ $output, $type ]))
        call_user_func_array([ $output, $type ], [ $formattedMessage ]);
    }

    if(isset(self::$settings['path']) || isset($settings['path'])){
      $path = $settings['path'] ?? self::$settings['path'];
      $fp = fopen(self::$settings['path'], 'a+');
      if($fp){
        fwrite($fp, $formattedMessage . "\n");
        fclose($fp);
      }
    }

    if(isset(self::$settings['table']) || isset($settings['table'])){
      $table = $settings['table'] ?? self::$settings['table'];
      DB::table($table)->insert([
        'type'=>1,
        'data'=>json_encode([ 'type'=>$type, 'message'=>$message ]),
        'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }

    if(isset(self::$settings['slack']) || isset($settings['slack'])){
      $slack = $settings['slack'] ?? self::$settings['slack'];
      ScheduledTask::runOnce(function($result) use($slack, $type, $message, $detail){
        Notification::route('slack', $slack)
          ->notify(new SlackNotification($message, $type, $detail));
      });
    }
  }

  public static function error($message)
  {
    return self::make($message, 'error');
  }

  public static function warning($message)
  {
    return self::make($message, 'warning');
  }

  public static function info($message)
  {
    return self::make($message);
  }

  public static function set(array $settings, $overwrite = false){

    foreach($settings as $key=>$value){
      switch($key){

        case 'path':
        case 'table':
        case 'output':
        case 'slack':
          if(!isset(self::$settings[$key]) || $overwrite)
            self::$settings[$key] = $value;
          break;
      }
    }
  }

}