<?php

namespace Andiwijaya\AppCore\Models\Traits;

trait OutputtableTrait{

  protected $verbose;

  public function setVerboseOutput($output){

    $this->verbose = $output;
  }

  public function verbose($message, $type = 0){

    if($this->verbose)
      switch($type){
        case 1: $this->verbose->error($message); break;
        case 2: $this->verbose->warn($message); break;
        default: $this->verbose->info($message); break;
      }
  }

}