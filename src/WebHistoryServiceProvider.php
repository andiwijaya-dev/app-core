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

        /*if($request->ip() == '103.164.223.110')
          file_put_contents(storage_path('logs/debug.log'), json_encode([
            Carbon::now()->format('Y-m-d H:i:s'),
            $request->header('Content-Type'),
            $request->input(null),
            $request->getContent()
          ], JSON_PRETTY_PRINT));*/

        if(strpos($request->header('Content-Type'), 'text/plain') !== false){
          $data = json_decode($request->getContent(), true);
        }
        else{
          $data = $request->input('data');
        }

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

    $session_cookie_value = $request->cookies->get(strtolower(env('APP_NAME')) . '_session');
    $session_id = $session_cookie_value ? Crypt::decrypt($session_cookie_value, false) : 'N/A';

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
