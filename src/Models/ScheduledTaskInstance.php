<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class ScheduledTaskInstance extends Model
{
  use LoggedTraitV3;

  const STATUS_SCHEDULED = 1;
  const STATUS_RUNNING = 2;
  const STATUS_COMPLETED = 3;

  protected $table = 'scheduled_task_instance';

  protected $fillable = [ 'task_id', 'command', 'start', 'result', 'result_details', 'completed_at' ];

  protected $attributes = [
    'status'=>self::STATUS_SCHEDULED
  ];

  protected $casts = [
    'result_details'=>'array',
    'completed_at'=>'datetime'
  ];


  public function __construct(array $attributes = [])
  {
    $this->log = false;

    parent::__construct($attributes);
  }

  public function calculate()
  {
    $this->task->calculate();
  }

  public function run(){

    if($this->task->status != ScheduledTask::STATUS_ACTIVE) return;

    $start_time = microtime(1);
    $this->status = ScheduledTaskInstance::STATUS_RUNNING;
    $this->pid = getmypid();
    $this->save([ 'log'=>false ]);

    $exitCode = Artisan::call($this->command);
    $output = Artisan::output();

    $this->status = ScheduledTaskInstance::STATUS_COMPLETED;
    $this->ellapsed = microtime(1) - $start_time;
    $this->completed_at = Carbon::now()->toDateTimeString();
    $this->result = $exitCode;
    $this->result_details = [
      'output'=>$output
    ];
    $this->save();
  }

  public function runInBackground(){

    chdir(base_path());

    exec("php artisan scheduled-task:run --id={$this->id} > /dev/null 2>&1 & disown", $output, $return_var);
  }

  public function task(){

    return $this->belongsTo('Andiwijaya\AppCore\Models\ScheduledTask', 'task_id');
  }


  public static function check(){

    // Execute task instance
    ScheduledTaskInstance::select('id')
      ->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)
      ->where(function($query){

        $query->whereNull('start')
          ->orWhere('start', Carbon::now()->format('Y-m-d H:i') . ':00');
      })
      ->orderBy('id')
      ->chunk(1000, function($instances){

        foreach($instances as $instance){

          exec("php artisan scheduled-task:run --id={$instance->id} > /dev/null 2>&1 & disown", $output, $return_var);

          //Log::info(implode("\n", $output) . "\n" . $return_var);
        }
      });

    // Create more instances for repeated task
    ScheduledTask::where('repeat', '>', 0)
      ->chunk(1000, function($tasks){

        foreach($tasks as $task){

          $task->createInstances();
        }
      });
  }
}
