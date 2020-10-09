<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Imports\GenericImport;
use App\Exceptions\AppException;
use App\Exceptions\BusinessLogicException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CMSImportController extends BaseController{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $columns = [];

  protected $path = '';

  protected $view = 'andiwijaya::cms-import';
  protected $view_column_section = 'andiwijaya::sections.cms-import-columns';
  protected $view_completed_section = 'andiwijaya::sections.cms-import-completed';

  protected $title = 'Import';
  protected $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In egestas at sem vel vehicula.';

  protected $column_section_title = 'Column';
  protected $column_section_description = 'Please complete column mapping below';

  protected $completed_section_title = 'Import Completed';
  protected $completed_section_description = '';

  protected $disk = 'imports';
  protected $sub_dir = '';

  protected $results = [];

  public function index(Request $request){

    $method = action2method($request->input('action', 'view'));
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function store(Request $request){

    $method = action2method($request->input('action', 'save'));
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }



  public function view(Request $request){

    if($request->ajax()){
      return view_modal($this->view, [
        'id'=>md5($this->path),
        'width'=>600,
        'height'=>600,
        'data'=>[
          'path'=>$this->path,
          'title'=>$this->title,
          'description'=>$this->description
        ],
      ]);
    }
    abort(404);
  }


  public function analyse(Request $request){

    try{

      if(!$request->hasFile('file')) exc(__('text.file-not-found'));

      if(is_zip($request->file('file')->getMimeType())) {

        $filename = md5_file($request->file('file')->getRealPath());
        $za = new \ZipArchive();
        $za->open($request->file('file')->getRealPath());
        $za->extractTo(Storage::disk($this->disk)->path($this->sub_dir) . '/' . $filename);

        $dir_path = Storage::disk($this->disk)->path($this->sub_dir) . '/' . $filename;
        $files = array_merge(
          rglob("{$dir_path}/*.xlsx"),
          rglob("{$dir_path}/*.xls"),
          rglob("{$dir_path}/*.csv")
        );

        $csv_path = $files[0];
      }
      else{

        if(!in_array($request->file('file')->getClientOriginalExtension(), [ 'csv', 'xls', 'xlsx' ]))
          throw new AppException(__('text.import-csv-invalid-ext'));

        $filename = md5_file($request->file('file')->getRealPath()) . '.' . $request->file('file')->getClientOriginalExtension();

        Storage::putFileAs($this->disk, $request->file('file'), ($this->sub_dir ? $this->sub_dir . '/' : '') . $filename);

        $csv_path = Storage::disk($this->disk)->path($this->sub_dir) . '/' . $filename;
      }

      if($request->file('file')->getClientOriginalExtension() == 'csv'){

        $sheets = array_map('str_getcsv', file($csv_path));
        $rows = [ $sheets ];
      }
      else{
        $rows = Excel::toArray(new GenericImport, $csv_path);
      }

      $csv_columns = array_filter($rows[0][0] ?? []);

      return [
        '#' . md5($this->path)=>view($this->view_column_section, [
          'title'=>$this->column_section_title,
          'description'=>$this->column_section_description,
          'path'=>$this->path,
          'columns'=>$this->columns,
          'csv_columns'=>$csv_columns,
          'filename'=>$filename
        ])->render(),
        'script'=>"$('#" . md5($this->path) . "').modal_resize()"
      ];
    }
    catch(AppException $ex){

      exc($ex->getMessage());
    }
    catch(\Exception $ex){

      exc($ex);
      exc(__('text.general-error'));
    }
  }

  public function run(Request $request){

    $t1 = microtime(1);

    try{

      $filename = $request->input('_filename');

      $path = Storage::disk($this->disk)->path('') . ($this->sub_dir ? $this->sub_dir . '/' : '') . $filename;

      if(is_dir($path)){

        $files = array_merge(
          rglob("{$path}/*.xlsx"),
          rglob("{$path}/*.xls"),
          rglob("{$path}/*.csv")
        );

        $rows = [];
        foreach($files as $csv_path){
          if(strpos($csv_path, '.csv') !== false)
            $rows = array_merge($rows, csv_to_array($csv_path));
          else
            $rows = array_merge($rows, Excel::toArray(new GenericImport, $csv_path));
        }

        $files = rglob("{$path}/*.*");
        $_files = [];
        foreach($files as $file){
          if(in_array(explode('.', basename($file))[1] ?? '', [ 'xlsx', 'xls', 'csv' ])) continue;

          $_files[basename($file)] = $file;
        }

        $request->merge([ '_is_dir'=>1, '_dir'=>$path, '_files'=>$_files ]);
      }
      else{

        $csv_path = $path;
        if(strpos($csv_path, '.csv') !== false)
          $rows = csv_to_array($csv_path);
        else
          $rows = Excel::toArray(new GenericImport, $csv_path);
      }

      // Convert mapped column text to index
      foreach($this->columns as $key=>$column){

        if(!$request->has($key) || $request->get($key) == null) continue;

        $this->columns[$key]['mapped_to'] = $request->get($key);
      }

      // Generate data
      $data = [];
      if(isset($rows[0][0])){
        foreach($rows as $tabidx=>$tab){

          $headers = $tab[0] ?? [];
          foreach($this->columns as $key=>$column){

            unset($this->columns[$key]['csv_index']);

            if(isset($column['mapped_to'])){

              foreach($headers as $headeridx=>$text){
                if(trim($text) == $column['mapped_to']){
                  $this->columns[$key]['csv_index'] = $headeridx;
                  break;
                }
              }

              if(!($column['optional'] ?? false) && !isset($this->columns[$key]['csv_index']))
                exc(__('text.import-required-column-not-found', [ 'text'=>$column['text'] ?? '' ]));
            }
          }

          for($i = 1 ; $i < count($tab) ; $i++){

            $row = $tab[$i];

            if(!trim(implode('', $row))) continue;

            $obj = [];
            foreach($this->columns as $key=>$column){

              $optional = $column['optional'] ?? 0;

              if(!isset($column['csv_index'])){
                if(!$optional) throw new AppException("Kolom " . ($column['text'] ?? '') . " harus diisi");
                else continue;
              }

              $value = $row[$column['csv_index']] ?? '';

              // Cast
              switch($column['cast'] ?? ''){
                case 'datetime':
                  $value = $this->castDatetime($value);
                  break;
                case 'date':
                  $value = $this->castDate($value);
                  break;
              }

              $obj[$key] = $value;
            }

            $data[] = $obj;
          }
        }
      }

      $this->process($data, $request);

      return [
        '#' . md5($this->path)=>view($this->view_completed_section, [
          'title'=>$this->completed_section_title,
          'description'=>$this->completed_section_description,
          'path'=>$this->path,
          'results'=>$this->results,
          'ellapsed'=>round(microtime(1) - $t1, 3)
        ])->render(),
        'script'=>"$('#" . md5($this->path) . "').modal_resize()"
      ];
    }
    catch(AppException $ex){

      exc($ex->getMessage());
    }
    catch(\Exception $ex){

      exc($ex);
      exc(__('text.general-error'));
    }

  }

  public function process(array $data, Request $request){}

  public function back(Request $request){

    switch($request->get('step')){

      case 2:
        return [
          '#' . md5($this->path)=>view($this->view, [
            'path'=>$this->path,
            'title'=>$this->title,
            'description'=>$this->description
          ])->render(),
          'script'=>"$('#" . md5($this->path) . "').modal_resize()"
        ];

    }
  }


  protected function insertBatch($query, $obj){

    $queries = $params = [];
    foreach($obj as $arr){

      $queries[] = $arr[0];
      foreach($arr[1] as $value)
        $params[] = $value;

      if(count($params) > 60000) {

        $currentQuery = str_replace('{QUERIES}', implode(',', $queries), $query);
        DB::statement($currentQuery, $params);

        $queries = $params = [];
      }
    }

    if(count($queries) > 0){
      $currentQuery = str_replace('{QUERIES}', implode(',', $queries), $query);
      DB::statement($currentQuery, $params);
    }
  }


  private function castDate($value){

    try{

      $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
    }
    catch(\Exception $ex){

      if(date('Y', strtotime($value)) != 1970)
        $value = date('Y-m-d', strtotime($value));
      else
        $value = '';
    }

    return $value;
  }

  private function castDatetime($value){

    try{

      $value = Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
    }
    catch(\Exception $ex){

      if(date('Y', strtotime($value)) != 1970)
        $value = date('Y-m-d H:i:s', strtotime($value));
      else
        $value = '';
    }

    return $value;
  }

}