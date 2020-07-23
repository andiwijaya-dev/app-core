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
      ($task = ScheduledTask::find($id)) ? $task->run() : Log::info("schedule-task:run {$id} task not found");
    else
      ScheduledTask::check();

    $this->info("Completed in " . (microtime(1) - LARAVEL_START));
  }
}
