<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\FilterableTrait;
use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class ScheduledTask extends Model
{
  use LoggedTraitV3, FilterableTrait;

  protected $table = 'scheduled_task';

  protected $fillable = [ 'status', 'description', 'creator', 'creator_id',
    'command', 'start', 'repeat', 'repeat_custom', 'count', 'error' ];

  protected $filter_searchable = [
    'id:=',
    'description:like'
  ];

  const STATUS_DISABLED = -2;
  const STATUS_ERROR = -1;
  const STATUS_ACTIVE = 1;
  const STATUS_RUNNING = 2;
  const STATUS_COMPLETED = 3;

  const REPEAT_NONE = 0;
  const REPEAT_MINUTELY = 1;
  const REPEAT_EVERY_FIVE_MINUTE = 2;
  const REPEAT_EVERY_TEN_MINUTE = 3;
  const REPEAT_HOURLY = 4;
  const REPEAT_DAILY = 5;
  const REPEAT_WEEKLY = 6;
  const REPEAT_MONTHLY = 7;
  const REPEAT_CUSTOM = 21;

  protected $attributes = [
    'repeat'=>self::REPEAT_NONE,
    'status'=>self::STATUS_ACTIVE
  ];

  protected $casts = [
    'repeat_custom'=>'array', // { every:{ n:1, unit:"day" }, max_count:10, except:{ dates:[], day:[] } }
    'updated_at'=>'datetime'
  ];

  public function results(){

    return $this->hasMany('Andiwijaya\AppCore\Models\ScheduledTaskResult', 'task_id');
  }

  public function preSave()
  {
    if(in_array(($name = explode(':', explode(' ', $this->command)[0])[0]), [
      'app',
      'migrate',
      'make',
      'event',
      'config',
      'data',
      'cache',
      'list',
      'optimize',
      'down',
      'up',
      'clear-compiled',
      'dump-server',
      'db',
      'queue',
      'redis',
      'route',
      'schedule',
      'vendor'
    ]))
      exc('Unable to use this command');

  }

  public function calculate()
  {
    $model = DB::table('scheduled_task_result')->where('task_id', $this->id)
      ->select(DB::raw("COUNT(*) as `count`, SUM(case when `status` = " . ScheduledTask::STATUS_ERROR .
        " then 1 else 0 end) as `error`"));
    //exc($model->toSql());
    $res = $model->first();
    
    $this->count = $res->count ?? 0;
    $this->error = $res->error ?? 0;
    parent::save();
  }

  public function run(){

    if(in_array($this->status, [ self::STATUS_DISABLED ])) return;

    if($this->status == self::STATUS_RUNNING){
      foreach($this->results->where('status', ScheduledTask::STATUS_RUNNING) as $result)
        exec("kill -9 {$result->pid}");
    }

    $t1 = microtime(1);

    $this->status = self::STATUS_RUNNING;
    $this->save();

    $report = $this->results()->create([
      'status'=>self::STATUS_RUNNING,
      'started_at'=>Carbon::now()->format('Y-m-d H:i:s'),
      'pid'=>getmypid()
    ]);

    $exitCode = Artisan::call($this->command);
    $output = Artisan::output();

    $report->status = $exitCode > 0 ? self::STATUS_ERROR : self::STATUS_COMPLETED;
    $report->verbose = $output;
    $report->ellapsed = microtime(1) - $t1;
    $report->completed_at = Carbon::now()->format('Y-m-d H:i:s');
    $report->save();

    $this->status = $report->status;
    $this->save();
  }

  public function runInBackground(){

    chdir(base_path());

    exec("php artisan scheduled-task:run --id={$this->id} > /Users/andiwijaya/www/kliknss/storage/logs/scheduled-task.log 2>&1 &", $output, $return_var);
  }

  public static function check(Command $cmd = null){

    ScheduledTask::where('status', '>=', ScheduledTask::STATUS_ACTIVE)
      ->orderBy('id')
      ->chunk(1000, function($tasks) use($cmd){

        foreach($tasks as $task){

          switch($task->repeat){

            case self::REPEAT_NONE:
              if($task->count <= 0){
                if($cmd) $cmd->info("Run in background {$task->id}");

                $task->runInBackground();
              }
              break;

            case self::REPEAT_MINUTELY:
              if(strtotime($task->start) < time() && (Carbon::now()->format('YmdHi') - $task->updated_at->format('YmdHi')) >= 1){
                if($cmd) $cmd->info("Run in background {$task->id} (every minute)");

                $task->runInBackground();
              }
              break;

            case self::REPEAT_HOURLY:
              if(strtotime($task->start) < time() && (Carbon::now()->format('YmdH') - $task->updated_at->format('YmdH')) >= 1){
                if($cmd) $cmd->info("Run in background {$task->id} (every hour)");

                $task->runInBackground();
              }
              break;

            case self::REPEAT_DAILY:
              if(strtotime($task->start) < time() && (Carbon::now()->format('Ymd') - $task->updated_at->format('Ymd')) >= 1){
                if($cmd) $cmd->info("Run in background {$task->id} (every day)");

                $task->runInBackground();
              }
              break;

            case self::REPEAT_MONTHLY:
              if(strtotime($task->start) < time() && (Carbon::now()->format('Ym') - $task->updated_at->format('Ym')) >= 1){
                if($cmd) $cmd->info("Run in background {$task->id} (every month)");

                $task->runInBackground();
              }
              break;

          }

        }

      });
  }



  public function createInstances(){

    if($this->status != self::STATUS_ACTIVE) return;

    //\Illuminate\Support\Facades\Log::info("create instance: {$this->id}");

    switch($this->repeat){

      case self::REPEAT_NONE:
        if(count($this->instances) <= 0){
          $this->instances()->create([
            'command'=>$this->command,
            'start'=>$this->start
          ]);
        }
        break;

      case self::REPEAT_MINUTELY:
        if($this->instances->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)->count() <= 0){

          $instances = [];
          for($i = 1 ; $i <= 10 ; $i++){
            $instances[] = new ScheduledTaskInstance([
              'command'=>$this->command,
              'start'=>Carbon::now()->addMinutes($i)->format('Y-m-d H:i:00')
            ]);
          }
          $this->instances()->saveMany($instances);
        }
        break;

      case self::REPEAT_EVERY_FIVE_MINUTE:
        if($this->instances->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)->count() <= 0){

          $instances = [];
          $currentMinute = Carbon::now()->minute;
          $addMinute = ((floor($currentMinute / 5) * 5) + 5) - $currentMinute;
          for($i = 1 ; $i <= 10 ; $i++){
            $instances[] = new ScheduledTaskInstance([
              'command'=>$this->command,
              'start'=>Carbon::now()->addMinutes($addMinute)->format('Y-m-d H:i:00')
            ]);
            $addMinute += 5;
          }

          $this->instances()->saveMany($instances);
        }
        break;

      case self::REPEAT_EVERY_TEN_MINUTE:
        if($this->instances->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)->count() <= 0){

          $instances = [];
          $currentMinute = Carbon::now()->minute;
          $addMinute = ((floor($currentMinute / 10) * 10) + 10) - $currentMinute;
          for($i = 1 ; $i <= 10 ; $i++){
            $instances[] = new ScheduledTaskInstance([
              'command'=>$this->command,
              'start'=>Carbon::now()->addMinutes($addMinute)->format('Y-m-d H:i:00')
            ]);
            $addMinute += 10;
          }

          $this->instances()->saveMany($instances);
        }
        break;

      case self::REPEAT_HOURLY:
        if($this->instances->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)->count() <= 0){

          $instances = [];
          for($i = 1 ; $i <= 10 ; $i++){
            $instances[] = new ScheduledTaskInstance([
              'command'=>$this->command,
              'start'=>Carbon::now()->addHours($i)->format('Y-m-d H:00:00')
            ]);
          }
          $this->instances()->saveMany($instances);
        }
        break;

      case self::REPEAT_DAILY:
        if($this->instances->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)->count() <= 0){

          $instances = [];
          for($i = 1 ; $i <= 10 ; $i++){
            $instances[] = new ScheduledTaskInstance([
              'command'=>$this->command,
              'start'=>Carbon::now()->addDays($i)->format('Y-m-d 00:00:00')
            ]);
          }
          $this->instances()->saveMany($instances);
        }
        break;

    }

  }


  public function getRepeatTextAttribute(){

    switch($this->repeat){

      case self::REPEAT_MINUTELY: return 'Every minute';
      case self::REPEAT_HOURLY: return 'Every hour';
      case self::REPEAT_DAILY: return 'Every day';
      case self::REPEAT_MONTHLY: return 'Every month';
      default: return '';

    }
  }

  public function getStatusTextAttribute(){

    switch($this->status){

      case self::STATUS_DISABLED: return 'Disabled';
      case self::STATUS_ACTIVE: return 'Active';
      case self::STATUS_RUNNING: return 'Running';
      case self::STATUS_COMPLETED: return 'Completed';
      case self::STATUS_ERROR: return 'Error';
    }
  }

  public function getStatusHtmlAttribute(){

    $html = [ "<div class='pad-1'>" ];

    switch($this->status){

      case self::STATUS_DISABLED: $html[] = "<span class='badge gray'><span>Inactive</span></span>"; break;
      case self::STATUS_ACTIVE: $html[] = "<span class='badge green'><span>Active</span></span>"; break;
      case self::STATUS_RUNNING: $html[] = "<span class='badge yellow'><span>Running</span></span>"; break;
      case self::STATUS_COMPLETED: $html[] = "<span class='badge blue'><span>Completed</span></span>"; break;
      case self::STATUS_ERROR: $html[] = "<span class='badge red'><span>Error</span></span>"; break;
    }

    $html[] = "</div>";

    return implode('', $html);
  }

}
