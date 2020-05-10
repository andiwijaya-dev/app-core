<?php

namespace Andiwijaya\AppCore\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthService{

  protected $user;

  public function check(){

    return Session::get('user_id') > 0;

  }

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

    if(!Hash::check($params['password'], $user->password)) exc(__('validation.password-invalid'));

    $user->last_login_at = Carbon::now()->toDateTimeString();
    $user->save();

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

      $user = $model::find($user_id);

      $this->user = $user;
    }
  }

  public function user()
  {
    return $this->user;
  }

}