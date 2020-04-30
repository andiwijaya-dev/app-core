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
    'referer',
    'remote_ip',
    'user_agent'
  ];

  const TYPE_VISIT = 1;

}
