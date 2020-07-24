<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ScheduledTask;
use Andiwijaya\AppCore\Models\ScheduledTaskResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ScheduledTaskController extends ListPageController2 {

  public $model = ScheduledTask::class;

  public $exportable = false;

  public $view_detail = 'andiwijaya::components.scheduled-task-edit';
  public $view_grid_head = 'andiwijaya::components.scheduled-task-grid-head';
  public $view_grid_item = 'andiwijaya::components.scheduled-task-grid-item';
  public $view_feed_item = 'andiwijaya::components.scheduled-task-feed-item';

  public $channel = 'scheduled-task';

  public function create(Request $request){

    $task = new ScheduledTask();
    $readonly = false;

    return view_modal($this->view_detail, [
      'id'=>'scheduled-task-edit',
      'width'=>480,
      'height'=>720,
      'data'=>compact('task', 'readonly')
    ]);
  }

  public function show(Request $request, $id){

    $method = action2method($request->get('action', 'open'));
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function store(Request $request){

    if($request->ajax()){

      $request->merge([ 'creator_id'=>Session::get('user_id') ]);

      $instance = $request->get('id') > 0 ? $this->model::find($request->get('id')) : new $this->model();
      $instance->fill($request->all());
      $instance->save();

      if($instance->repeat == ScheduledTask::REPEAT_NONE && $instance->count <= 0)
        $instance->runInBackground();

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

  public function open(Request $request, $id){

    $task = ScheduledTask::findOrFail($id); //  TODO
    $readonly = $task->flag == 's';

    $results = ScheduledTaskResult::where('task_id', $task->id)
      ->orderBy('created_at', 'desc')
      ->limit(10)
      ->get();

    return view_modal($this->view_detail, [
      'id'=>'scheduled-task-edit',
      'width'=>480,
      'height'=>720,
      'data'=>compact('task', 'results', 'readonly')
    ]);
  }

  public function openResultDetail(Request $request){

    $result = ScheduledTaskResult::findOrFail($request->get('id'));

    return [
      ".scheduled-result-stack-{$result->task_id}"=>'>>' . view('admin.sections.scheduled-task-result', compact('result'))->render(),
      'script'=>"$('.scheduled-result-stack-{$result->task_id}').stack_activate()"
    ];
  }

  public function run(Request $request, $id){

    $task = ScheduledTask::findOrFail($id); //  TODO
    $task->runInBackground();

    return [
      'script'=>''
    ];
  }

}