<?php

namespace Andiwijaya\AppCore\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

  protected $table = 'user';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'user_id', 'email', 'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function notifications(){

    return $this->hasMany('Andiwijaya\AppCore\Models\UserNotification', 'user_id', 'id');

  }


  public function scopeFilter($model, array $params, $callback = null){

    if(isset($params['search']) && $params['search'])
      $model->search($params['search']);

    if(isset($params['filters']) && is_array($params['filters'])){

      foreach($params['filters'] as $filter){

        $model->where(function($query) use($filter){

          $name = $filter['name'];

          foreach($filter['values'] as $idx=>$item){

            switch($item['operator']){

              case '=':
                $item['operand'] == 'or' ? $query->orWhere($name, '=', $item['value']) :
                  $query->where($name, '=', $item['value']);
                break;

              case 'contains':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "%{$item['value']}%") :
                  $query->where($name, 'like', "%{$item['value']}%");
                break;

              case 'begins_with':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "{$item['value']}%") :
                  $query->where($name, 'like', "{$item['value']}%");
                break;

              case 'ends_with':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "%{$item['value']}") :
                  $query->where($name, 'like', "%{$item['value']}");
                break;


            }

          }

        });

      }

    }

    if(is_callable($callback))
      $callback($model);

    return $model;

  }

}
