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

  public function append($target, $value, $options = [])
  {
    $this->data[] = [ '_type'=>'append', 'value'=>$value, 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function addImage($target, $image_url){

    $this->data[] = [ '_type'=>'method', 'method'=>'addImage', 'target'=>$target, 'params'=>[ $image_url ] ];
    return $this;
  }

  public function toast($title, $icon = '', $type = ''){

    $this->data[] = [
      '_type'=>'toast',
      'title'=>$title,
      'type'=>$type,
      'icon'=>$icon
    ];
    return $this;
  }

  public function prepend($target, $html, $options = [])
  {
    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'mode'=>'prepend', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function html($target, $html, $options = []){

    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function replace($target, $html, $options = []){

    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'mode'=>'replace', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function replaceOrAppend($target, $html, $options = [])
  {
    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'mode'=>'replace-or-append', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function replaceOrPrepend($target, $html, $options = [])
  {
    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'mode'=>'replace-or-prepend', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function remove($target, $options = []){

    $this->data[] = [ '_type'=>'remove', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function update($target, array $options = [])
  {
    $this->data[] = [ '_type'=>'method', 'method'=>'update', 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function value($target, $value, array $options = [])
  {
    $this->data[] = [ '_type'=>'value', 'target'=>$target, 'value'=>$value, 'options'=>$options ];
    return $this;
  }

  public function text($text, $expr, array $data = []){

    $this->data[] = [ '_type'=>'text', 'text'=>$text, 'target'=>$expr ];
    return $this;
  }

  public function script($script, $id = ''){

    $this->data[] = [ '_type'=>'script', 'script'=>$script, 'id'=>$id ];
    return $this;
  }

  public function table_append($target, $params){

    $this->data[] = [ '_type'=>'method', 'target'=>$target, 'method'=>'table_append', 'params'=>$params ];
    return $this;
  }

  public function form_errors($target, $errors, $options = []){

    $this->data[] = [ '_type'=>'method', 'target'=>$target, 'method'=>'form_errors', 'params'=>[ $errors ] ];
    return $this;
  }

  public function htmlRequire($src, array $options = [])
  {
    $this->data[] = [ '_type'=>'require', 'src'=>$src, 'options'=>$options ];
    return $this;
  }


  public function alert($text, $type = 'error', $options = []){

    $title = $text['title'] ?? $text;
    $description = $text['description'] ?? '';

    $this->data[] = [
      '_type'=>'alert',
      'type'=>$type,
      'text'=>[ 'title'=>nl2br($title), 'description'=>$description ],
      'options'=>$options
    ];
    return $this;
  }

  public function click($target){

    $this->data[] = [
      '_type'=>'click',
      'target'=>$target
    ];
    return $this;
  }

  public function chart($target, $type, array $labels, array $data, array $options = []){

    $colors = [
      '#4A89DC',
      '#E9573F',
      '#3BAFDA',
      '#37BC9B',
      '#F6BB42',
      '#E9573F',
      '#DA4453',
      '#967ADC',
      '#D770AD',
      '#434A54'
    ];

    $datasets = [];
    $counter = 0;
    foreach($data as $idx=>$arr){
      $dataset = [
        'label'=>$idx,
        'data'=>$arr,
        'fill'=>false,
        'borderColor'=>$colors[$counter] ?? 'rgba(0, 0, 0, 1)'
      ];
      $datasets[] = $dataset;

      $counter++;
    }

    $params = [
      '_type'=>$type,
      'data'=>[
        'labels'=>$labels,
        'datasets'=>$datasets
      ],
      'options'=>[
        'scales'=>[
          'yAxes'=>[
            [
              'ticks'=>[
                'beginAtZero'=>true,
                'display'=>false
              ]
            ]
          ]
        ]
      ]
    ];

    $id = 'chart' . uniqid();
    $html[] = "<canvas id='{$id}'></canvas>";
    $this->data[] = [ '_type'=>'html', 'html'=>implode('', $html), 'target'=>$target ];
    $this->data[] = [ '_type'=>'script', 'script'=>"new Chart('{$id}', " . json_encode($params) . ");" ];

    return $this;
  }

  public function consoleError($text){

    $this->data[] = [ '_type'=>'console.error', 'text'=>$text ];

    return $this;
  }

  public function consoleInfo($text){

    $this->data[] = [ '_type'=>'console.info', 'text'=>$text ];

    return $this;
  }

  public function consoleWarn($text){

    $this->data[] = [ '_type'=>'console.warn', 'text'=>$text ];

    return $this;
  }

  public function grid($target, $data, $columns, array $options = []){

    $onitemclick = $options['onitemclick'] ?? '';

    $html_columns = [];
    foreach($columns as $key=>$column){

      $width = $column['width'] ?? '';
      $align = $column['align'] ?? '';
      $text = $column['text'] ?? '';

      $html_columns[] = "<th width='{$width}' align='{$align}'>{$text}</th>";
    }
    $html_columns[] = "<th></th>";
    $html_columns = implode('', $html_columns);

    $html_data = [];
    $html_data[] = "<tr>";
    foreach($columns as $key=>$column){
      $width = $column['width'] ?? '';
      $html_data[] = "<td width='{$width}'></td>";
    }
    $html_data[] = "<td></td>";
    $html_data[] = "</tr>";
    foreach($data as $obj){
      $html_data[] = "<tr onclick=\"{$onitemclick}\">";
      foreach($columns as $key=>$column){

        $align = $column['align'] ?? '';
        $value = $obj[$key] ?? '';

        if(isset($column['format']))
          $value = call_user_func_array($column['format'], [ $value, $obj ]);

        $html_data[] = "<td align='{$align}' data-key='{$key}'>{$value}</td>";
      }
      $html_data[] = "<td></td>";
      $html_data[] = "</tr>";
    }
    $html_data = implode('', $html_data);


    $html = <<<EOT
 <div data-type="grid">
        <div class="grid-head">
          <table>
            <tr>{$html_columns}</tr>
          </table>
        </div>
        <div class="grid-body">
          <table>
            {$html_data}
          </table>
        </div>
      </div>
EOT;

    $this->data[] = [ '_type'=>'html', 'html'=>$html, 'target'=>$target ];

    return $this;
  }

  public function popup($id, $html, array $options = []){

    $this->data[] = [ '_type'=>'popup', 'id'=>$id, 'html'=>$html, 'options'=>$options ];

    return $this;
  }
  
  public function call($fn, $params = [], $thisArg = null){

    $this->data[] = [ '_type'=>'call', 'fn'=>$fn, 'params'=>$params, 'thisArg'=>$thisArg ];

    return $this;
  }

  /**
   * @param $title
   * @param $target "<selector|top|top-right>"
   * @param array|string[] $options "{ id:<string>, system:<true|false>, description:<string> }"
   * @return $this
   */
  public function notify($title, $target, array $options = [ 'description' => '' ]){

    $this->data[] = [ '_type'=>'notify', 'title'=>$title, 'target'=>$target, 'options'=>$options ];
    return $this;
  }

  public function modal($id, $html, array $options = [ 'init'=>1 ]){

    $this->data[] = [ '_type'=>'modal', 'html'=>$html, 'id'=>$id, 'options'=>$options ];
    return $this;
  }

  public function open($url){

    $this->data[] = [ '_type'=>'open', 'target'=>$url ];
    return $this;
  }

  public function redirect($url, $options = []){

    $this->data[] = [ '_type'=>'redirect', 'target'=>$url, 'options'=>$options ];
    return $this;
  }



  public function debug(){

    $this->data[] = [
      '_type'=>'debug',
      'data'=>func_get_args()
    ];

    return $this;
  }

  public function openModal($id, $params = [], $html = ''){

    $this->data[] = [ '_type'=>'open-modal', 'target'=>$id, 'html'=>$html, 'params'=>$params ];
    return $this;
  }

  public function closeModal($url){

    $this->data[] = [ '_type'=>'close-modal', 'target'=>$url ];
    return $this;
  }

  public function openPopup($id, $params = [], $html = ''){

    $this->data[] = [ '_type'=>'open-popup', 'target'=>$id, 'params'=>$params, 'html'=>$html ];
    return $this;
  }




  public function merge(array $data){

    $this->data = array_merge($this->data, $data);

    return $this;
  }


  public function getData(){

    return $this->data;
  }

  public function toResponse($request)
  {
    $this->data[] = [ '_type'=>'script', 'script'=>"ui(\"meta[name='csrf-token']\").attr('content', '" . csrf_token() . "')" ];

    return response()->json($this->data, $this->status, $this->headers);
  }
}
