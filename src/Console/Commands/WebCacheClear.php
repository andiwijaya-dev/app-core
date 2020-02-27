<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Andiwijaya\AppCore\Facades\WebCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class WebCacheClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web-cache:clear {--key=} {param?} {--clear-db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $key = $this->option('key');
      $param = $this->argument('param');

      if(strlen($key) > 0){

        $count = WebCache::clearByKey($key, $this->option('clear-db'));

      }

      else if(strlen($param) > 0){

        $count = WebCache::clearByTag($param, $this->option('clear-db'));

      }

      else{

        $count = WebCache::clearAll($this->option('clear-db'));

      }

      $this->info("Cache cleared, total: {$count}");

    }
}
