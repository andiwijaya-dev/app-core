<?php

namespace Andiwijaya\AppCore\Models\Traits;


trait OverviewTrait{

  public static function getOverview(){

    $items = self::limit(10)->get();

    $arr = [
      'total'=>count($items),
      'items'=>$items
    ];

    if(method_exists(($instance = new self), 'getFillable')){

      if(in_array('rate', $instance->getFillable())){
        $arr['min_rate'] = self::all()->min('rate');
        $arr['max_rate'] = self::all()->max('rate');
        $arr['avg_rate'] = self::all()->avg('rate');
      }

      if(in_array('amount', $instance->getFillable())){
        $arr['min_amount'] = self::all()->min('amount');
        $arr['max_amount'] = self::all()->max('amount');
        $arr['avg_amount'] = self::all()->avg('amount');
      }

    }

    return (object) $arr;

  }

}