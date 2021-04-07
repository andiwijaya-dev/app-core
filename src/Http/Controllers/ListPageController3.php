<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Illuminate\Http\Request;

class ListPageController3 extends ActionableController{

  protected $title = 'Untitled';
  protected $model = null;
  protected $columns;
  protected $presets;

  protected $view_extends = 'andiwijaya::layouts.list-page-3';
  protected $view = 'andiwijaya::list-page-3';

  public function view(Request $request){

    return view($this->view);
  }

  protected function loadColumns(){}
  protected function loadPresets(){

    $this->presets = [
      [
        'name'=>'Default'
      ]
    ];
  }

  public function index(Request $request)
  {
    $this->loadColumns();
    $this->loadPresets();

    return parent::index($request);
  }
  public function store(Request $request)
  {
    $this->loadColumns();
    $this->loadPresets();

    return parent::store($request);
  }
}