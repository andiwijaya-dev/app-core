<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use App\Models\WebSession;
use Illuminate\Database\Eloquent\Model;

class WebHistory extends Model
{
  use LoggedTraitV3;

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

  public function calculate()
  {
    $session = WebSession::firstOrNew([
      'session_id'=>session_id
    ], [
      'path'=>$this->path,
      'remote_ip'=>$this->remote_ip,
      'user_agent'=>$this->user_agent,
      'referrer'=>$this->referrer
    ]);
  }

  public function __construct(array $attributes = [])
  {
    $this->log = false;

    parent::__construct($attributes);
  }
}
