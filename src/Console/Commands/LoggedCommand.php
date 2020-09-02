<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class LoggedCommand extends Command {

  public function __destruct()
  {
    $id = $this->argument('id');
    file_put_contents(storage_path('logs/command.log'), implode("\n", [
        "[" . Carbon::now()->format('Y-m-d H:i:s') . "]",
        "Sparepart calculate completed. " . json_encode(array_merge($this->arguments(), $this->options())),
        "id: {$id}, ellapsed: " . (microtime(1) - LARAVEL_START),
        ""
      ]) . "\n", FILE_APPEND);
  }

}