<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Andiwijaya\AppCore\Models\ScheduledTaskInstance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ScheduledTaskRun extends Command
{
  protected $signature = 'scheduled-task:run {--id=}';

  protected $description = 'Run scheduled task instance';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    $id = $this->option('id');

    if($id > 0)
      $this->handleOne($id);
    else
      $this->checkForHandles();
  }

  public function handleOne($id){

    $instance = ScheduledTaskInstance::find($id);
    if(!$instance) return;
    if($instance->task->status != ScheduledTask::STATUS_ACTIVE) return;

    $start_time = microtime(1);
    $instance->status = ScheduledTaskInstance::STATUS_RUNNING;
    $instance->pid = getmypid();
    $instance->save([ 'log'=>false ]);

    $exitCode = Artisan::call($instance->command);
    $output = Artisan::output();

    $instance->status = ScheduledTaskInstance::STATUS_COMPLETED;
    $instance->ellapsed = microtime(1) - $start_time;
    $instance->completed_at = Carbon::now()->toDateTimeString();
    $instance->result = $exitCode;
    $instance->result_details = [
      'output'=>$output
    ];
    $instance->save();
  }

  public function checkForHandles(){

    // Execute task instance
    ScheduledTaskInstance::select('id')
      ->where('status', ScheduledTaskInstance::STATUS_SCHEDULED)
      ->where(function($query){

        $query->whereNull('start')
          ->orWhere('start', Carbon::now()->format('Y-m-d H:i') . ':00');
      })
      ->orderBy('id')
      ->chunk(1000, function($tasks){

        foreach($tasks as $task){

          exec("php artisan scheduled-task:run --id={$task->id} >> /dev/null 2>&1", $output, $return_var);

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
