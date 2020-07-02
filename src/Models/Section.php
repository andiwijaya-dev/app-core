<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
  use LoggedTraitV3;

  const TYPE_CUSTOM = 99;

  protected $table = 'section';

  protected $fillable = [ 'page_id', 'type', 'data' ];

  protected $casts = [
    'data'=>'array'
  ];

  public function __construct(array $attributes = [])
  {
    $this->log = false;

    parent::__construct($attributes);
  }

  public function preSave()
  {
    $data = $this->data;

    if(isset($data['html']))
      $data['html'] = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $data['html']);

    $this->data = $data;

  }
}
