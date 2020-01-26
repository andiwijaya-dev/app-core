<?php

namespace Andiwijaya\AppCore\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class GenericImport{

  public static function import($path, array $headers, $readerType = null){

    $rows = Excel::toArray(new GenericImport, $path, null, $readerType);

    if(!isset($rows[0]) || !is_array($rows[0]))
      throw new \Exception(trans('validation.unexpected-error'));

    $rows = $rows[0];

    // Looking for headers
    $header_idx = -1;
    foreach($rows as $row_idx=>$row){

      foreach($row as $col_idx=>$col){
        foreach($headers as $header_key=>$header_cols){
          if(in_array(strtoupper(trim($col)), $header_cols['headers'])){
            $header_idx = $row_idx;
            break;
          }
        }
      }

      if($header_idx >= 0){
        foreach($headers as $header_key=>$header_cols) {
          foreach($row as $col_idx=>$col){
            if(in_array(strtoupper(trim($col)), $header_cols['headers'])){
              $headers[$header_key]['index'] = $col_idx;
            }
          }
        }
      }

    }

    // Validate required columns
    $column_not_founds = [];
    foreach($headers as $header){
      if(!isset($header['index']))
        $column_not_founds[] = "Kolom " . $header['headers'][0] . " tidak ditemukan.";
    }
    if(count($column_not_founds))
      throw new \Exception(implode("\n", $column_not_founds));

    $content_start_idx = $header_idx + 1;

    $data = [];
    for($i = $content_start_idx ; $i < count($rows) ; $i++){
      $row = $rows[$i];

      $is_empty = true;
      foreach($row as $col)
        if($col){
          $is_empty = false;
          break;
        }
      if($is_empty) continue;

      $obj = [];
      foreach($headers as $header_key=>$header_cols){
        $obj[$header_key] = $row[$headers[$header_key]['index']];
      }
      $data[] = $obj;

    }

    return $data;

  }

}