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

  public function append($target, $html){

    $this->data[] = [ 'type'=>'html', 'html'=>$html, 'mode'=>'append', 'target'=>$target ];
    return $this;
  }

  public function prepend($target, $html){

    $this->data[] = [ 'type'=>'html', 'html'=>$html, 'mode'=>'prepend', 'target'=>$target ];
    return $this;
  }

  public function html($target, $html){

    $this->data[] = [ 'type'=>'html', 'html'=>$html, 'target'=>$target ];
    return $this;
  }

  public function replace($target, $html){

    $this->data[] = [ 'type'=>'html', 'html'=>$html, 'mode'=>'replace', 'target'=>$target ];
    return $this;
  }

  public function text($text, $expr, array $data = []){

    $this->data[] = [ 'type'=>'text', 'text'=>$text, 'target'=>$expr ];
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

  public function modal($id, $view, array $data = [], array $options = [ 'init'=>1 ]){

    if(View::exists($view)) $view = view($view, $data)->render();

    $this->data[] = [ 'type'=>'modal', 'html'=>$view, 'id'=>$id, 'options'=>$options ];
    return $this;
  }

  public function redirect($url){

    $this->data[] = [ 'type'=>'redirect', 'target'=>$url ];
    return $this;
  }






  public function toResponse($request)
  {
    return response()->json($this->data, $this->status, $this->headers);
  }
}