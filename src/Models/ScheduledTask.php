<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class ScheduledTask extends Model
{
  use LoggedTraitV3;

  protected $table = 'scheduled_task';

  protected $fillable = [ 'status', 'description', 'creator', 'creator_id', 'command', 'start', 'repeat', 'repeat_options',
    'result', 'result_details', 'parent_id', 'completed_at', 'ellapsed', 'flag' ];

  const STATUS_DISABLED = -1;
  const STATUS_ACTIVE = 1;
  const STATUS_COMPLETED = 2;

  const REPEAT_ONCE = 0;
  const REPEAT_MINUTELY = 1;
  const REPEAT_EVERY_FIVE_MINUTE = 2;
  const REPEAT_EVERY_TEN_MINUTE = 3;
  const REPEAT_HOURLY = 4;
  const REPEAT_DAILY = 5;
  const REPEAT_WEEKLY = 6;
  const REPEAT_MONTHLY = 7;
  const REPEAT_CUSTOM = 21;

  protected $attributes = [
    'repeat'=>self::REPEAT_ONCE,
    'status'=>self::STATUS_ACTIVE
  ];

  protected $casts = [
    'repeat_options'=>'array', // { every:{ n:1, unit:"day" }, max_count:10, except:{ dates:[], day:[] } }
    'result_details'=>'array'
  ];




  public function instances(){

    return $this->hasMany('Andiwijaya\AppCore\Models\ScheduledTaskInstance', 'task_id');
  }

  public function last_instance(){

    return $this->hasOne('Andiwijaya\AppCore\Models\ScheduledTaskInstance', 'task_id', 'id')
      ->orderBy('id', 'desc');
  }

  public function last_completed_instance(){

    return $this->hasOne('Andiwijaya\AppCore\Models\ScheduledTaskInstance', 'task_id', 'id')
      ->where('status', ScheduledTaskInstance::STATUS_COMPLETED)
      ->orderBy('id', 'desc');
  }

  public function calculate()
  {

    // Calculate status
    switch($this->repeat) {

      case self::REPEAT_ONCE:
        $this->load([ 'instances' ]);
        if($this->instances[0]->status >= ScheduledTaskInstance::STATUS_COMPLETED)
          $this->status = self::STATUS_COMPLETED;
        break;
    }

    parent::save();
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

  public function postSave()
  {
    $this->createInstances();
  }

  public function preDelete()
  {
    if($this->flag == 's') exc('Unable to remove system task');
  }

  public function createInstances(){

    if($this->status != self::STATUS_ACTIVE) return;

    //\Illuminate\Support\Facades\Log::info("create instance: {$this->id}");

    switch($this->repeat){

      case self::REPEAT_ONCE:
        if(count($this->instances) <= 0)
          $this->instances()->create([
            'command'=>$this->command,
            'start'=>null
          ]);
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

  public function getStatusHtmlAttribute(){

    $html = [ "<div class='pad-1'>" ];

    switch($this->status){

      case self::STATUS_DISABLED: $html[] = "<span class='badge gray'><span>Inactive</span></span>"; break;
      case self::STATUS_ACTIVE: $html[] = "<span class='badge green'><span>Active</span></span>"; break;
      case self::STATUS_COMPLETED: $html[] = "<span class='badge blue'><span>Completed</span></span>"; break;
    }

    $html[] = "</div>";

    return implode('', $html);
  }

}
