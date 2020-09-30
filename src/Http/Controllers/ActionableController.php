<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Exceptions\KnownException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ActionableController extends BaseController{

  protected $request;

  public function index(Request $request){

    $this->request = $request;

    $action = isset(($actions = explode('|', $request->input('action', 'view')))[0]) ? $actions[0] : '';
    $method = action2method($action);
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function store(Request $request){

    $this->request = $request;

    $action = isset(($actions = explode('|', $request->input('action', 'save')))[0]) ? $actions[0] : '';
    $method = action2method($action);
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function show(Request $request, $id){

    $this->request = $request;

    $action = isset(($actions = explode('|', $request->input('action', 'open')))[0]) ? $actions[0] : '';
    $method = action2method($action);
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function patch(Request $request){

    $this->request = $request;

    $action = isset(($actions = explode('|', $request->input('action', 'patch')))[0]) ? $actions[0] : '';
    $method = action2method($action);
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function onlyMethods($methods){

    $arr = is_scalar($methods) ? [ $methods ] : (!is_array($methods) ? [] : $methods);

    if(!in_array($this->request->getMethod(), $arr))
      throw new KnownException(__('Action not available for this method'));

    return true;
  }


}