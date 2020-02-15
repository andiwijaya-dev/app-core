<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\CMSListUpdateTrait;
use Andiwijaya\AppCore\Models\Traits\FilterableTrait;
use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Models\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class User extends Model
{
  use LoggedTraitV3, CMSListUpdateTrait, FilterableTrait;

  protected $table = 'user';

  protected $filter_searchable = [
    'name:like',
    'email:like',
    'code:like',
  ];

  protected $fillable = [
    'code', 'name', 'email',
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

  public function privileges(){

    return $this->hasMany('Andiwijaya\AppCore\Models\UserPrivilege', 'user_id', 'id');

  }


  public function getPrivilege($module_id, $key){

    foreach($this->privileges as $privilege)
      if($privilege->module_id == $module_id && isset($privilege->{$key}))
        return $privilege->{$key};
    return 0;

  }

  public function anyPrivilege($modules, $key){

    $value = 0;
    foreach($this->privileges as $privilege){
      foreach($modules as $module_id){
        if($privilege->module_id == $module_id && isset($privilege->{$key}) && $privilege->{$key} > 0){
          $value = 1;
          break;
        }
      }
      if($value > 0) break;
    }
    return $value;

  }


  public function preSave(){

    $validator = Validator::make($this->attributes,
      [
        'code'=>($this->exists ? 'sometimes|' : '') . 'required|unique:user,code' . ($this->exists ? ',' . $this->id : ''),
        'email'=>($this->exists ? 'sometimes|' : '') . 'required|email|unique:user,email' . ($this->exists ? ',' . $this->id : ''),
        'name'=>($this->exists ? 'sometimes|' : '') . 'required'
      ],
      [
        'code.required'=>'Kode user harus diisi.',
        'code.unique'=>'Kode user sudah ada.',
        'email.required'=>'Email harus diisi.',
        'email.unique'=>'Email sudah ada.',
        'name.required'=>'Nama harus diisi.'
      ]
    );
    if($validator->fails()) throw new \Exception($validator->errors()->first());

    if(isset($this->fill_attributes['privileges']) &&
      ($privileges = array_diff_assoc2($this->privileges, $this->fill_attributes['privileges'])))
      $this->updates['privileges'] = $privileges;

    if(isset($this->fill_attributes['password']) && strlen($this->fill_attributes['password']) > 0){

      $validator = Validator::make($this->fill_attributes,
        [
          'password'=>'required|min:6|confirmed',
        ],
        [
          'password.required'=>'Password harus diisi.',
          'password.min'=>'Minimal password diisi 6 karakter',
          'password.confirmed'=>'Konfirmasi password tidak sama'
        ]
      );
      if($validator->fails()) throw new \Exception($validator->errors()->first());

      $this->password = md5($this->fill_attributes['password']);

    }

  }

  public function postSave(){

    if(isset($this->fill_attributes['privileges'])){

      if(isset($this->updates['privileges'])){

        foreach($this->updates['privileges'] as $item){

          if(isset($item['_type'])){
            switch($item['_type']){

              case Log::TYPE_REMOVE:
                UserPrivilege::where([
                  'user_id'=>$this->id,
                  'module_id'=>$item['module_id']
                ])
                  ->delete();
                break;

              case Log::TYPE_UPDATE:
                UserPrivilege::where([
                  'user_id'=>$this->id,
                  'module_id'=>$item['module_id']
                ])
                  ->first()
                  ->fill($item['_updates'])
                  ->save();
                break;

              case Log::TYPE_CREATE:
                (new UserPrivilege([
                  'user_id'=>$this->id,
                  'module_id'=>$item['module_id']
                ]))
                  ->fill($item)
                  ->save();
                break;

            }
          }

        }

      }

    }

  }

}
