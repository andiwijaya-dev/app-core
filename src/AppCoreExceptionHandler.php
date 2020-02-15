<?php

namespace Andiwijaya\AppCore;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class AppCoreExceptionHandler extends ExceptionHandler{

  public function report(Exception $exception)
  {
    parent::report($exception);
  }

  public function render($request, Exception $exception)
  {

    exc(233);

  }

}