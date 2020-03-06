<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreset extends Model{

  protected $table = 'user_preset';

  protected $fillable = [ 'user_id', 'key', 'value' ];

  protected $casts = [
    'value'=>'array'
  ];

}