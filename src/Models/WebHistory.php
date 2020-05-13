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
    'user_agent'
  ];

  const TYPE_VISIT = 1;
  const TYPE_LEAVE = 2;
  const TYPE_FOCUS = 3;
  const TYPE_BLUR = 4;

}
