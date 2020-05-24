<?php

namespace Andiwijaya\AppCore\Models\Traits;

use Andiwijaya\AppCore\Events\ModelEvent;
use Andiwijaya\AppCore\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait LoggedTraitV3{

  protected $fill_attributes = [];

  public $updates = [];

  public $log = true;

  public static function getShortName(){

    return (new \ReflectionClass(self::class))->getShortName();

  }

  public function logs(){

    return $this->morphMany('Andiwijaya\AppCore\Models\Log', 'loggable');

  }

  public function fill(array $attributes){

    $this->fill_attributes = $attributes;

    return parent::fill($attributes);

  }

  /**
   * @param array $options  { pre-delete:1|0, post-delete:1|0, log:1|0, notify:1|0, log-type:(int) }
   * @return bool|null
   * @throws \Exception
   */
  public function delete($options = []){

    if(in_array(debug_backtrace()[1]['function'], [ 'preDelete', 'postDelete' ]))
      return Model::delete($options);

    try{

      DB::beginTransaction();

      if(!isset($options['pre-delete']) || $options['pre-delete'])
        $this->preDelete();

      if($this->log && (!isset($options['log']) || $options['log'])){

        $type = Log::TYPE_REMOVE;
        if(isset($options['log_type'])) $type = $options['log_type'];
        if(isset($options['log-type'])) $type = $options['log-type'];

        $user_id = isset($options['user-id']) ? $options['user-id'] : Session::get('user_id');

        $this->logs()->create([
          'type'=>$type,
          'data'=>$this->attributes,
          'user_id'=>$user_id
        ]);

      }

      $return = parent::delete();

      DB::commit();

    }
    catch(\Exception $ex){

      DB::rollBack();

      throw $ex;

    }

    if(!isset($options['post-delete']) || $options['post-delete'])
      $this->postDelete();

    if(!isset($options['notify']) || $options['notify']){

      event(new ModelEvent(ModelEvent::TYPE_REMOVE, self::class, $this->id));

    }

    return $return;

  }

  /**
   * @param array $options  { pre-save:1|0, post-save:1|0, calculate:1|0, log:1|0, notify:1:0, log-type:(int), user-id:(int) }
   * @return bool
   * @throws \Exception
   */
  public function save(array $options = [], $debug = null){

    if(in_array(debug_backtrace()[1]['function'], [ 'calculate', 'preSave', 'postSave' ]))
      return Model::save($options);

    try{

      DB::beginTransaction();

      if(!isset($options['pre-save']) || $options['pre-save'])
        $this->preSave();

      $exists = $this->exists;

      if($exists){
        $dirty = array_merge($this->updates, $this->getDirty());
        if(count($dirty) > 0){
          $original = $this->getOriginal();

          foreach($dirty as $key=>$value)
            if(array_key_exists($key, $original))
              $this->updates[$key] = [ '_type'=>2, '_value'=>$original[$key], '_updates'=>$value ];
        }
      }
      else{
        $this->updates = array_merge($this->attributes, $this->updates);
      }

      $return = parent::save($options);

      // Post save event, should return array containing updated data
      if(!isset($options['post-save']) || $options['post-save'])
        $this->postSave();

      if($this->log &&
        (count($this->updates) > 0 &&
          !app()->runningInConsole() &&
          (!isset($options['log']) || $options['log']))){

        $type = $exists ? Log::TYPE_UPDATE : Log::TYPE_CREATE;
        if(isset($options['log_type'])) $type = $options['log_type'];
        if(isset($options['log-type'])) $type = $options['log-type'];

        $user_id = isset($options['user-id']) ? $options['user-id'] : Session::get('user_id');

        $this->logs()->create([
          'type'=>$type,
          'data'=>$this->updates,
          'user_id'=>$user_id
        ]);

      }

      DB::commit();

    }
    catch(\Exception $ex){

      DB::rollBack();

      if($ex instanceof QueryException){
        switch($ex->getCode()){
          default:
            throw $ex;
        }
      }
      else
        throw $ex;

    }

    if(!isset($options['calculate']) || $options['calculate'])
      $this->calculate();

    if((!isset($options['notify']) || $options['notify']) &&
      count($this->updates) > 0){

      event(new ModelEvent($exists ? ModelEvent::TYPE_UPDATE : ModelEvent::TYPE_CREATE, self::class, $this->id));

    }

    return $return;

  }


  function preSave(){}

  function postSave(){}

  function preDelete(){}

  function postDelete(){}

  function calculate(){}

}