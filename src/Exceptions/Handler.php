<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;

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
        parent::report($exception);
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
          return response()->json([ 'script'=>"$.alert('Maaf, form ini tidak dapat dikirim. Silakan perbarui halaman ini dan lakukan pengisian ulang.')" ]);

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
          return redirect($request->fullUrl())->with('warning', 'Maaf, form ini tidak dapat dikirim. Silakan perbarui halaman ini dan lakukan pengisian ulang.');

      }

      return parent::render($request, $exception);
    }
}
