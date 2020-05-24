<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController
{
  public $view = 'andiwijaya::login';

  public $redirectTo = '';

  public function index(Request $request)
  {
    return view($this->view);
  }

  public function store(Request $request)
  {
    try{

      Auth::login($request->only('user_id', 'password'));

      $redirect_to = Session::pull('after_login_redirect', $this->redirectTo);

      if($redirect_to == '/') $redirect_to = '';

      return $request->ajax() ? [ 'redirect'=>'/' . $redirect_to ] : redirect('/' . $redirect_to);

    }
    catch(\Exception $ex){

      return $request->ajax() ? [ '.error-badge'=> "<label class=\"pad-1 block\">{$ex->getMessage()}</label>" ] :
        back()->withErrors([ 'error', $ex->getMessage() ]);

    }
  }

}