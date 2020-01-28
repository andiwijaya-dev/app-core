<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\User;
use App\Models\Category;
use Faker\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CMSListController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $default_columns = [];

  protected $columns = [];

  protected $module = '';

  protected $title = '';

  protected $list_view = 'andiwijaya::cms-list';

  protected $model = null;


  public function __construct(){

    $this->middleware(function($request, $next){
      if(Session::has("states.{$this->module}.columns"))
        $this->columns = Session::get("states.{$this->module}.columns");
      else
        $this->columns = $this->default_columns;

      return $next($request);
    });

  }

  public function index(Request $request){

    if($request->wantsJson()) return $this->indexJson($request);

    // Find action
    $actions = explode('|', $request->get('action'));
    $action = isset($actions[0]) ? $actions[0] : '';

    if($request->has('reset')){
      Session::put("states.{$this->module}.columns", ($this->columns = $this->default_columns));
      Session::put("states.{$this->module}.search", '');
      Session::put("states.{$this->module}.filters", []);
    }

    // Apply columns process
    switch($action){

      case 'apply-columns':
        foreach($this->columns as $idx=>$column){
          $this->columns[$idx]['active'] = $request->has($column['name']) ? 1 : 0;
        }
        Session::put("states.{$this->module}.columns", $this->columns);
        break;

      case 'apply-filters':
        Session::put("states.{$this->module}.filters", $request->get('filters', []));
        break;

      case 'resize-column':
        $values = explode('=', $request->get('value'));
        $this->columns[$values[0]]['width'] = $values[1];
        Session::put("states.{$this->module}.columns", $this->columns);
        break;

    }

    if($request->has('search')) Session::put("states.{$this->module}.search", $request->get('search'));

    $page = $request->get('page');
    if(in_array($action, [ 'search' ])) $page = 1;
    $filters = Session::get("states.{$this->module}.filters", []);
    $search = Session::get("states.{$this->module}.search", '');

    // Fetch data
    $model = $this->model;
    $model = $model::filter(
      array_merge($request->all(),
        [
          'columns'=>$this->columns,
          'filters'=>$filters,
          'search'=>$search
        ]
      ), function($model){
      $model->orderBy('updated_at', 'desc');
    });
    $items = $model->paginate(18, ['*'], 'page', $page);

    // Render response
    $params = $this->getParams($request);
    $params['page'] = $page;
    $params['module'] = $this->module;
    $params['title'] = $this->title;
    $params['columns'] = $this->columns;
    $params['filters'] = $filters;
    $params['search'] = $search;
    $params['items'] = $items;
    $params['controller'] = $this;
    $params['channel'] = $channel = implode('-', [ 'cms_list', (new \ReflectionClass($this->model))->getShortName(), Session::get('user_id') ]);

    Redis::set($channel, json_encode(array_merge($params, [
      'view'=>$this->list_view,
      'section'=>'items'
    ])));

    if($request->ajax()){

      $grid_id = '#' . Str::slug($this->module);

      $return = [];
      switch($action) {

        case 'select-column':
          $return['_'] = view('andiwijaya::components.columns-select', $params)->render();
          $return['script'] = "$('#columns-modal').open()";
          break;

        case 'apply-columns':
          $sections = view($this->list_view, $params)->renderSections();

          $return["{$grid_id} thead"] = $sections['header'];
          $return["{$grid_id} tbody"] = ($page > 1 ? '>>' : '') . ($params['items']->total() > 0 ? $sections['items'] : "");
          $return["{$grid_id} tfoot"] = $sections['paging'];
          $return['script'] = "$('#columns-modal').close();$('." . (Str::slug($this->module)) . "-grid').grid()";
          break;

        case 'open-filter':
          $return['_'] = view('andiwijaya::components.grid-filter', $params)->render();
          $return['script'] = "$('#filter-modal').open({ width:'50%', height:'70%'})";
          break;

        case 'apply-filters':
          $sections = view($this->list_view, $params)->renderSections();

          $return['#' . Str::slug($this->module) . ' tbody'] = ($page > 1 ? '>>' : '') . ($params['items']->total() > 0 ? $sections['items'] : "");
          $return['#' . Str::slug($this->module) . ' tfoot'] = $sections['paging'];
          $return['script'] = implode(';', [
            "$('#filter-modal').close()",
            "$('{$grid_id}').grid_update()"
          ]);
          break;

        case 'resize-column':
          break;

        default:
          $sections = view($this->list_view, $params)->renderSections();

          $return["{$grid_id} tbody"] = ($page > 1 ? '>>' : '') . ($params['items']->total() > 0 ? $sections['items'] : "");
          $return["{$grid_id} tfoot"] = $sections['paging'];
          $return['script'] = "$('{$grid_id}').grid_update()";
          break;
      }
      return $return;

    }

    else
      return view($this->list_view)->with($params);

  }

  public function indexJson(Request $request){

    $model = $this->model;
    $model = $model::filter($request->all());
    $items = $model->paginate(18, ['*'], 'page', $request->get('page', 1));

    return $items;

  }


  public function create(Request $request){

    if($request->ajax()){

      // Render response
      $params = $this->getParams($request);
      $params['module'] = $this->module;
      $params['title'] = $this->title;
      $params['columns'] = $this->columns;
      $params['filters'] = Session::get("states.{$this->module}.filters", []);
      $params['search'] = Session::get("states.{$this->module}.search", '');
      $params['item'] = [];
      $params['controller'] = $this;
      $sections = view($this->list_view, $params)->renderSections();

      return [
        '_'=>$sections['detail'],
        'script'=>"$('#" . Str::slug($this->module) . "-detail').open()"
      ];

    }

  }

  public function show(Request $request, $id){

    // Fetch data
    $model = $this->model;
    $item = $model::where('id', '=', $id)->first();

    // Render response
    $params = $this->getParams($request);
    $params['module'] = $this->module;
    $params['title'] = $this->title;
    $params['columns'] = $this->columns;
    $params['filters'] = Session::get("states.{$this->module}.filters", []);
    $params['search'] = Session::get("states.{$this->module}.search", '');
    $params['item'] = $item;
    $params['controller'] = $this;

    $parent = $request->get('parent');

    if($request->ajax()){

      $sections = view($this->list_view, $params)->renderSections();

      return [
        '_'=>$sections['detail'],
        'script'=>"$('#" . Str::slug($this->module) . "-detail').open({ parent:'{$parent}' })"
      ];

    }

  }

  public function store(Request $request){

    // Find action
    $actions = explode('|', $request->get('action'));
    $action = isset($actions[0]) ? $actions[0] : '';

    // Prepare saved object
    $obj = $request->all();

    if($request->ajax()){

      // Perform save
      $model = $this->model;


      $instance = $model::updateOrCreate([ 'id'=>$request->get('id') ], $obj);

      switch($action){

        case 'save':

          // Render response
          $params = $this->getParams($request, [
            'items'=>[ $instance ],
            'columns'=>$this->columns,
            'module'=>$this->module,
            'title'=>$this->title,
            'search'=>'',
            'controller'=>$this
          ]);
          $sections = view($this->list_view, $params)->renderSections();
          return $instance->wasRecentlyCreated ?
            array_merge(
              [
                "script"=>"$.notify({ title:'Data {$instance->name} berhasil disimpan.', timeout:3000 })"
              ],
              !redis_available() ? [ "." . (Str::slug($this->module)) . "-grid tbody"=>'<<' . trim($sections['items']) ] : []
            )
            :
            array_merge(
              [
                "script"=>"$.notify({ title:'Data {$instance->name} berhasil diupdate.', timeout:3000 })",
              ],
              !redis_available() ? [ "." . (Str::slug($this->module)) . "-grid tr[data-id={$instance->id}]"=>substr(trim($sections['items']), strpos(trim($sections['items']), '<tr') + 3, strrpos(trim($sections['items']), '</tr>') - 5) ] : []
            );


        case 'save-and-close':

          // Render response
          $params = $this->getParams($request, [
            'items'=>[ $instance ],
            'columns'=>$this->columns,
            'module'=>$this->module,
            'title'=>$this->title,
            'search'=>'',
            'controller'=>$this
          ]);
          $sections = view($this->list_view, $params)->renderSections();
          return $instance->wasRecentlyCreated ?
            array_merge(
              [
                "script"=>"$('#" . Str::slug($this->module) . "-detail').close();$.notify({ title:'Data {$instance->name} berhasil disimpan.', timeout:3000 })",
              ],
              !redis_available() ? [ "." . (Str::slug($this->module)) . "-grid tbody"=>'<<' . trim($sections['items']) ] : []
            )
            :
            array_merge(
              [
                "script"=>"$('#" . Str::slug($this->module) . "-detail').close();$.notify({ title:'Data {$instance->name} berhasil diupdate.', timeout:3000 })",
              ],
              !redis_available() ? [ "." . (Str::slug($this->module)) . "-grid tr[data-id={$instance->id}]"=>substr(trim($sections['items']), strpos(trim($sections['items']), '<tr') + 3, strrpos(trim($sections['items']), '</tr>') - 5) ] : []
            );

      }


    }

  }

  public function destroy(Request $request, $id){

    // Perform delete
    $model = $this->model;
    $instance = $model::where('id', '=', $id)->first();
    $instance->delete();

    if($request->ajax()){

      // Render response
      return [
        'script'=>implode(';', [
          !redis_available() ? "$('." . (Str::slug($this->module)) . "-grid tr[data-id={$id}]').remove()" : '',
          "$.notify({ title:'{$this->module} {$instance->name} berhasil dihapus.', timeout:3000 })"
        ])
      ];

    }

  }




  public function getParams(Request $request, array $params = []){

    $faker = Factory::create();

    $obj = [
      'faker'=>$faker,
      'user'=>User::where('id', '=', Session::get('user_id'))->first()
    ];

    $obj = array_merge($obj, $params);

    return $obj;

  }

}