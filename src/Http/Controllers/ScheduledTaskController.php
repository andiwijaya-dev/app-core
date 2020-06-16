<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Illuminate\Http\Request;

class ScheduledTaskController extends ListPageController2 {

  public $model = ScheduledTask::class;

  public $exportable = false;

  public $view_detail = 'andiwijaya::components.scheduled-task-edit';
  public $view_grid_head = 'andiwijaya::components.scheduled-task-grid-head';
  public $view_grid_item = 'andiwijaya::components.scheduled-task-grid-item';
  public $view_feed_item = 'andiwijaya::components.scheduled-task-feed-item';

  public function create(Request $request){

    $task = new ScheduledTask();
    $readonly = false;

    return view_modal($this->view_detail, [
      'id'=>'scheduled-task-edit',
      'width'=>'480px',
      'data'=>compact('task', 'readonly')
    ]);
  }

  public function show(Request $request, $id){

    $task = ScheduledTask::findOrFail($id); //  TODO
    $readonly = $task->flag == 's';

    return view_modal($this->view_detail, [
      'id'=>'scheduled-task-edit',
      'width'=>'480px',
      'data'=>compact('task', 'readonly')
    ]);
  }

  public function store(Request $request){

    if($request->ajax()){

      $instance = $request->get('id') > 0 ? $this->model::find($request->get('id')) : new $this->model();
      $instance->fill($request->all());
      $instance->save();

      return view_append([
        view($this->view_feed_item, [ 'item'=>$instance ])->render(),
        view($this->view_grid_item, [ 'item'=>$instance ])->render(),
        [ 'type'=>'script', 'script'=>"$('#scheduled-task-edit').modal_close()" ]
      ]);
    }

    abort(404);
  }

  public function destroy(Request $request, $id){

    if($request->ajax()){

      $instance = $this->model::find($id) ?? exc(__('errors.find-and-fail', [ 'model'=>'Scheduled Task' ]));
      $instance->delete();

      return view_append([
        'script' => implode(';', [
          "$(\"*[data-id={$instance->id}]\").remove()",
          "$('#scheduled-task-edit').modal_close()",
        ])
      ]);
    }
  }

}