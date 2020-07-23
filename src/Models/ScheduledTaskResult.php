<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTaskResult extends Model{

  const STATUS_RUNNING = 1;
  const STATUS_COMPLETED = 2;
  const STATUS_ERROR = -1;

  protected $table = 'scheduled_task_result';

  protected $fillable = [ 'task_id', 'status', 'verbose', 'started_at', 'completed_at',
    'ellapsed', 'pid' ];

  protected $casts = [
    'started_at'=>'datetime',
    'completed_at'=>'datetime',
  ];


  public function getStatusTextAttribute(){

    switch($this->status){

      case self::STATUS_RUNNING: return 'Running';
      case self::STATUS_COMPLETED: return 'Completed';
      case self::STATUS_ERROR: return 'Error';
    }
  }

}