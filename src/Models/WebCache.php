<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Model;

class WebCache extends Model{

  use SearchableTrait;

  protected $table = 'web_cache';

  protected $fillable = [ 'key', 'tag' ];

  protected $searchable = [
    'tag'
  ];

}
