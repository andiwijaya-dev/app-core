<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduledTaskRun extends Command
{
  protected $signature = 'scheduled-task:run {--id=} {--delay=0}';

  protected $description = 'Run scheduled task instance';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    $id = $this->option('id');
    $delay = $this->option('delay');

    if($delay > 0) sleep($delay);

    if($id > 0){
      $task = ScheduledTask::findOrFail($id);
      //Log::error("Run task:{$task->id}, description:{$task->description}");
      $task->run();
    }
    else{
      if(!file_exists(storage_path('logs/scheduled-task-run.lock'))){
        file_put_contents(storage_path('logs/scheduled-task-run.lock'), Carbon::now()->format('Y-m-d H:i:s'));
        ScheduledTask::check($this);
        unlink(storage_path('logs/scheduled-task-run.lock'));
      }
    }


    //$this->info("Completed in " . (microtime(1) - LARAVEL_START));
  }
}