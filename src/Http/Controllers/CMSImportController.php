<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Imports\GenericImport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CMSImportController extends BaseController{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $default_columns = [
    [ 'name'=>'is_active', 'text'=>'Aktif', 'default_mapping'=>[], 'value'=>1 ]
  ];

  protected $path = 'path/to/url';

  protected $view = 'andiwijaya::cms-import';

  protected $samples = [];

  //protected function preProcessImport($session_data){}

  //protected function processImport($obj, &$error, $index = null, $session_data = null){}

  //protected function processAllImport(array $arr, &$errors, &$warnings, $session_data = null){}

  protected $percentage = 0;

  private $last_progress_sent_at = null;


  public function index(Request $request){

    $params = [
      'columns'=>$this->default_columns,
      'path'=>$this->path,
      'channel'=>$this->getChannel(),
      'samples'=>$this->samples,
    ];

    $sections = view($this->view, $params)->renderSections();

    return [
      '_'=>$sections['detail'],
      'script'=>"$('#import-modal').modal_open()"
    ];

  }

  public function store(Request $request){

    $step = $request->get('step', 1);
    $action = isset(($actions = explode('|', $request->get('action')))[0]) ? $actions[0] : '';

    switch($action){

      case 'next':
        if($step == 1) return $this->analyse($request);
        if($step == 2) return $this->process($request);
        if($step == 3) return [
          'script'=>implode(';', [
            "$('#import-modal').close()",
            "if(typeof cmslist_reload == 'function') cmslist_reload()"
          ])
        ];

      case 'back':
        if($step == 2) return $this->upload($request);
        if($step == 3) return $this->analyse($request);

    }

  }



  public function upload(Request $request){

    $params = $this->getParams($request);
    $sections = view($this->view, $params)->renderSections();

    return [
      '.import-modal-col'=>$sections['detail-1'],
      'script'=>implode(';', [
        "$('.legend>*:nth-child(2) .circle', '#import-modal').removeClass('active')",
        "$('.legend>*:nth-child(3) .circle', '#import-modal').removeClass('active')",
        "$('button[value=back]', '#import-modal').addClass('hidden')",
        "$('#import-modal .progressbar').reset()"
      ])
    ];

  }

  public function analyse(Request $request){

    if($request->has('file')){

      if (is_zip($request->file('file')->getClientMimeType())){

        return $this->analyseZip($request);

      }
      else{

        return $this->analyseExcel($request);

      }

    }
    else{

      return $this->analyseExcel($request);

    }

  }

  public function analyseZip(Request $request){

    $za = new \ZipArchive();
    $za->open($request->file('file')->getRealPath());

    $id = Session::getId();
    Storage::disk('local')->deleteDirectory($id);
    Storage::disk('local')->makeDirectory($id);
    $dir_name = Storage::disk('local')->path($id);
    $za->extractTo($dir_name);

    $files = array_merge(
      rglob("{$dir_name}/*.xlsx"),
      rglob("{$dir_name}/*.xls"),
      rglob("{$dir_name}/*.csv")
    );

    if(count($files) > 0){

      $file = $files[0];
      //$readerType = ucwords(mime2ext(mime_content_type($file)));

      $path_info = pathinfo($file);
      $readerType = ucwords($path_info['extension']);

      if(!in_array($readerType, [ 'Csv', 'Xlsx', 'Xls' ]))
        exc('Unsupported file format, please provide csv, xls or xlsx.');

      $rows = Excel::toArray(new GenericImport, $file,null, $readerType);

      $defined_column_names = [];
      foreach($this->default_columns as $column){
        if(isset($column['text'])) $defined_column_names[] = $column['text'];
        if(isset($column['default_mapping']) && is_array($column['default_mapping']))
          $defined_column_names = array_merge($defined_column_names, $column['default_mapping']);
      }
      foreach($defined_column_names as $idx=>$column)
        $defined_column_names[$idx] = strtoupper($defined_column_names[$idx]);

      if(isset($rows[0]) && is_array($rows[0])){

        $header_row_index = -1;
        foreach($rows[0] as $index=>$row){
          foreach($row as $column){
            if(in_array(strtoupper($column), $defined_column_names)){
              $header_row_index = $index;
              break;
            }
          }
          if($header_row_index != -1) break;
        }

        if($header_row_index === -1) exc('Header row not found.');

        foreach($this->default_columns as $idx=>$column){

          $texts = [];
          if(isset($column['text'])) $texts[] = $column['text'];
          if(isset($column['default_mapping']) && is_array($column['default_mapping']))
            $texts = array_merge($texts, $column['default_mapping']);

          $column_index = -1;
          foreach($rows[0][$header_row_index] as $idx2=>$column_text){
            if(in_array($column_text, $texts)){
              $column_index = $idx2;
              break;
            }
          }
          $this->default_columns[$idx]['index'] = $column_index;

        }

      }

      Session::put(str_replace('/', '.',  $this->path), [
        'columns'=>$this->default_columns,
        'file_path'=>$id . str_replace($dir_name, '', $file),
        'zip_dir'=>Storage::disk('local')->path($id),
        'header_row_index'=>$header_row_index,
        'headers'=>$rows[0][$header_row_index],
        'reader_type'=>$readerType
      ]);

      if(redis_available())
        Redis::publish($this->getChannel(), json_encode([ 'script'=>"$('#import-modal .progressbar').reset()" ]));

      $session_data = Session::get(str_replace('/', '.',  $this->path));

      $params = $this->getParams($request);
      $params['columns'] = $session_data['columns'];
      $params['headers'] = $session_data['headers'];
      $sections = view($this->view, $params)->renderSections();

      return [
        '.import-modal-col'=>$sections['detail-2'],
        'script'=>implode(';', [
          "$('.legend>*:nth-child(2) .circle', '#import-modal').addClass('active')",
          "$('.legend>*:nth-child(3) .circle', '#import-modal').removeClass('active')",
          "$('button[value=back]', '#import-modal').removeClass('hidden')"
        ])
      ];

    }

  }

  public function analyseExcel(Request $request){

    if($request->has('file')){

      ini_set('memory_limit', '1G');

      $readerType = ucwords($request->file('file')->getClientOriginalExtension());

      try{
        $rows = Excel::toArray(new GenericImport, $request->file('file')->getRealPath(), null, $readerType);
      }
      catch(\Exception $ex){
        if(strpos($ex->getMessage(), 'No reader found for') !== false)
          exc('Unable to process the file, please supply valid csv, xls, xlsx or zip file');
        throw $ex;
      }

      $defined_column_names = [];
      foreach($this->default_columns as $column){
        if(isset($column['text'])) $defined_column_names[] = $column['text'];
        if(isset($column['default_mapping']) && is_array($column['default_mapping']))
          $defined_column_names = array_merge($defined_column_names, $column['default_mapping']);
      }

      if(isset($rows[0]) && is_array($rows[0])){

        $header_row_index = -1;
        foreach($rows[0] as $index=>$row){
          foreach($row as $column){
            if(in_array($column, $defined_column_names)){
              $header_row_index = $index;
              break;
            }
          }
          if($header_row_index != -1) break;
        }

        if($header_row_index < 0) $header_row_index = 0;

        foreach($this->default_columns as $idx=>$column){

          $texts = [];
          if(isset($column['text'])) $texts[] = $column['text'];
          if(isset($column['default_mapping']) && is_array($column['default_mapping']))
            $texts = array_merge($texts, $column['default_mapping']);

          foreach($texts as $idx2=>$text)
            $texts[$idx2] = strtolower($text);

          $column_index = -1;
          foreach($rows[0][$header_row_index] as $idx2=>$column_text){
            if(in_array(strtolower($column_text), $texts)){
              $column_index = $idx2;
              break;
            }
          }
          $this->default_columns[$idx]['index'] = $column_index;

        }

      }

      Session::put(str_replace('/', '.',  $this->path), [
        'columns'=>$this->default_columns,
        'file_path'=>$request->file('file')->storeAs('', md5($request->file('file')->getClientOriginalName()), 'local'),
        'header_row_index'=>$header_row_index,
        'headers'=>$rows[0][$header_row_index],
        'reader_type'=>$readerType
      ]);

    }

    if(redis_available())
      Redis::publish($this->getChannel(), json_encode([ 'script'=>"$('#import-modal .progressbar').reset()" ]));

    $session_data = Session::get(str_replace('/', '.',  $this->path));

    $params = $this->getParams($request);
    $params['columns'] = $session_data['columns'];
    $params['headers'] = $session_data['headers'];
    $sections = view($this->view, $params)->renderSections();

    return [
      '.import-modal-col'=>$sections['detail-2'],
      'script'=>implode(';', [
        "$('.legend>*:nth-child(2) .circle', '#import-modal').addClass('active')",
        "$('.legend>*:nth-child(3) .circle', '#import-modal').removeClass('active')",
        "$('button[value=back]', '#import-modal').removeClass('hidden')"
      ])
    ];

  }

  public function process(Request $request){

    $this->last_progress_sent_at = $t1 = microtime(1);

    ini_set('memory_limit', '1G');
    ini_set('max_execution_time', 1200);
    ini_set('set_time_limit', 1200);
    set_time_limit(1200);

    $this->percentage = 20;

    $session_data = Session::get(str_replace('/', '.',  $this->path));
    $session_data = array_merge($request->all(), $session_data);

    $custom_columns = $request->get('columns');
    foreach($custom_columns as $custom_column_name=>$custom_column_index){
      foreach($session_data['columns'] as $idx=>$column){
        if($custom_column_name == $column['name'] && $custom_column_index >= 0){
          $session_data['columns'][$idx]['index'] = $custom_column_index;
        }
      }
    }

    if(isset($session_data['zip_dir'])){

      $zip_files = [];

      $files = rglob($session_data['zip_dir'] . '/*');
      foreach($files as $file)
        $zip_files[basename($file)] = $file;

      $session_data['zip_files'] = $zip_files;

    }

    $reader_type = $session_data['reader_type'];
    $header_row_index = $session_data['header_row_index'];
    $rows = Excel::toArray(new GenericImport, Storage::disk('local')->path($session_data['file_path']),null, $reader_type);
    $headers = $rows[0][$header_row_index];

    if(redis_available() && microtime(1) - $this->last_progress_sent_at > 1){
      $this->percentage += 20;
      Redis::publish($this->getChannel(), json_encode([  'script'=>"$('#import-modal .progressbar').val(" . ($this->percentage) . ")" ]));
      $this->last_progress_sent_at = microtime(1);
    }

    $arr = [];
    for($i = $header_row_index + 1 ; $i < count($rows[0]) ; $i++){

      $is_empty = true;
      foreach($rows[0][$i] as $col)
        if($col){
          $is_empty = false;
          break;
        }

      $obj = [];
      if($is_empty)
        $obj = null;
      else{

        foreach($headers as $idx=>$header){
          if(empty(trim($header))) continue;
          $obj[$header] = isset($rows[0][$i][$idx]) ? $rows[0][$i][$idx] : '';
        }

        foreach($session_data['columns'] as $column){

          if(!is_null($column['index']) && $column['index'] >= 0 && $column['index'] < count($rows[0][$i]))
            $obj[$column['name']] = $rows[0][$i][$column['index']];
          else if(isset($column['value']))
            $obj[$column['name']] = $column['value'];
          else if(!isset($column['optional']) || !$column['optional'])
            exc('Column ' . $column['text'] . ' is required');

        }

      }

      $arr[] = $obj;
    }

    $errors = [];
    $warnings = [];
    $total = 0;

    if(method_exists($this, 'processAllImport')) {

      try{

        DB::beginTransaction();

        if(method_exists($this, 'preProcessImport'))
          $this->preProcessImport($session_data);

        $total = $this->processAllImport($arr, $errors, $warnings, $session_data);

        if(count($errors) > 0)
          throw new \Exception();

        DB::commit();

      }
      catch(\Exception $ex){

        DB::rollBack();

        if($ex->getMessage() && isset($index))
          $errors[] = [ 'row'=>$index + $header_row_index + 2, 'message'=>$ex->getMessage() . (env('APP_DEBUG') ? $ex->getFile() . ':' . $ex->getLine() : '') ];
        else
          $errors[] = [ 'row'=>'-', 'message'=>$ex->getMessage() . (env('APP_DEBUG') ? $ex->getFile() . ':' . $ex->getLine() : '') ];

      }

      if(method_exists($this, 'postProcessImport'))
        $this->postProcessImport($session_data);

    }

    else {

      try{

        DB::beginTransaction();

        foreach($arr as $index=>$obj){

          if(!$obj) continue;

          $error = '';

          $this->processImport($obj, $error, $index + $header_row_index + 2, $session_data);

          if($error){
            if(isset($error['type'])){
              switch($error['type']){

                case 1:
                  $errors[] = [ 'row'=>$index + $header_row_index + 2, 'message'=>isset($error['message']) ? $error['message'] : 'Error not specified' ];
                  break;

                case 2:
                  $warnings[] = [ 'row'=>$index + $header_row_index + 2, 'message'=>$error['message'] ];
                  break;

              }
            }
            else
              $errors[] = [ 'row'=>$index + $header_row_index + 2, 'message'=>$error ];
          }

          $total++;

          $this->addProgress(80 / count($arr));

        }

        if(method_exists($this, 'postProcessImport'))
          $this->postProcessImport($session_data);

        if(count($errors) > 0)
          throw new \Exception();

        DB::commit();

      }
      catch(\Exception $ex){

        DB::rollBack();

        if($ex->getMessage() && isset($index))
          $errors[] = [ 'row'=>$index + $header_row_index + 2, 'message'=>$ex->getMessage() . (env('APP_DEBUG') ? $ex->getFile() . ':' . $ex->getLine() : '') ];
        else
          $errors[] = [ 'row'=>'-', 'message'=>$ex->getMessage() . (env('APP_DEBUG') ? $ex->getFile() . ':' . $ex->getLine() : '') ];

      }

    }



    if(redis_available())
      Redis::publish($this->getChannel(), json_encode([  'script'=>"$('#import-modal .progressbar').val(100)" ]));

    Session::put(str_replace('/', '.',  $this->path), $session_data);

    $params = $this->getParams($request);
    $params['errors'] = $errors;
    $params['warnings'] = $warnings;
    $params['total'] = $total;
    $params['ellapsed'] = round(microtime(1) - $t1, 2);
    $sections = view($this->view, $params)->renderSections();

    return [
      '.import-modal-col'=>$sections['detail-3'],
      'script'=>implode(';', [
        "$('.legend>*:nth-child(3) .circle', '#import-modal').addClass('active')",
        count($errors) > 0 ?
          "$('button[value=back]', '#import-modal').removeClass('hidden')" :
          implode(';', [
            "$('button[value=back]', '#import-modal').addClass('hidden')",
            "$('button[value=next]>label', '#import-modal').html('Selesai')",
          ]),
      ])
    ];

  }

  protected function sendProgressbar($percentage){

    if(microtime(1) - $this->last_progress_sent_at > 1 && redis_available()){

      $max_percentage = 100 - $this->percentage;
      $real_percentage = $this->percentage + ($percentage / 100 * $max_percentage);

      Redis::publish($this->getChannel(), json_encode(['script' => "$('#import-modal .progressbar').val({$real_percentage})"]));

      $this->last_progress_sent_at = microtime(1);

    }

  }

  protected function addProgress($percentage){

    $this->percentage += $percentage;

    if($this->percentage > 100) $this->percentage = 100;

    if(microtime(1) - $this->last_progress_sent_at > 1 && redis_available()){

      Redis::publish($this->getChannel(), json_encode(['script' => "$('#import-modal .progressbar').val({$this->percentage})"]));

      $this->last_progress_sent_at = microtime(1);

    }

  }

  public function getParams(Request $request, array $params = [])
  {
    return [
      'columns'=>$this->default_columns,
      'path'=>$this->path,
      'channel'=>$this->getChannel()
    ];
  }

  protected function getChannel(){

    return Str::slug($this->path) . Session::get('user_id');

  }

}