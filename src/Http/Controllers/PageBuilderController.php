<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Facades\WebCache;
use Andiwijaya\AppCore\Models\Page;
use Illuminate\Http\Request;

class PageBuilderController extends ListPageController2 {

  public $model = Page::class;
  public $view = 'andiwijaya::page-builder';
  public $view_grid_head = 'andiwijaya::components.page-builder-grid-head';
  public $view_grid_item = 'andiwijaya::components.page-builder-grid-item';
  public $view_feed_item = 'andiwijaya::components.page-builder-feed-item';

  public $view_edit = 'andiwijaya::components.page-builder-edit';

  public $url = '';
  public $content_css = '';
  public $custom_tags = [];

  public function create(Request $request){

    if($request->ajax()){

      return view_modal($this->view_edit, [
        'id'=>'page-builder-edit',
        'width'=>800,
        'height'=>1200,
        'data'=>[],
        'script'=>"tinymce.remove();tinymce_init('.section-html')"
      ]);
    }

    abort(404);
  }

  public function show(Request $request, $id){

    $page = $this->model::find($id) ?? exc(__('errors.find-and-fail', [ 'model'=>'Page' ]));

    if($request->ajax()){

      return view_modal($this->view_edit, [
        'id'=>'page-builder-edit',
        'width'=>800,
        'height'=>1200,
        'data'=>[
          'page'=>$page
        ],
        'script'=>"tinymce.remove();tinymce_init('.section-html')"
      ]);
    }

    abort(404);
  }

  public function destroy(Request $request, $id){

    if($request->ajax()){

      $instance = $this->model::find($id) ?? exc(__('errors.find-and-fail', [ 'model'=>'page' ]));
      $instance->delete();

      return view_append([
        'script' => implode(';', [
          "$(\"*[data-id={$instance->id}]\").remove()",
          "$('#page-builder-edit').modal_close()",
        ])
      ]);
    }
  }

  public function store(Request $request){

    if($request->ajax()){

      $action = $request->get('action');

      $page = $request->get('id') > 0 ? $this->model::findOrFail($request->get('id')) : new $this->model();
      $page->fill($request->all());
      $page->save();

      $return = [
        [ 'type'=>'element', 'html'=>view($this->view_feed_item, [ 'item'=>$page ])->render(), 'mode'=>'prepend' ],
        [ 'type'=>'element', 'html'=>view($this->view_grid_item, [ 'item'=>$page ])->render(), 'mode'=>'prepend', 'parent'=>'.grid-content tbody' ],
      ];
      switch($action){

        case 'save-and-close':
          $return[] = [ 'type'=>'script', 'script'=>"$('#page-builder-edit').modal_close()" ];
          break;

      }

      WebCache::clearByTag($page->path, 1);

      return $return;
    }

    abort(404);
  }

  public function getParams(): array
  {
    return [
      'content_css'=>$this->content_css,
      'custom_tags'=>$this->custom_tags
    ];
  }

}