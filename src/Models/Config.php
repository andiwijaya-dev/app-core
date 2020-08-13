<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
  use LoggedTraitV3;

  protected $table = 'config';

  protected $fillable = [ 'key', 'value' ];

  protected $casts = [
    'value'=>'array'
  ];
}