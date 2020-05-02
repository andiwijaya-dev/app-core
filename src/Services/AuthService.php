<?php

namespace Andiwijaya\AppCore\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthService{

  protected $user;

  public function login(array $params)
  {
    $validator = Validator::make($params, [
      'user_id'=>'required',
      'password'=>'required'
    ]);
    if($validator->fails()) exc($validator->errors()->first());

    $model = config('auth.providers.user.model');

    $user = $model::where(function($query) use($params){
      $query->where('email', $params['user_id'])
        ->orWhere('name', $params['user_id']);
    })
      ->first();

    if(!$user) exc('Unable to login, invalid user id');

    if($user->password != md5($params['password'])) exc('Invalid password');

    $this->user = $user;

    Session::put('user_id', $user->id);

    return $user;
  }

  public function logout()
  {
    if(Session::get('user_id') > 0){

      Session::forget('user_id');
    }

    $this->user = null;
  }

  public function load()
  {
    $user_id = Session::get('user_id');

    if($user_id > 0){

      $model = config('auth.providers.user.model');

      $user = $model::find($user_id)->first();

      $this->user = $user;
    }
  }

  public function __get($name)
  {
    switch($name){

      case 'user': return $this->user;

    }
  }

}