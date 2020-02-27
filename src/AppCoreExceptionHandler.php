<?php

namespace Andiwijaya\AppCore;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;

class AppCoreExceptionHandler extends ExceptionHandler{

  public function report(Exception $exception)
  {
    parent::report($exception);
  }

  public function render($request, Exception $exception)
  {

    if($request->ajax()){
      if($exception->getMessage() == 'Login required')
        return response()->json([ 'script'=>"window.location = '/login';" ]);
      else{

        $traces = [];
        if(env('APP_DEBUG')){
          foreach($exception->getTrace() as $idx=>$trace){
            if(isset($trace['file']) && isset($trace['class'])){
              if($idx > 0 && strpos($trace['file'], 'Illuminate') !== false) continue;

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
        else
          return  response()->json([ 'error'=>1, 'title'=>$exception->getMessage(), 'description'=>implode("\n", $traces) ]);
      }
    }

    return parent::render($request, $exception);

  }

}