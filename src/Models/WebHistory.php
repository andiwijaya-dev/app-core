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

}
