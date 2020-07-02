<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
  use LoggedTraitV3;

  protected $table = 'page';

  protected $fillable = [ 'is_active', 'path', 'title', 'description', 'keywords', 'h1', 'p', 'offers' ];

  protected $casts = [
    'offers'=>'array'
  ];

  public function sections(){

    return $this->hasMany('Andiwijaya\AppCore\Models\Section', 'page_id');
  }

  public function postSave()
  {
    if(isset($this->fill_attributes['sections'])){

      $this->sections()->delete();

      foreach($this->fill_attributes['sections'] as $obj){

        if(isset($obj['data']['html'])){
          $obj['data']['html'] = preg_replace('/([\/]*images\/)/', '/images/', $obj['data']['html']);
        }

        $this->sections()->create($obj);
      }


      $this->load([ 'sections' ]);
    }

  }
}
