<?php

namespace Andiwijaya\AppCore;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class WebHistoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(Request $request, ResponseFactory $response){

    if(config('webhistory.enabled') && !$this->app->runningInConsole()){

      $hosts = config('webhistory.hosts', []);
      $host = $request->getHost();
      $path = isset($hosts[$host]) ? $hosts[$host] : config('webhistory.path', '/t');

      if('/' . $request->path() == $path && isset($hosts[$host])){

        global $kernel;

        $data = $request->json('data');

        $this->saveHistory($request, $data);

        $kernel->terminate($request, $response);

        exit();

      }

    }

    $this->publishes(
      [
        __DIR__.'/database/webhistory/' => database_path(),
        __DIR__.'/config/webhistory/' => config_path(),
      ],
      'webhistory'
    );
  }

  public function saveHistory($request, $data){

    $queries = $params = [];

    $session_id = Crypt::decrypt($request->cookies->get(strtolower(env('APP_NAME')) . '_session'), false);

    if(is_assoc($data)) $data = [ $data ];

    if(is_array($data)){

      foreach($data as $obj){

        $type = $obj['a'] ?? '';
        $path = $obj['p'] ?? '';
        $timestamp = $obj['t'] ?? '';
        $referrer = $obj['r'] ?? ($obj['referrer'] ?? ($obj['referer'] ?? ''));
        $extra = $obj['x'] ?? null;
        $remote_ip = $request->server('REMOTE_ADDR');
        $user_agent = $request->server('HTTP_USER_AGENT');
        $created_at = Carbon::now()->format('Y-m-d H:i:s');

        $queries[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        array_push($params,
          $type,
          $session_id,
          $path,
          $referrer,
          $remote_ip,
          $user_agent,
          json_encode($extra),
          $created_at,
          $created_at,
          $timestamp / 1000
        );
      }
    }

    try{

      if(count($queries) > 0){

        DB::statement("INSERT INTO web_history(`type`, session_id, path, referrer, remote_ip, user_agent, extra, created_at, updated_at, `timestamp`) VALUES " . implode(', ', $queries), $params);
      }
    }
    catch(\Exception $ex){

      report($ex);
    }

  }

}