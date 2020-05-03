<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ListPageController{

  public $model = null;

  public $title = '';

  public $extends = '';
  public $view = 'andiwijaya::list-page';
  public $view_grid_head = 'andiwijaya::components.list-page-grid-head';
  public $view_grid_item = 'andiwijaya::components.list-page-grid-item';
  public $view_feed_item = 'andiwijaya::components.list-page-feed-item';

  public $exportable = true;

  public function index(Request $request){

    $action = isset(($actions = explode('|', $request->get('action')))[0]) ? $actions[0] : '';

    if($action == 'export') return $this->export($request);


    if(!($builder = $this->datasource($request)) && class_exists($this->model)) {

      $builder = $this->model::select('*');

      if(method_exists(new $this->model, 'scopeFilter'))
        $builder->filter($request->all());

      if(method_exists(new $this->model, 'scopeSearch') && strlen($request->get('search')) > 0)
        $builder->search($request->get('search'));
    }

    $row_per_page = 15;

    $items = [];

    if($builder instanceof Builder)
    {
      switch($action){

        case 'load-more':
          $after_id = ($load_more_params = explode(',', $actions[1]))[0] ?? 0;
          $device_type = $load_more_params[1] ?? '';
          $items = collect([]);
          $should_add_items = false;
          $builder->chunk(1000, function($rows) use(&$items, $after_id, &$should_add_items, $row_per_page){

            foreach($rows as $row)
            {
              if($row->id == $after_id){
                $should_add_items = true;
                continue;
              }

              if($should_add_items)
                $items->add($row);

              if(count($items) == $row_per_page + 1) break;
            }

            if(count($items) == $row_per_page + 1) return false;

          });
          break;

        default:
          $items = $builder->limit($row_per_page + 1)->get();
          break;

      }
    }

    if(count($items) == $row_per_page + 1){

      $next_items_after = $items[count($items) - 2]->id;

      $items = $items->slice(0, $row_per_page);
    }
    else
      $next_items_after = 0;

    $params = [
      'extends'=>$this->extends,
      'title'=>$this->title,
      'view_grid_head'=>$this->view_grid_head,
      'view_grid_item'=>$this->view_grid_item,
      'view_feed_item'=>$this->view_feed_item,
      'items'=>$items,
      'next_items_after'=>$next_items_after,
      'search'=>$request->get('search'),
      'sorts'=>$request->get('sorts', []),
      'exportable'=>$this->exportable
    ];

    $params = array_merge($params, $this->getParams());

    if($request->ajax())
    {
      $sections = view($this->view, $params)->renderSections();

      switch($action){

        case 'reset':
          return [
            '.filter-cont'=>$sections['filter'],
            '.desktop-list-cont'=>$sections['desktop-list'],
            '.mobile-list-cont'=>$sections['mobile-list'],
            'script'=>implode(';', [
              "$('.list-search').val('')"
            ])
          ];

        case 'load-more':
          $return = [];

          if($device_type == 'sm'){
            $html = [];
            foreach($items as $idx=>$item){
              $html[] = view($this->view_feed_item, [ 'item'=>$item, 'idx'=>$idx ]);
            }

            $return['.feed-content'] = '>>' . implode('', $html);
          }
          else{
            $html = [];
            foreach($items as $idx=>$item){
              $html[] = view($this->view_grid_item, [ 'item'=>$item, 'idx'=>$idx ]);
            }

            $return['.grid-content tbody'] = '>>' . implode('', $html);
          }

          $load_more_html = $next_items_after > 0 ?
            "<div class=\"pad-1 align-center\"><button class=\"min load-more-btn\" name=\"action\" value=\"load-more|{$next_items_after},{$device_type}\"><label class=\"less\">Load More</label></button></div>" :
            '';
          $return['.load-more-cont'] = $load_more_html;

          return $return;

        default:
          return [
            '.desktop-list-cont'=>$sections['desktop-list'],
            '.mobile-list-cont'=>$sections['mobile-list'],
          ];

      }
    }
    else{
      return view($this->view, $params);
    }

  }

  public function applySorts($builder, array $sorts)
  {
    foreach($sorts as $sort){

      list($key, $type) = explode(',', $sort);

      $builder->orderBy($key, $type);

    }
  }

  public function getParams() : array
  {
    return [];
  }


  function datasource(Request $request){ }

  function export(Request $request){ }

}