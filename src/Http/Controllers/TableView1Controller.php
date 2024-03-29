<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class TableView1Controller extends ActionableController
{
  protected $extends;
  protected $title;
  protected $model;
  protected $id;
  protected $columns = [
    [ 'text'=>'', 'name'=>'options', 'width'=>50 ],
    [ 'text'=>'Name', 'name'=>'name', 'width'=>260, 'filterable'=>[ 'type'=>'string' ], 'sortable'=>true ],
    [ 'text'=>'Price', 'name'=>'price', 'width'=>100, 'datatype'=>'number', 'align'=>'left', 'sortable'=>true ],
    [ 'text'=>'Date Created', 'name'=>'created_at', 'width'=>120, 'datatype'=>'datetime', 'class'=>"font-size-2", 'sortable'=>true ],
  ];

  protected $filters = [
    [ 'name'=>'name', 'type'=>'string' ],
    [ 'name'=>'price', 'type'=>'number' ],
    [ 'name'=>'created_at', 'type'=>'date' ],
  ];

  protected $searchable = true;

  protected $view = 'webfxfy::tableview1';
  protected $items_per_page = 20;
  protected $meta_row_click;

  public function view(Request $request)
  {
    View::share([
      'extends'=>$this->extends,
      'title'=>$this->title,
      'id'=>$this->id,
      'column_html'=>$this->renderHeader()
    ]);

    return view_content($this->view);
  }

  /**
   * @param Request $request
   * @return \WebFxFy\WebApp\Responses\HTMLResponse
   * @ajax true
   * @method POST
   */
  public function load(Request $request)
  {
    $this->id = $request->input('_tableview1_id');

    list($data, $page, $next, $builder) = $this->loadData($request);

    $html = [];
    if(count($data) > 0){
      foreach($data as $obj)
        $html[] = $this->renderItem($obj);
    }
    else
      $html[] = "<tr><td colspan='100'><div class='p-2 px-3'>No data</div></td></tr>";
    $html = implode('', $html);

    $response = htmlresponse();

    if($page <= 1)
      $response->value("#{$this->id}", $html, [ 'next_page'=>$next ])
        ->html(".table-foot", $this->renderFooter($builder));
    else
      $response->append("#{$this->id}", $html, [ 'next_page'=>$next ]);

    return $response;
  }

  /**
   * @param Request $request
   * @return \WebFxFy\WebApp\Responses\HTMLResponse
   * @throws \Throwable
   * @ajax true
   * @method POST
   */
  public function openFilters(Request $request)
  {
    foreach($this->filters as $idx=>$filter){
      if(($filter['type'] ?? '') == 'enum'){
        if(method_exists($this, ($method = 'enum' . ucwords(Str::camel($filter['name']))))){
          $enums = $this->{$method}($request);
          $this->filters[$idx]['enums'] = $enums;
        }
        if(!is_array($this->filters[$idx]['enums'] ?? null) || count($this->filters[$idx]['enums'] ?? []) == 0)
          $this->filters[$idx]['type'] = 'string';
      }
    }
    View::share([ 'filters'=>$this->filters ]);

    $id = explode('|', $request->input('action'))[1] ?? null;
    $value = null;

    Session::put('tableview1', $request->all());

    if($id){
      $filters = $request->input('filters', []);
      foreach($filters as $idx=>$filter)
        if(is_string($filter))
          $filters[$idx] = json_decode($filter, true);

      $value = collect($filters)->where('id', $id)->first();

      if($value){
        foreach($value['filters'] as $idx=>$exp){

          list($operand, $operator, $val) = explode('|', $exp);

          if($operator == 'in'){
            $val = explode(',', $val);
          }

          $value['filters'][$idx] = [
            'operand'=>$operand,
            'operator'=>$operator,
            'value'=>$val
          ];
        }
      }
    }

    return htmlresponse()
      ->modal(
        'tableview1-filter',
        view('webfxfy::sections.tableview1-filter', compact('value'))->render(),
        [
          'width'=>600
        ]
      );
  }

  /**
   * @param $builder
   * @param array $filters
   * @ajax true
   * @method POST
   */
  protected function applyFilters($builder, array $filters)
  {
    foreach($filters as $filter){
      $name = $filter['name'];
      $filters = $filter['filters'];

      $base_operand = null;
      $builder->where(function($query) use($name, $filters, &$base_operand){
        foreach($filters as $exp){
          list($operand, $operator, $value) = explode('|', $exp);

          if(!$base_operand) $base_operand = $operand;

          switch($operator){

            case '=':
            case '<':
            case '<=':
            case '>':
            case '>=':
              if($base_operand == 'or')
                $query->orWhere($name, $operator, $value);
              else
                $query->where($name, $operator, $value);
              break;

            case 'in':
              $value = str_replace('(empty)', '', $value);
              $value = explode(',', $value);
              foreach($value as $val)
                if(empty($val))
                  $query->orWhereNull($name);
              $query->orWhereIn($name, $value);
              break;

            case 'contains':
              if($base_operand == 'or')
                $query->orWhere($name, 'like', "%{$value}%");
              else
                $query->where($name, 'like', "%{$value}%");
              break;

            case 'starts-with':
              if($base_operand == 'or')
                $query->orWhere($name, 'like', "{$value}%");
              else
                $query->where($name, 'like', "{$value}%");
              break;

            case 'ends-with':
              if($base_operand == 'or')
                $query->orWhere($name, 'like', "%{$value}");
              else
                $query->where($name, 'like', "%{$value}");
              break;

          }
        }
      });
    }
  }

  /**
   * @param Request $request
   * @return \WebFxFy\WebApp\Responses\HTMLResponse
   * @throws \Throwable
   * @throws \WebFxFy\WebApp\Exceptions\UserException
   * @ajax true
   * @method POST
   */
  protected function addFilter(Request $request)
  {
    $id = $request->input('id');
    $name = $request->input('name');
    $params = $request->input('params', []);
    $type = collect($this->filters)->where('name', $name)->first()['type'] ?? 'string';

    $filters = [];
    for($i = 0 ; $i < count($params) / 3 ; $i++){

      $operand = $params[$i * 3]['operand'];
      $operator = $params[($i * 3) + 1]['operator'];
      $value = $params[($i * 3) + 2]['value'] ?? '';

      switch($type){
        case 'date':
          $value = date('Y-m-d', strtotime($value));
          break;
        case 'bool':
          if(!$value) $value = 0;
          break;
      }

      if($operator == 'in'){
        $value = [];
        for($i = 2 ; $i < count($params) ; $i++)
          $value[] = $params[$i]['value'] ?? '';
        $value = implode(',', $value);
      }

      if($value !== ''){
        $filters[] = $operand . '|' . $operator . '|' . $value;
      }
    }

    if(count($filters) <= 0)
      exc(__('models.tableview1-no-filter-value'));

    $filter = [
      'id'=>$id ?? uniqid(),
      'name'=>$name,
      'text'=>collect($this->filters)->where('name', $name)->first()['text'] ?? $name,
      'filters'=>$filters
    ];

    $current = Session::get('tableview1');
    unset($current['action']);

    if($id){
      foreach($current['filters'] as $idx=>$item){

        if(is_string($item)){
          $item = json_decode($item, true);
          $current['filters'][$idx] = $item;
        }

        if($item['id'] == $id)
          $current['filters'][$idx] = $filter;
      }
    }
    else
      $current['filters'][] = $filter;

    $request->offsetUnset('params');
    $request->offsetUnset('name');
    $request->offsetUnset('id');
    $request->offsetUnset('action');
    $request->merge($current);
    $response = $this->load($request);

    if($id){
      $response->replace('#filter-item-' . $id, view('webfxfy::components.tableview1-filter-item', compact('filter'))->render());
    }
    else
      $response->append('.filter-area', view('webfxfy::components.tableview1-filter-item', compact('filter'))->render());

    $response->script("ui('#tableview1-filter').modal_close()");

    return $response;
  }

  /**
   * @param Request $request
   * @return \WebFxFy\WebApp\Responses\HTMLResponse
   * @ajax true
   * @method POST
   */
  protected function sort(Request $request)
  {
    $sorts = $request->input('sorts', []);

    $name = explode('|', $request->input('action'))[1] ?? null;

    if(count($sorts) == 0)
      $sorts = [ $name . '|asc' ];
    else{

      $exists_and_inverted = false;
      foreach($sorts as $idx=>$sort){
        list($sort_name, $sort_type) = explode('|', $sort);
        if($sort_name == $name){
          $sort_type = $sort_type == 'desc' ? 'asc' : 'desc';
          $sorts[$idx] = $name . '|' . $sort_type;
          $exists_and_inverted = true;
        }
      }

      if(!$exists_and_inverted)
        $sorts = [ $name . '|asc' ];
    }

    $request->merge([
      'sorts'=>$sorts,
      'action'=>'load'
    ]);

    $response = $this->load($request);
    $response->remove("input[name='sorts[]']");
    foreach($sorts as $sort)
      $response->append("th[name='$name']", "<input type='hidden' name='sorts[]' value=\"{$sort}\" />");
    return $response;
  }


  protected function columnImage($obj, $column)
  {
    return <<<EOF
<div class="p-1">
  <div data-type="img" class="b-3 rounded-2 ratio-1-1 relative" data-src="{$obj->image_url}">
    <div class="dock-center no-image-img">
      <span class="fa fa-image font-size-6 cl-gray-300"></span>
    </div>
  </div>
</div>
EOF;
  }

  protected function columnOptions($obj, $column)
  {
    return <<<EOF
<div class="align-center">
  <a href="/module/{$obj['id']}" class="async" data-history="none"><span class="fa fa-bars cl-gray-400 p-1"></span></a>
  <a href="/module/{$obj['id']}" class="async" data-history="none" data-method="DELETE" data-confirm="Hapus?"><span class="fa fa-times cl-gray-300 p-1"></span></a>
</div>
EOF;
  }

  protected function getBuilder(Request $request)
  {
    return $this->model::whereRaw('1=1');
  }

  protected function renderFooter($builder)
  {
    return '';
  }

  protected function renderHeader(array $options = [])
  {
    $html = [];
    $columns = $this->columns;
    foreach($columns as $column){

      $name = $column['name'] ?? '';
      $width = $column['width'] ?? 100;
      $text = $column['text'] ?? ($column['name'] ?? '');
      $datatype = $column['datatype'] ?? 'text';
      $align = $column['align'] ?? '';
      $sortable = $options['sortable'] ?? ($column['sortable'] ?? false);

      switch($datatype)
      {
        case 'bool':
        case 'boolean':
          if(!$align) $align = 'align-center';
          break;

        case 'number':
          if(!$align) $align = 'align-right';
          break;
      }

      $html[] = "<th class='{$align}' width=\"{$width}px\" name=\"{$name}\">";
      if($sortable)
        $html[] = "<button name='action' value=\"sort|{$name}\">{$text}</button>";
      else
        $html[] = $text;
      $html[] = "<div class=\"table-resize\"></div>";
      $html[] = "</th>";
    }
    $html[] = '<th width="100%"></th>';

    return implode('', $html);
  }

  protected function renderItem($obj){

    $id = $obj['id'] ?? '';
    $tag = "<tr data-id=\"{$id}\" class='tableview1-row'>";
    foreach($this->columns as $column){

      $name = $column['name'] ?? '';
      $text = $obj[$name] ?? '';
      $datatype = $column['datatype'] ?? 'text';
      $align = $column['align'] ?? '';
      $class = $column['class'] ?? '';

      switch($datatype){

        case 'bool':
        case 'boolean':
        case 'sort-order':
          if(!$align) $align = 'align-center';
          break;

        case 'number':
          $text = number_format(doubleval($text));
          if(!$align) $align = 'align-right';
          break;

        case 'date':
          $dateformat = $column['dateformat'] ?? 'j M Y';
          $text = date('Y', strtotime($text)) > 1970 ? date($dateformat, strtotime($text)) : '';
          break;

        case 'datetime':
          $dateformat = $column['dateformat'] ?? 'j M Y H:i';
          $text = date('Y', strtotime($text)) > 1970 ? date($dateformat, strtotime($text)) : '';
          break;
      }

      $tag .= "<td class='{$align}'>";
      switch($datatype){

        case 'bool':
        case 'boolean':
          if($text)
            $tag .= "<label class='ellipsis'><span class='fa fa-check-circle cl-green'></span></label>";
          else
            $tag .= "<label class='ellipsis'><span class='fa fa-minus-circle cl-gray-500'></span></label>";
          break;

        case 'sort-order':
          $tag .= "<span class=\"fa fa-grip-vertical cl-primary p-1\" data-event
        data-mousedown-start-reorder=\"parent(.tableview1-row)|tableview1-row\"></span>";
          $tag .= "<span class=\"fa fa-arrow-up cl-primary p-1\" data-event data-click-reorder-up='parent(.tableview1-row)'></span>";
          $tag .= "<span class=\"fa fa-arrow-down cl-primary p-1\" data-event data-click-reorder-down='parent(.tableview1-row)'></span>";
          $tag .= "<input type='hidden' name='sort_order[]' value='{$obj->id}' />";
          break;

        default:
          if(method_exists($this, ($method = 'column' . ucwords(Str::camel($name))))){
            $tag .= $this->$method($obj, $column);
          }
          else{
            $tag .= "<label class=\"ellipsis {$class}\">{$text}</label>";
          }
      }
      $tag .= "</td>";
    }
    $tag .= '<td width="100%"></td>';
    $tag .= "</tr>";

    return $tag;
  }

  protected function loadData(Request $request)
  {
    $page = explode('|', $request->input('action'))[1] ?? 1;

    $sorts = $request->input('sorts', []);

    $filters = $request->input('filters', []);
    foreach($filters as $idx=>$filter)
      if(is_string($filter))
        $filters[$idx] = json_decode($filter, 1);

    $builder = $this->getBuilder($request);

    $this->applyFilters($builder, $filters);

    if($request->has('search') && $this->searchable)
      $builder->search($request->input('search'));

    foreach($sorts as $sort){
      list($sort_name, $sort_type) = explode('|', $sort);
      $builder->orderBy($sort_name, $sort_type);
    }

    $offset = ($page - 1) * $this->items_per_page;
    $data = $builder->limit($this->items_per_page + 1)->offset($offset)->get();
    $next = count($data) > $this->items_per_page ? $page + 1 : -1;
    $data = $data->splice(0, $this->items_per_page);

    return [ $data, $page, $next, $builder ];
  }

  public function __construct()
  {
    if(!$this->id) $this->id = 'tableview1-' . md5(get_class($this));

    parent::__construct();
  }
}