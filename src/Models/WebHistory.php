<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class WebHistory extends Model
{
  protected $table = 'web_history';

  protected $fillable = [
    'type',
    'session_id',
    'path',
    'query',
    'referrer',
    'remote_ip',
    'city',
    'user_agent',
    'timestamp',
    'extra'
  ];

  protected $casts = [
    'extra'=>'array'
  ];

  const TYPE_VISIT = 1;
  const TYPE_LEAVE = 2;
  const TYPE_FOCUS = 3;
  const TYPE_BLUR = 4;
  const TYPE_CLICK = 5;
  const TYPE_HOVER = 6;
  const TYPE_SCROLL = 7;

}
