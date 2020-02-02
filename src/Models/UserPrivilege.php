<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrivilege extends Model{

  protected $table = 'user_privilege';

  protected $fillable = [ 'user_id', 'module_id', 'list', 'create', 'update', 'delete', 'import', 'export' ];


  public function user(){

    return $this->hasOne('Andiwijaya\AppCore\Models\User', 'id', 'user_id');

  }

}