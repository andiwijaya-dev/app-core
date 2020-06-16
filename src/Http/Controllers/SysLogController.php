<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\SysLog;
use Illuminate\Http\Request;

class SysLogController extends ListPageController2
{
  public $model = SysLog::class;

  public $title = 'Syslog';

  public $view_grid_head = 'andiwijaya::components.syslog-grid-head';
  public $view_grid_item = 'andiwijaya::components.syslog-grid-item';
  public $view_feed_item = 'andiwijaya::components.syslog-feed-item';

  public $view = 'andiwijaya::syslog';


  public function show(Request $request, $id){

    $instance = $this->model::find($id) ?? exc(__('errors.find-and-fail', [ 'model'=>__('models.article') ]));

    if($request->ajax()){

      return view_modal('andiwijaya::components.syslog-edit', [
        'id'=>'syslog-edit',
        'width'=>600,
        'data'=>compact('instance')
      ]);
    }

    abort(404);
  }

  function datasource(Request $request){

    $builder = $this->model::select('*');

    if($request->get('action') != 'reset'){

      if (method_exists(new $this->model, 'scopeFilter'))
        $builder->filter($request->all());

      if (method_exists(new $this->model, 'scopeSearch') && strlen($request->get('search')) > 0)
        $builder->search($request->get('search'));

      $this->applySorts($builder, $request->get('sorts', [ 'created_at,desc' ]));
    }

    return $builder;
  }
}