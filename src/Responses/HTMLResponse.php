<?php

namespace Andiwijaya\AppCore\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\View;

class HTMLResponse implements Responsable {

  protected $data;
  protected $status;
  protected $headers;

  public function __construct($data = [], $status = 200, array $headers = [])
  {
    $this->data = $data;
    $this->status = $status;
    $this->headers = $headers;

    $this->headers['Content-Type'] = 'application/json';
  }

  public function append($view, $expr, array $data = []){

    $this->data[] = [ 'type'=>'html', 'html'=>view($view, $data)->render(), 'mode'=>'append', 'parent'=>$expr ];
    return $this;
  }

  public function prepend($view, $expr, array $data = []){

    $this->data[] = [ 'type'=>'html', 'html'=>view($view, $data)->render(), 'mode'=>'prepend', 'parent'=>$expr ];
    return $this;
  }

  public function html($view, $expr, array $data = []){

    $html = View::exists($view) ? view($view, $data)->render() : $view;
    $this->data[] = [ 'type'=>'html', 'html'=>$html, 'parent'=>$expr ];
    return $this;
  }

  public function text($text, $expr, array $data = []){

    $this->data[] = [ 'type'=>'text', 'text'=>$text, 'parent'=>$expr ];
    return $this;
  }

  public function alert($text, $type = 'error'){

    $this->data[] = [ 'type'=>$type, 'message'=>$text ];
    return $this;
  }

  public function popup($content, $ref, array $options = []){

    $html = [];

    $html[] = "<div class=\"popup\">";
    $html[] = $content;
    $html[] = "</div>";

    $this->data[] = [ 'type'=>'popup', 'ref'=>$ref, 'html'=>implode('', $html) ];

    return $this;
  }


  /**
   * @param $title
   * @param $target "<selector|top|top-right>"
   * @param array|string[] $options "{ id:<string>, system:<true|false>, description:<string> }"
   * @return $this
   */
  public function notify($title, $target, array $options = [ 'description' => '' ]){

    $this->data[] = [ 'type'=>'notify', 'title'=>$title, 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function script($script, $id = ''){

    $this->data[] = [ 'type'=>'script', 'script'=>$script, 'id'=>$id ];
    return $this;
  }

  public function replace($view, $expr, array $data = []){

    $this->data[] = [ 'type'=>'html', 'html'=>view($view, $data)->render(), 'mode'=>'replace', 'parent'=>$expr ];
    return $this;
  }

  public function modal($id, $view, array $data = [], array $options = [ 'init'=>1 ]){

    if(View::exists($view)) $view = view($view, $data)->render();

    $this->data[] = [ 'type'=>'modal', 'html'=>$view, 'id'=>$id, 'options'=>$options ];
    return $this;
  }

  public function redirect($url){

    $this->data[] = [ 'type'=>'redirect', 'target'=>$url ];
    return $this;
  }




  public function repeater($items, $view){

    $html = [];

    $html[] = "<div class=\"popup\">";
    foreach($items as $key=>$item){

      $html[] .= "<div class=\"item\">";
      $html[] = view($view, compact('item', 'key'))->render();
      $html[] = "</div>";
    }
    $html[] = "</div>";
  }



  public function toResponse($request)
  {
    return response()->json($this->data, $this->status, $this->headers);
  }
}