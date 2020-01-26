<?php

namespace Andiwijaya\AppCore\Models\Traits;


trait FilterableTrait{

  /*protected $filter_searchable = [
    'id:=',
    'name:like'
  ];*/

  public function scopeFilter($model, array $params, $callback = null){

    // Handle search parameter
    if(isset($params['search']) && $params['search']){

      if(isset($this->filter_searchable) && is_array($this->filter_searchable)){

        $model->where(function($query) use($params){

          foreach($this->filter_searchable as $expr){

            list($key, $operator) = explode(':', $expr);

            switch($operator){

              case '=':
                $query->orWhere($key, '=', "{$params['search']}");
                break;

              case 'like':
                $query->orWhere($key, 'like', "%{$params['search']}%");
                break;

            }

          }

        });

      }

    }

    // Handle filter parameter
    if(isset($params['filters']) && is_array($params['filters'])){

      foreach($params['filters'] as $filter){

        $model->where(function($query) use($filter){

          $name = $filter['name'];

          foreach($filter['values'] as $idx=>$item){

            switch($item['operator']){

              case '=':
                $item['operand'] == 'or' ? $query->orWhere($name, '=', $item['value']) :
                  $query->where($name, '=', $item['value']);
                break;

              case 'contains':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "%{$item['value']}%") :
                  $query->where($name, 'like', "%{$item['value']}%");
                break;

              case 'begins_with':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "{$item['value']}%") :
                  $query->where($name, 'like', "{$item['value']}%");
                break;

              case 'ends_with':
                $item['operand'] == 'or' ? $query->orWhere($name, 'like', "%{$item['value']}") :
                  $query->where($name, 'like', "%{$item['value']}");
                break;


            }

          }

        });

      }

    }

    // Handle generic filter parameter
    foreach($params as $key=>$value){

      if(in_array($key, [ 'columns', 'filters', 'search' ])) continue;

      if(in_array($key, $this->getFillable()))
        $model->where($key, '=', $value);

    }

    if(method_exists($this, 'customFilter'))
      $this->customFilter($model, $params);

    if(is_callable($callback))
      $callback($model);

    return $model;

  }

}