<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
  protected $table = 'config';

  protected $fillable = [ 'key', 'value' ];

  protected $casts = [
    'value'=>'array'
  ];
}