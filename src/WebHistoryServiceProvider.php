<?php

namespace Andiwijaya\AppCore;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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

      if('/' . $request->path() == '/t' && in_array($request->getHost(), config('webhistory.hosts', []))){

        global $kernel;

        try{

          $session_id = Crypt::decrypt($request->cookies->get(strtolower(env('APP_NAME')) . '_session'), false);
          $type = $request->input('a');
          $path = $request->input('p');
          $timestamp = $request->input('t');
          $referrer = $request->input('r', $request->input('referrer', $request->input('referer')));
          $remote_ip = $request->server('REMOTE_ADDR');
          $user_agent = $request->server('HTTP_USER_AGENT');
          $created_at = Carbon::now()->format('Y-m-d H:i:s');

          if($type > 0 && !empty(trim($path))){
            DB::statement("INSERT INTO web_history(`type`, session_id, path, referrer, remote_ip, user_agent, created_at, updated_at, `timestamp`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
              $type,
              $session_id,
              $path,
              $referrer,
              $remote_ip,
              $user_agent,
              $created_at,
              $created_at,
              $timestamp / 1000
            ]);
          }
        }
        catch(\Exception $ex){

          report($ex);
        }

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

}