<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
  use LoggedTraitV3;

  protected $table = 'page';

  protected $fillable = [ 'is_active', 'path', 'title', 'description', 'keywords', 'h1', 'p', 'offers', 'image_url' ];

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

      $image_url = '';
      foreach($this->fill_attributes['sections'] as $obj){

        if(isset($obj['data']['html'])){
          $obj['data']['html'] = preg_replace('/([\/]*images\/)/', '/images/', $obj['data']['html']);
        }

        $this->sections()->create($obj);
      }

      $this->load([ 'sections' ]);

    }

  }

  public function calculate()
  {
    $image_url = $this->image_url;
    foreach($this->sections as $section){

      preg_match_all('/src=[\'|\"](.*?(?=[\'|\"]))/', $section->data['html'] ?? '', $matches);

      if(isset($matches[1][0])){
        $image_url = $matches[1][0];
        break;
      }
    }

    $this->image_url = $image_url;
    parent::save();

  }
}
