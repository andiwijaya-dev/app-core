<?php

namespace Andiwijaya\AppCore\Models\Traits;

trait SearchableTrait{

  /*protected $searchable = [];*/

  /*protected $lookup_rules = [
    'city_id'=>'id',
    'city_code'=>'code',
    'city_name'=>'name',
    'city'=>'code,name',
  ];*/

  /**
   * Replaces spaces with full text search wildcards
   *
   * @param string $term
   * @return string
   */
  protected function fullTextWildcards($term)
  {
    // removing symbols used by MySQL
    $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
    $term = str_replace($reservedSymbols, ' ', $term);

    $words = explode(' ', $term);

    foreach($words as $key => $word){
      /*
       * applying + operator (required word) only big words
       * because smaller ones are not indexed by mysql
       */
      if(strlen($word) >= 3) {
        $words[$key] = '+' . $word . '*';
      }
    }

    $searchTerm = implode( ' ', $words);

    return $searchTerm;
  }

  /**
   * Scope a query that matches a full text search of term.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $term
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeSearch($query, $term)
  {
    $columns = implode(',',$this->searchable);

    //$query->whereRaw("MATCH ({$columns}) AGAINST (?)" , $this->fullTextWildcards($term));
    $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));

    return $query;
  }



  function scopeLookup($model, array $obj){

    if(isset($this->lookup_rules) && is_array($this->lookup_rules)){

      $has_lookup = false;

      foreach($this->lookup_rules as $key=>$rules){

        if(!isset($obj[$key])) continue;

        if(strpos($rules, '+') !== false){

          $rules = explode('+', $rules);

          foreach($rules as $idx=>$rule)
            $rules[$idx] = "COALESCE(`{$rule}`, '')";

          $model->orWhereRaw("CONCAT(`" . implode("`, ' ', `", $rules) . "`) = '$obj[$key]'");

        }
        else{

          $rules = explode(',', $rules);

          $model->orWhere(function($query) use($key, $rules, $obj){
            foreach($rules as $rule)
              $query->orWhere($rule, 'like', isset($obj[$key]) ? $obj[$key] : '');
          });

        }

        $has_lookup = true;

      }

      if(!$has_lookup)
        $model = $model->whereRaw('1 = 0');

    }

  }

}