<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  protected $table = 'setting';

  protected $fillable = [ 'key', 'value' ];

  protected $casts = [
    'value'=>'array'
  ];
}