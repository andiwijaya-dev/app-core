<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends BaseController{

  public $redirectTo = '/';

  public function index(Request $request){

    Auth::logout();

    Cookie::queue('kliknss_ctoken', '', 0);

    if($request->ajax())
      return [ 'redirect'=>$this->redirectTo ];

    return redirect($this->redirectTo);
  }

}