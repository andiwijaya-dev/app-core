<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\CMSListUpdateTrait;
use Andiwijaya\AppCore\Models\Traits\FilterableTrait;
use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Banner extends Model
{
  use LoggedTraitV3, FilterableTrait, CMSListUpdateTrait;

  protected $table = 'banner';

  protected $fillable = [ 'type', 'is_active', 'start_date', 'end_date', 'title',
    'image_url_desktop', 'image_url_mobile', 'target', 'sequence' ];

  protected $attributes = [
    'is_active'=>1
  ];

  protected $casts = [
    'start_date'=>'date',
    'end_date'=>'date',
  ];

  const TYPE_HOME = 1;


  public function setStartDateAttribute($value){

    $this->attributes['start_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');

  }

  public function setEndDateAttribute($value){

    $this->attributes['end_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');

  }

  public function getImageUrlDesktopHtmlAttribute(){

    return "<div class=\"pad-1\">
                  <span class=\"img unloaded rat-88\" data-src=\"/images/{$this->image_url_desktop}\"></span>
                </div>";

  }

  public function getImageUrlMobileHtmlAttribute(){

    return "<div class=\"pad-1\">
                  <span class=\"img unloaded rat-88\" data-src=\"/images/{$this->image_url_mobile}\"></span>
                </div>";

  }

  public function getStartDateHtmlAttribute(){

    return "<label>{$this->start_date->format('j M Y')}</label>";

  }

  public function getEndDateHtmlAttribute(){

    return "<label>{$this->end_date->format('j M Y')}</label>";

  }


  public function preSave(){

    if(isset($this->fill_attributes['image_desktop']) && file_exists($this->fill_attributes['image_desktop'])){

      if(!is_file($this->fill_attributes['image_desktop'])) exc('Invalid file for image_desktop parameter');

      $file_md5 = md5_file($this->fill_attributes['image_desktop']);
      list($width, $height) = getimagesize($this->fill_attributes['image_desktop']);

      if($width / $height != 4 || $height < 400) exc('Banner desktop harus berukuran 1600x400px');

      if(!Storage::disk('images')->exists($file_md5))
        Storage::disk('images')->put($file_md5, file_get_contents($this->fill_attributes['image_desktop']));

      $this->attributes['image_url_desktop'] = $file_md5;

    }

    if(isset($this->fill_attributes['image_mobile']) && file_exists($this->fill_attributes['image_mobile'])){

      if(!is_file($this->fill_attributes['image_mobile'])) exc('Invalid file for image_mobile parameter');

      $file_md5 = md5_file($this->fill_attributes['image_mobile']);
      list($width, $height) = getimagesize($this->fill_attributes['image_mobile']);

      if($width / $height != 2 || $height < 400) exc('Banner mobile harus berukuran 800x400px');

      if(!Storage::disk('images')->exists($file_md5))
        Storage::disk('images')->put($file_md5, file_get_contents($this->fill_attributes['image_mobile']));

      $this->attributes['image_url_mobile'] = $file_md5;

    }

    $validator = Validator::make($this->attributes,
      [
        'type'=>[ 'required', Rule::in(self::getTypes()) ],
        'is_active'=>'required|boolean',
        'start_date'=>'required|date',
        'end_date'=>'required|date',
        'title'=>'required|unique:banner,title,' . $this->id,
        'image_url_desktop'=>'required',
        'image_url_mobile'=>'required',
        'target'=>'required|url',
      ],
      [
        'type.required'=>'Tipe harus diisi',
        'title.required'=>'Deskripsi banner harus diisi',
        'title.unique'=>'Deskripsi banner sudah ada, silakan menggunakan yang lain',
        'image_url_desktop.required'=>'Gambar desktop harus diisi',
        'image_url_mobile.required'=>'Gambar mobile harus diisi',
      ]
    );
    if($validator->fails()) exc($validator->errors()->first());

  }


  public function scopeActive($model, $type){

    $model->where('type', '=', $type)
      ->where('is_active', '=', 1)
      ->where('start_date', '<=', Carbon::now()->toDateString() . ' 00:00:00')
      ->where('end_date', '>=', Carbon::now()->toDateString() . ' 00:00:00')
      ->orderBy('sequence')
      ->orderBy('id');

  }


  public static function getTypes(){

    return [
      self::TYPE_HOME,
      self::TYPE_HOME_M2W,
      self::TYPE_SPAREPART,
    ];

  }

}
