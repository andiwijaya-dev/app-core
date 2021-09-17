<?php

namespace Andiwijaya\AppCore\Exceptions;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Andiwijaya\AppCore\Models\SysLog;
use Andiwijaya\AppCore\Notifications\SlackNotification;
use App\Exceptions\APIException;
use App\Exceptions\InvalidTokenException;
use App\Notifications\LogToSlackNotification;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
      if($exception instanceof KnownException || $exception instanceof APIException ||
        $exception instanceof InvalidTokenException || $exception instanceof TokenMismatchException) return;

      $message = substr($exception->getMessage(), 0, 255);
      $traces = $exception->getTraceAsString();

      SysLog::create([
        'type'=>SysLog::TYPE_ERROR,
        'message'=>$message,
        'data'=>[
          'console'=>app()->runningInConsole(),
          'session_id'=>Session::getId(),
          'method'=>Request::method(),
          'url'=>Request::fullUrl(),
          'data'=>Request::input(null),
          'traces'=>$traces,
          'session'=>Session::get(null),
          'user_agent'=>$_SERVER['HTTP_USER_AGENT'] ?? '',
          'remote_ip'=>$_SERVER['REMOTE_ADDR'] ?? '',
          'cookies'=>$_COOKIE ?? '',
        ]
      ]);

      if(strlen(env('LOG_SLACK_WEBHOOK_URL')) > 0){
        try{
          ScheduledTask::runOnce(function() use($message, $traces){
            try{
              Notification::route('slack', env('LOG_SLACK_WEBHOOK_URL'))
                ->notify(new SlackNotification($message, 'error', $traces));
            }
            catch(\Exception $ex){
              SysLog::create([
                'type'=>SysLog::TYPE_ERROR,
                'message'=>$ex->getMessage(),
                'data'=>[
                  'traces'=>$ex->getTraceAsString(),
                ]
              ]);
            }
          });
        }
        catch(\Exception $ex){
          SysLog::create([
            'type'=>SysLog::TYPE_ERROR,
            'message'=>$ex->getMessage(),
            'data'=>[
              'traces'=>$ex->getTraceAsString(),
            ]
          ]);
        }
      }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
      if($request->ajax()){

        if ($exception instanceof TokenMismatchException)
          return htmlresponse()->redirect($request->fullUrl());

        else if($exception->getMessage() == 'Login required')
          return response()->json([ 'script'=>"window.location = '/login';" ]);

        else{

          $traces = [];
          if(env('APP_DEBUG')){
            foreach($exception->getTrace() as $idx=>$trace){
              if(isset($trace['file']) && isset($trace['class'])){
                //if($idx > 0 && strpos($trace['file'], 'Illuminate') !== false) continue;

                $traces[] = implode(' - ', [
                  isset($trace['file']) && isset($trace['line']) ? basename($trace['file']) . ":" . $trace['line'] : '',
                  isset($trace['class']) && isset($trace['function']) && isset($trace['line']) ? $trace['class'] . "@" . $trace['function'] . ":" . $trace['line'] : '',
                  isset($trace['args']) ? json_encode($trace['args']) : ''
                ]);
              }
            }
          }

          if($exception instanceof PostTooLargeException)
            return response()->json([ 'error'=>1, 'title'=>"Post data too large, maximum upload is " . ini_get('post_max_size') ]);
          else{

            if(basename($exception->getFile()) == 'FilesystemManager.php')
              return response()->json([ 'error'=>1, 'title'=>__('errors.storage-not-found'), 'description'=>implode("\n", $traces) ]);
            else
              return response()->json([ 'error'=>1, 'title'=>$exception->getMessage(), 'description'=>implode("\n", $traces) ]);
          }

        }
      }
      else{
        if ($exception instanceof TokenMismatchException)
          return redirect($request->fullUrl())->with('warning', 'Session expired, please reload the page.');
      }

      return parent::render($request, $exception);
    }
}
