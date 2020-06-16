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


  public function task(){

    return $this->belongsTo('Andiwijaya\AppCore\Models\ScheduledTask', 'task_id');
  }
}
