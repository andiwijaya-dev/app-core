<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class LogoutController extends BaseController{

  public $redirectTo = '/';

  public function index(Request $request){

    Auth::logout();

    if($request->ajax())
      return [ 'redirect'=>$this->redirectTo ];

    return redirect($this->redirectTo);
  }

}