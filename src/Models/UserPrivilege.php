<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrivilege extends Model{

  protected $table = 'user_privilege';

  protected $fillable = [ 'user_id', 'key', 'value' ];

  protected $casts = [
    'value'=>'array'
  ];

  public function user(){

    return $this->hasOne('Andiwijaya\AppCore\Models\User', 'id', 'user_id');

  }

}