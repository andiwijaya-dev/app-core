<?php

namespace Andiwijaya\AppCore\Models\Traits;

use Andiwijaya\AppCore\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait LoggedTraitV3{

  protected $fill_attributes = [];

  public $updates = [];

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

  public function delete($options = []){

    try{

      DB::beginTransaction();

      if(!isset($options['predelete']) || $options['predelete'])
        $this->preDelete();

      if(!isset($options['log']) || $options['log']){

        $this->logs()->create([
          'type'=>isset($options['log_type']) ? $options['log_type'] : Log::TYPE_REMOVE,
          'data'=>$this->attributes,
          'user_id'=>Session::get('user_id')
        ]);

      }

      $return = parent::delete();

      DB::commit();

    }
    catch(\Exception $ex){

      DB::rollBack();

      throw $ex;

    }

    if(!isset($options['postdelete']) || $options['postdelete'])
      $this->postDelete();

    if(!isset($options['notify']) || $options['notify']){
      if(method_exists($this, 'cmsListDelete'))
        $this->cmsListDelete();
    }

    return $return;

  }

  public function save(array $options = []){

    //echo self::getShortName() . ":" . $this->id . " save: " . json_encode($options) . PHP_EOL;

    $skip = !isset($options['skip']) ?
      (debug_backtrace()[1]['class'] == self::class &&
      in_array(debug_backtrace()[1]['function'], [ 'calculate', 'preSave', 'postSave' ]) ? true : false) :
      $options['skip'];

    try{

      DB::beginTransaction();

      $exists = $this->exists;

      if(!$skip && (!isset($options['pre_save']) || $options['pre_save']))
        $this->preSave();

      if($exists){
        $dirty = array_merge($this->updates, $this->getDirty());
        if(count($dirty) > 0){
          $original = $this->getOriginal();
          foreach($dirty as $key=>$value)
            if(isset($original[$key]))
              $this->updates[$key] = [ '_type'=>2, '_value'=>$original[$key], '_updates'=>$value ];
        }
      }
      else{
        $this->updates = array_merge($this->updates, $this->attributes);
      }

      $return = parent::save($options);

      // Post save event, should return array containing updated data
      if(!$skip && (!isset($options['post_save']) || $options['post_save']))
        $this->postSave();

      if((count($this->updates) > 0 && !app()->runningInConsole() &&
        !$skip &&
        (!isset($options['log']) || $options['log']))){

        $this->logs()->create([
          'type'=>isset($options['log_type']) ? $options['log_type'] : ($exists ? Log::TYPE_UPDATE : Log::TYPE_CREATE),
          'data'=>$this->updates,
          'user_id'=>Session::get('user_id')
        ]);

      }

      DB::commit();

    }
    catch(\Exception $ex){

      DB::rollBack();

      throw $ex;

    }

    if(!$skip && (!isset($options['calculate']) || $options['calculate']))
      $this->calculate();

    if(!isset($options['notify']) || $options['notify']) {
      if (method_exists($this, 'cmsListUpdate'))
        $this->cmsListUpdate();
    }

    return $return;

  }


  function preSave(){}

  function postSave(){}

  function preDelete(){}

  function postDelete(){}

  function calculate(){}

}