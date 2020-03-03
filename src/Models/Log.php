<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\FilterableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model{

  use FilterableTrait;

  protected $table = 'log';

  protected $fillable = [
    'loggable_type', 'loggable_id', 'type', 'data', 'user_agent', 'remote_ip', 'user_id'
  ];

  protected $casts = [
    'data'=>'array'
  ];

  const TYPE_CREATE = 1;
  const TYPE_UPDATE = 2;
  const TYPE_REMOVE = -1;


  public function __construct(array $attributes = []){

    if(!app()->runningInConsole()){
      if(!isset($attributes['user_agent'])) $attributes['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      if(!isset($attributes['remote_ip'])) $attributes['remote_ip'] = $_SERVER['REMOTE_ADDR'];
    }

    parent::__construct($attributes);

  }

  public function loggable(){

    return $this->morphTo();

  }

  public function user(){

    return $this->hasOne('Andiwijaya\AppCore\Models\User', 'id', 'user_id');

  }


  public function getTypeTextAttribute(){

    if(method_exists($this->loggable, 'getLogTypeText'))
      return $this->loggable::getLogTypeText($this);

    return __('models.log-type-' . $this->type);

  }

}