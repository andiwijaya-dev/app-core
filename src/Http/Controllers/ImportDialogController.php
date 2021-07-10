<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Imports\GenericImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportDialogController extends ActionableController
{
  protected $columns = [];
  protected $view;

  public function view(Request $request)
  {
    return htmlresponse()
      ->modal(
        'import-dialog',
        view('andiwijaya::sections.import-dialog-1')->render(),
        [
          'width'=>540
        ]
      );
  }

  public function analyse(Request $request)
  {
    if(!$request->hasFile('file')) exc('File belum diupload');

    $params = Session::get('import', [
      'filename'=>'',
      'files'=>[],
      'columns'=>[]
    ]);

    if(is_zip($request->file('file')->getMimeType())) {

      $zip = new \ZipArchive();
      $zip->open($request->file('file')->getRealPath());
      $dir = storage_path('app/uploads/' . md5_file($request->file('file')->getRealPath()));
      $zip->extractTo($dir);

      $files = array_merge(
        rglob("{$dir}/*.xlsx"),
        rglob("{$dir}/*.xls"),
        rglob("{$dir}/*.csv")
      );
      if(!isset($files[0])) exc("File xlsx, xls atau csv tidak ditemukan didalam file zip.");
      $params['filename'] = $files[0];

      $all_files = rglob("{$dir}/*.*");
      $files = [];
      foreach($all_files as $file){
        if(in_array(explode('.', basename($file))[1] ?? '', [ 'xlsx', 'xls', 'csv', 'zip' ])) continue;
        $files[basename($file)] = $file;
      }
      $params['files'] = $files;
    }
    else{

      if(!in_array($request->file('file')->getClientOriginalExtension(), [ 'csv', 'xls', 'xlsx' ]))
        exc('File tidak didukung, masukkan file csv, xls, xlsx atau zip');

      $filename = md5_file($request->file('file')->getRealPath()) . '.' . $request->file('file')->getClientOriginalExtension();
      $request->file('file')->storeAs('uploads', $filename);
      $params['filename'] = storage_path('app/uploads/' . $filename);
    }

    if($request->file('file')->getClientOriginalExtension() == 'csv'){
      $sheets = array_map('str_getcsv', file($params['filename']));
      $rows = [ $sheets ];
    }
    else{
      $rows = Excel::toArray(new GenericImport, $params['filename']);
    }
    $params['columns'] = array_filter($rows[0][0] ?? []);

    Session::put('import', $params);
    
    View::share([
      'columns'=>$this->columns,
      'data_columns'=>$params['columns']
    ]);

    return htmlresponse()
      ->html('#import-dialog', view('andiwijaya::sections.import-dialog-2')->render())
      ->script("ui('#import-dialog').modal_resize()");
  }

  public function import(Request $request, $data){}
  
  public function proceed(Request $request)
  {
    $params = Session::get('import', []);

    if(strpos($params['filename'], '.csv') !== false)
      $rows = csv_to_array($params['filename']);
    else
      $rows = Excel::toArray(new GenericImport, $params['filename']);
    if(!isset($rows[0][0])) exc('Unexpected data value.');

    $columnIdx = [];
    foreach($params['columns'] as $idx=>$column){
      $columnIdx[$column] = $idx;
    }

    $data = [];
    foreach($rows as $sheet){

      foreach($sheet as $idx=>$row){
        if($idx == 0) continue;

        $obj = [];

        foreach($this->columns as $column){
          $mapped_to = $request->input($column['name']);
          $mapped_to_idx = $columnIdx[$mapped_to] ?? -1;

          $optional = $column['optional'] ?? 0;
          $value = $row[$mapped_to_idx] ?? null;

          if(!$optional && !$value) exc("Data tidak ditemukan untuk kolom " . $column['name']);
          
          if($mapped_to_idx >= 0)
            $obj[$column['name']] = $value;
        }

        $data[] = $obj;
      }
    }

    $request->merge([
      'columns'=>$params['columns'],
      'filename'=>$params['filename'],
      'files'=>$params['files'] ?? [],
    ]);

    $result = $this->import($request, $data);

    $response = htmlresponse()
      ->html('#import-dialog', view('andiwijaya::sections.import-dialog-3', $result)->render())
      ->script("ui('#import-dialog').modal_resize()");

    if(isset($result['script']))
      $response->script($result['script']);

    return $response;
  }
}