<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
  use LoggedTraitV3;

  const TYPE_CUSTOM = 99;
  const TYPE_BANNER = 1;
  const TYPE_THUMBNAIL1 = 2;
  const TYPE_THUMBNAIL2 = 3;
  const TYPE_TEXT = 4;
  const TYPE_STEP_BY_STEP = 5;
  const TYPE_SPAREPART_CATEGORY = 6;
  const TYPE_HMC_PRICE_TABLE= 7;
  const TYPE_LOCATION = 8;
  const TYPE_DISCUSSION = 9;
  const TYPE_REVIEW = 10;

  const TYPE_M2W_FORM_NAMA = 23;
  const TYPE_M2W_FORM_SIMULASI = 24;
  const TYPE_FAQ = 31;

  const TYPE_SPAREPART_THUMBNAIL1 = 32;
  const TYPE_SPAREPART_THUMBNAIL2 = 33;
  const TYPE_SPAREPART_DETAIL = 34;
  const TYPE_SPAREPART_CATEGORIES = 35;
  const TYPE_CATEGORIES_SPAREPART = 38;

  const TYPE_M4W_FORM_NAMA = 43;
  const TYPE_M4W_FORM_SIMULASI = 44;
  const TYPE_M4W_BENEFIT = 45;
  const TYPE_M4W_STEP_BY_STEP = 46;

  protected $table = 'section';

  protected $fillable = [ 'page_id', 'is_active', 'type', 'title', 'description', 'extra', 'data', 'thumbnail_type' ];

  protected $attributes = [
    'is_active'=>1
  ];

  protected $casts = [
    'data'=>'array',
    'extra'=>'array'
  ];

  public function __construct(array $attributes = [])
  {
    $this->log = false;

    parent::__construct($attributes);
  }

  public function preSave()
  {
    $data = $this->data;

    if(isset($data['html']))
      $data['html'] = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $data['html']);

    $this->data = $data;

  }

  public function getTypeTextAttribute()
  {
    return __('models.section-type-' . $this->type);
  }
}
