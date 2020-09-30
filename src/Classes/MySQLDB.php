<?php

namespace Andiwijaya\AppCore\Classes;

class MySQLDB{

  private static $conn;

  public static function fetch($params, $indexes = null){

    self::$conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));

    if(is_string($params))
      $query = mysqli_query(self::$conn, $params);

    $arr = $query->fetch_all(MYSQLI_ASSOC);

    if($indexes != null)
      $arr = array_index($arr, $indexes);

    return $arr;
  }

}