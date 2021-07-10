<?php

namespace Andiwijaya\AppCore\Traits;

trait CommandExt{

  public function preventOverlapping(){

    $rawCommand = 'php ' . str_replace("'", '', ((string) $this->input));
    $output = '';
    exec("ps ax | grep \"{$rawCommand}\"", $output);
    $this->info(json_encode($output, JSON_PRETTY_PRINT));

    if(count($output) > 1)
      exc('Process already exists and running.');
  }

}