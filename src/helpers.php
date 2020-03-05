<?php


/**
 * Check if variable is assosiative array
 */

if(!function_exists('is_assoc')){
  function is_assoc($array) {
    if(gettype($array) == "array")
      return (bool)count(array_filter(array_keys($array), 'is_string'));
    return false;
  }
}

if (! function_exists('exc')) {
  function exc($message)
  {
    if(is_array($message)) $message = json_encode($message);
    throw new \Exception($message);

  }
}

if(! function_exists('is_zip')){

  function is_zip($mime_type){

    return in_array($mime_type, [ 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip' ]);

  }

}

if(! function_exists('rglob')){

  function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
      $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
  }

}

if(!function_exists('paging_url_replace')){

  function paging_url_replace($url, $page){

    if(strpos($url, 'page=') !== false)
      $url = preg_replace('/page=(\d+)/', "page={$page}", $url);
    else
      $url .= strpos($url, '?') !== false ? "&page={$page}" : "?page={$page}";
    return $url;

  }

  function paging_render($items){

    $page = $items->currentPage();
    $last_page = $items->lastPage();

    if($last_page == 1) return;

    $html = [];
    $html[] = "<div class='paging'>";

    if($page > 1) $html[] = "<a class='small' href=\"" . paging_url_replace(\Illuminate\Http\Request::fullUrl(), 1) . "\">First</a>";
    if($page - 1 >= 1) $html[] = "<a class='small' href=\"" . paging_url_replace(\Illuminate\Http\Request::fullUrl(), $page - 1) . "\">Prev</a>";

    $start_index = $page - 3 < 1 ? 1 : $page - 3;
    $end_index = $page + 3 > $last_page ? $last_page : $page + 3;

    if($start_index + 6 > $last_page && $end_index - 6 < 1){
      $start_index = 1;
      $end_index = $last_page;
    }
    else if($end_index - 6 < 1){
      $start_index = 1;
      $end_index = 6;
    }
    else if($start_index + 6 > $last_page){
      $start_index = $last_page - 6;
      $end_index = $last_page;
    }

    for($i = $start_index ; $i <= $end_index ; $i++){
      $html[] = "<a class='small" . ($i == $page ? " active" : '') . "' href=\"" . paging_url_replace(\Illuminate\Support\Facades\Request::fullUrl(), $i) . "\">{$i}</a>";
    }

    if($page + 1 <= $last_page) $html[] = "<a class='small' href=\"" . paging_url_replace(\Illuminate\Support\Facades\Request::fullUrl(), $page + 1) . "\">Next</a>";
    if($page < $last_page) $html[] = "<a class='small' href=\"" . paging_url_replace(\Illuminate\Support\Facades\Request::fullUrl(), $last_page) . "\">Last</a>";

    $html[] = "</div>";
    return implode('', $html);

  }

}

if(!function_exists('random_voucher_code')){

  function random_voucher_code($count, $prefix, $digitcount, $existingvouchers = null, $numeric_only = false){

    if(!$existingvouchers) $existingvouchers = array();

    $newvouchers = array();
    do{
      $index = $numeric_only ? str_pad(rand(0, pow(10, $digitcount) - 1), $digitcount, '0', STR_PAD_LEFT) : strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $digitcount));
      $vouchercode = $prefix . $index;
      $exists = isset($existingvouchers[$vouchercode]) ? true : false;
      if(!$exists && substr($vouchercode, 0, 1) != '0'){
        $newvouchers[] = $vouchercode;
        $existingvouchers[$vouchercode] = 1;
      }
    }
    while(count($newvouchers) < $count);

    return $newvouchers;

  }

}

if(!function_exists('validYear')){

  function validYear($year){

    return $year > 1901 && $year < 2099;

  }

}

if(!function_exists('array_to_object')){

  /**
   * Convert array to object
   * - Static value supported instead of mapping to columns
   *
   * @param array $rows
   * @param array $mapping if array, map column. if scalar, map static value
   * @return array
   */
  function array_to_object(array $rows, array $mapping){

    $columns_names = [];
    foreach($mapping as $key=>$columns)
      if(is_array($columns)){
        foreach($columns as $column)
          $columns_names[$column] = 1;
      }

    $header_row = -1;

    $columns = [];
    foreach($rows as $idx=>$row){
      foreach($row as $col_idx=>$col)
        if(isset($columns_names[$col])){
          $header_row = $idx;
          break;
        }
    }
    if($header_row >= 0){
      foreach($rows[$header_row] as $col_idx=>$col){
        foreach($mapping as $key=>$map){

          if(is_array($map)){
            if(in_array($col, $map)){
              $columns[$key] = [
                'key'=>$key,
                'index'=>$col_idx
              ];
            }
          }

          elseif(is_scalar($map)){
            $columns[$key] = [
              'key'=>$key,
              'value'=>$map
            ];
          }

        }
      }
    }

    $results = [];
    for($i = $header_row + 1 ; $i < count($rows) ; $i++){

      $empty_row = true;
      foreach($rows[$i] as $col)
        if($col){
          $empty_row = false;
          break;
        }

      if($empty_row) continue;

      $obj = [];
      foreach($columns as $key=>$column){

        if(isset($column['index']))
          $obj[$key] = $rows[$i][$column['index']];

        if(isset($column['value']))
          $obj[$key] = $column['value'];

      }
      $results[] = $obj;

    }

    return $results;

  }

}

if(!function_exists('ov')){

  function ov($key, $obj){

    if(is_scalar($key))
      return isset($obj[$key]) ? $obj[$key] : '';

    elseif(is_array($key)){

      foreach($key as $key_)
        if(isset($obj[$key_]))
          return $obj[$key_];
      return '';

    }

  }

}

if(!function_exists('redis_available')){

  function redis_available(){

    try{
      $redis = \Illuminate\Support\Facades\Redis::connection();
      $redis->connect();
      $redis->disconnect();

      return true;
    }
    catch (\Exception $e){
      return false;
    }

  }

}

if(!function_exists('array_diff_assoc2')){

  /**
   * Compare 2 array of object, returns created, updated and deleted object
   * TODO: Will fail if object contains multi-dimension value
   * @param $arr1
   * @param $arr2
   * @param string $key
   * @return array
   */
  function array_diff_assoc2($arr1, $arr2, $key = 'id', $debug = false){

    if(!is_array($arr1)) $arr1 = json_decode(json_encode($arr1), 1);
    if(!is_array($arr2)) $arr2 = json_decode(json_encode($arr2), 1);

    $has_update = false;

    foreach($arr1 as $idx1=>$obj1){

      $exists = -1;
      foreach($arr2 as $idx2=>$obj2){
        if(isset($obj2[$key]) && $obj1[$key] == $obj2[$key]){
          $exists = $idx1;
          break;
        }
      }
      if($exists == -1){
        $arr1[$idx1]['_type'] = -1;
        $has_update = true;
      }
    }

    $created = [];
    foreach($arr2 as $idx2=>$obj2){

      $exists = -1;
      $updates = null;
      foreach($arr1 as $idx1=>$obj1){
        if(($id2 = $obj2[$key] ?? 'x') == ($id1 = $obj1[$key] ?? 'y')){
          $updates = array_diff2($obj1, $obj2);
          $exists = $idx1;
        }
      }

      if($exists == -1){
        $created[] = array_merge($obj2, [ '_type'=>1 ]);
        $has_update = true;
      }
      elseif(count($updates) > 0){
        $arr1[$exists]['_type'] = 2;
        $arr1[$exists]['_updates'] = $updates;
        $has_update = true;
      }

    }

    $arr1 = array_merge($arr1, $created);

    return $has_update ? $arr1 : null;


    /*$deleted = array_filter($arr1, function($obj1) use($arr2, $key){
      foreach($arr2 as $obj2)
        if(isset($obj2[$key]) && $obj1[$key] == $obj2[$key])
          return false;
      return true;
    });*/

    /*$updated = [];
    $created = array_filter($arr2, function($obj2) use($arr1, $key, &$updated){

      $exists = false;
      foreach($arr1 as $idx1=>$obj1){
        if(($id2 = $obj2[$key] ?? 'x') == ($id1 = $obj1[$key] ?? 'y')){
          if(count(($update = array_diff2($obj1, $obj2))) > 0)
            $updated[] = array_merge([ $key=>$id2 ], $update);
          $exists = true;
        }
      }
      return !$exists;

    });*/

    /*$result = [];
    if(count($deleted) > 0) $result['deleted'] = $deleted;
    if(count($created) > 0) $result['created'] = $created;
    if(count($updated) > 0) $result['updated'] = $updated;
    return $result;*/

  }

  function array_diff2($obj1, $obj2){

    $obj3 = [];
    foreach($obj1 as $key=>$value){

      if(isset($obj2[$key])){

        if(is_scalar($obj1[$key])){
          if($obj1[$key] != $obj2[$key])
            $obj3[$key] = $obj2[$key];
        }
        else if(json_encode($obj1[$key]) != json_encode($obj2[$key]))
          $obj3[$key] = $obj2[$key];

      }

    }
    return $obj3;

  }

}

if(!function_exists('array_index')){

  function array_index($arr, $indexes, $objResult = false){
    if(!is_array($arr)) return null;
    $result = array();

    for($i = 0 ; $i < count($arr) ; $i++){
      $obj = $arr[$i];

      switch(count($indexes)){
        case 1 :
          $idx0 = $indexes[0];
          if(!isset($obj[$idx0])) continue;
          if(!isset($result[$obj[$idx0]])) $result[$obj[$idx0]] = array();
          $result[$obj[$idx0]][] = $obj;
          break;
        case 2 :
          $idx0 = $indexes[0];
          $idx1 = $indexes[1];
          if(!isset($obj[$idx0]) || !isset($obj[$idx1])) continue;
          $key0 = $obj[$idx0];
          $key1 = $obj[$idx1];
          if(!isset($result[$key0])) $result[$key0] = array();
          if(!isset($result[$key0][$key1])) $result[$key0][$key1] = array();
          $result[$key0][$key1][] = $obj;
          break;
        case 3 :
          $idx0 = $indexes[0];
          $idx1 = $indexes[1];
          $idx2 = $indexes[2];
          if(!isset($obj[$idx0]) || !isset($obj[$idx1]) || !isset($obj[$idx2])) continue;
          $key0 = $obj[$idx0];
          $key1 = $obj[$idx1];
          $key2 = $obj[$idx2];
          if(!isset($result[$key0])) $result[$key0] = array();
          if(!isset($result[$key0][$key1])) $result[$key0][$key1] = array();
          $result[$key0][$key1][$key2] = $obj;
          break;
        default:
          throw new Exception("Unsupported index level.");
      }
    }

    // If array count = 1, remove array
    if($objResult){
      switch(count($indexes)){
        case 1:
          foreach($result as $key=>$arr)
            if(count($arr) == 1) $result[$key] = $arr[0];
          break;
        case 2:
          foreach($result as $key=>$arr1){
            foreach($arr1 as $key1=>$arr){
              if(count($arr) == 1) $result[$key][$key1] = $arr[0];
            }
          }
          break;
        case 3:
          foreach($result as $key=>$arr1){
            foreach($arr1 as $key1=>$arr2){
              foreach($arr2 as $key2=>$arr)
                if(count($arr) == 1) $result[$key][$key1][$key2] = $arr[0];
            }
          }
          break;
      }
    }

    return $result;
  }

  function array_index_obj($arr, $indexes, $objResult = false)
  {

    $result = [];

    for ($i = 0; $i < count($arr); $i++) {
      $obj = $arr[$i];

      switch (count($indexes)) {
        case 1 :
          $idx0 = $indexes[0];
          if (!isset($obj->{$idx0})) continue;
          if (!isset($result[$obj->{$idx0}])) $result[$obj->{$idx0}] = array();
          $result[$obj->{$idx0}][] = $obj;
          break;
        case 2 :
          $idx0 = $indexes[0];
          $idx1 = $indexes[1];
          if (!isset($obj->{$idx0}) || !isset($obj->{$idx1})) continue;
          $key0 = $obj->{$idx0};
          $key1 = $obj->{$idx1};
          if (!isset($result[$key0])) $result[$key0] = array();
          if (!isset($result[$key0][$key1])) $result[$key0][$key1] = array();
          $result[$key0][$key1][] = $obj;
          break;
        case 3 :
          $idx0 = $indexes[0];
          $idx1 = $indexes[1];
          $idx2 = $indexes[2];
          if (!isset($obj->{$idx0}) || !isset($obj->{$idx1}) || !isset($obj->{$idx2})) continue;
          $key0 = $obj->{$idx0};
          $key1 = $obj->{$idx1};
          $key2 = $obj->{$idx2};

          if (!isset($result[$key0])) $result[$key0] = array();
          if (!isset($result[$key0][$key1])) $result[$key0][$key1] = array();
          $result[$key0][$key1][$key2][] = $obj;
          break;
        default:
          throw new Exception("Unsupported index level.");
      }
    }

  }

}

if(!function_exists('mime2ext')){

  function mime2ext($mime) {
    $mime_map = [
      'video/3gpp2'                                                               => '3g2',
      'video/3gp'                                                                 => '3gp',
      'video/3gpp'                                                                => '3gp',
      'application/x-compressed'                                                  => '7zip',
      'audio/x-acc'                                                               => 'aac',
      'audio/ac3'                                                                 => 'ac3',
      'application/postscript'                                                    => 'ai',
      'audio/x-aiff'                                                              => 'aif',
      'audio/aiff'                                                                => 'aif',
      'audio/x-au'                                                                => 'au',
      'video/x-msvideo'                                                           => 'avi',
      'video/msvideo'                                                             => 'avi',
      'video/avi'                                                                 => 'avi',
      'application/x-troff-msvideo'                                               => 'avi',
      'application/macbinary'                                                     => 'bin',
      'application/mac-binary'                                                    => 'bin',
      'application/x-binary'                                                      => 'bin',
      'application/x-macbinary'                                                   => 'bin',
      'image/bmp'                                                                 => 'bmp',
      'image/x-bmp'                                                               => 'bmp',
      'image/x-bitmap'                                                            => 'bmp',
      'image/x-xbitmap'                                                           => 'bmp',
      'image/x-win-bitmap'                                                        => 'bmp',
      'image/x-windows-bmp'                                                       => 'bmp',
      'image/ms-bmp'                                                              => 'bmp',
      'image/x-ms-bmp'                                                            => 'bmp',
      'application/bmp'                                                           => 'bmp',
      'application/x-bmp'                                                         => 'bmp',
      'application/x-win-bitmap'                                                  => 'bmp',
      'application/cdr'                                                           => 'cdr',
      'application/coreldraw'                                                     => 'cdr',
      'application/x-cdr'                                                         => 'cdr',
      'application/x-coreldraw'                                                   => 'cdr',
      'image/cdr'                                                                 => 'cdr',
      'image/x-cdr'                                                               => 'cdr',
      'zz-application/zz-winassoc-cdr'                                            => 'cdr',
      'application/mac-compactpro'                                                => 'cpt',
      'application/pkix-crl'                                                      => 'crl',
      'application/pkcs-crl'                                                      => 'crl',
      'application/x-x509-ca-cert'                                                => 'crt',
      'application/pkix-cert'                                                     => 'crt',
      'text/css'                                                                  => 'css',
      'text/x-comma-separated-values'                                             => 'csv',
      'text/comma-separated-values'                                               => 'csv',
      'application/vnd.msexcel'                                                   => 'csv',
      'application/x-director'                                                    => 'dcr',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
      'application/x-dvi'                                                         => 'dvi',
      'message/rfc822'                                                            => 'eml',
      'application/x-msdownload'                                                  => 'exe',
      'video/x-f4v'                                                               => 'f4v',
      'audio/x-flac'                                                              => 'flac',
      'video/x-flv'                                                               => 'flv',
      'image/gif'                                                                 => 'gif',
      'application/gpg-keys'                                                      => 'gpg',
      'application/x-gtar'                                                        => 'gtar',
      'application/x-gzip'                                                        => 'gzip',
      'application/mac-binhex40'                                                  => 'hqx',
      'application/mac-binhex'                                                    => 'hqx',
      'application/x-binhex40'                                                    => 'hqx',
      'application/x-mac-binhex40'                                                => 'hqx',
      'text/html'                                                                 => 'html',
      'image/x-icon'                                                              => 'ico',
      'image/x-ico'                                                               => 'ico',
      'image/vnd.microsoft.icon'                                                  => 'ico',
      'text/calendar'                                                             => 'ics',
      'application/java-archive'                                                  => 'jar',
      'application/x-java-application'                                            => 'jar',
      'application/x-jar'                                                         => 'jar',
      'image/jp2'                                                                 => 'jp2',
      'video/mj2'                                                                 => 'jp2',
      'image/jpx'                                                                 => 'jp2',
      'image/jpm'                                                                 => 'jp2',
      'image/jpeg'                                                                => 'jpeg',
      'image/pjpeg'                                                               => 'jpeg',
      'application/x-javascript'                                                  => 'js',
      'application/json'                                                          => 'json',
      'text/json'                                                                 => 'json',
      'application/vnd.google-earth.kml+xml'                                      => 'kml',
      'application/vnd.google-earth.kmz'                                          => 'kmz',
      'text/x-log'                                                                => 'log',
      'audio/x-m4a'                                                               => 'm4a',
      'application/vnd.mpegurl'                                                   => 'm4u',
      'audio/midi'                                                                => 'mid',
      'application/vnd.mif'                                                       => 'mif',
      'video/quicktime'                                                           => 'mov',
      'video/x-sgi-movie'                                                         => 'movie',
      'audio/mpeg'                                                                => 'mp3',
      'audio/mpg'                                                                 => 'mp3',
      'audio/mpeg3'                                                               => 'mp3',
      'audio/mp3'                                                                 => 'mp3',
      'video/mp4'                                                                 => 'mp4',
      'video/mpeg'                                                                => 'mpeg',
      'application/oda'                                                           => 'oda',
      'audio/ogg'                                                                 => 'ogg',
      'video/ogg'                                                                 => 'ogg',
      'application/ogg'                                                           => 'ogg',
      'application/x-pkcs10'                                                      => 'p10',
      'application/pkcs10'                                                        => 'p10',
      'application/x-pkcs12'                                                      => 'p12',
      'application/x-pkcs7-signature'                                             => 'p7a',
      'application/pkcs7-mime'                                                    => 'p7c',
      'application/x-pkcs7-mime'                                                  => 'p7c',
      'application/x-pkcs7-certreqresp'                                           => 'p7r',
      'application/pkcs7-signature'                                               => 'p7s',
      'application/pdf'                                                           => 'pdf',
      'application/octet-stream'                                                  => 'pdf',
      'application/x-x509-user-cert'                                              => 'pem',
      'application/x-pem-file'                                                    => 'pem',
      'application/pgp'                                                           => 'pgp',
      'application/x-httpd-php'                                                   => 'php',
      'application/php'                                                           => 'php',
      'application/x-php'                                                         => 'php',
      'text/php'                                                                  => 'php',
      'text/x-php'                                                                => 'php',
      'application/x-httpd-php-source'                                            => 'php',
      'image/png'                                                                 => 'png',
      'image/x-png'                                                               => 'png',
      'application/powerpoint'                                                    => 'ppt',
      'application/vnd.ms-powerpoint'                                             => 'ppt',
      'application/vnd.ms-office'                                                 => 'ppt',
      'application/msword'                                                        => 'ppt',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
      'application/x-photoshop'                                                   => 'psd',
      'image/vnd.adobe.photoshop'                                                 => 'psd',
      'audio/x-realaudio'                                                         => 'ra',
      'audio/x-pn-realaudio'                                                      => 'ram',
      'application/x-rar'                                                         => 'rar',
      'application/rar'                                                           => 'rar',
      'application/x-rar-compressed'                                              => 'rar',
      'audio/x-pn-realaudio-plugin'                                               => 'rpm',
      'application/x-pkcs7'                                                       => 'rsa',
      'text/rtf'                                                                  => 'rtf',
      'text/richtext'                                                             => 'rtx',
      'video/vnd.rn-realvideo'                                                    => 'rv',
      'application/x-stuffit'                                                     => 'sit',
      'application/smil'                                                          => 'smil',
      'text/srt'                                                                  => 'srt',
      'image/svg+xml'                                                             => 'svg',
      'application/x-shockwave-flash'                                             => 'swf',
      'application/x-tar'                                                         => 'tar',
      'application/x-gzip-compressed'                                             => 'tgz',
      'image/tiff'                                                                => 'tiff',
      'text/plain'                                                                => 'txt',
      'text/x-vcard'                                                              => 'vcf',
      'application/videolan'                                                      => 'vlc',
      'text/vtt'                                                                  => 'vtt',
      'audio/x-wav'                                                               => 'wav',
      'audio/wave'                                                                => 'wav',
      'audio/wav'                                                                 => 'wav',
      'application/wbxml'                                                         => 'wbxml',
      'video/webm'                                                                => 'webm',
      'audio/x-ms-wma'                                                            => 'wma',
      'application/wmlc'                                                          => 'wmlc',
      'video/x-ms-wmv'                                                            => 'wmv',
      'video/x-ms-asf'                                                            => 'wmv',
      'application/xhtml+xml'                                                     => 'xhtml',
      'application/excel'                                                         => 'xl',
      'application/msexcel'                                                       => 'xls',
      'application/x-msexcel'                                                     => 'xls',
      'application/x-ms-excel'                                                    => 'xls',
      'application/x-excel'                                                       => 'xls',
      'application/x-dos_ms_excel'                                                => 'xls',
      'application/xls'                                                           => 'xls',
      'application/x-xls'                                                         => 'xls',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
      'application/vnd.ms-excel'                                                  => 'xlsx',
      'application/xml'                                                           => 'xml',
      'text/xml'                                                                  => 'xml',
      'text/xsl'                                                                  => 'xsl',
      'application/xspf+xml'                                                      => 'xspf',
      'application/x-compress'                                                    => 'z',
      'application/x-zip'                                                         => 'zip',
      'application/zip'                                                           => 'zip',
      'application/x-zip-compressed'                                              => 'zip',
      'application/s-compressed'                                                  => 'zip',
      'multipart/x-zip'                                                           => 'zip',
      'text/x-scriptzsh'                                                          => 'zsh',
    ];

    return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
  }

}

if(!function_exists('in_array_any')){

  function in_array_any($needles, $haystack) {
    return !empty(array_intersect($needles, $haystack));
  }

}

if(!function_exists('in_array_all')){

  function in_array_all($needles, $haystack) {
    return empty(array_diff($needles, $haystack));
  }

}

if(!function_exists('save_image')){

  function save_image($image, $disk = 'images'){

    if(!is_file($image) && !filter_var($image, FILTER_VALIDATE_URL)) exc('Invalid file');

    $file_md5 = md5_file($image);
    //list($width, $height) = getimagesize($image);

    if(!\Illuminate\Support\Facades\Storage::disk($disk)->exists($file_md5))
      \Illuminate\Support\Facades\Storage::disk($disk)->put($file_md5, file_get_contents($image));

    return $file_md5;

  }

}

if(!function_exists('save_base64_image')){

  function is_base64_image($data){

    return preg_match('/^data:image\/(\w+);base64,/', $data, $type);

  }

  function save_base64_image($data, $disk = 'images'){

    if(preg_match('/^data:image\/(\w+);base64,/', $data, $type)){

      $data = substr($data, strpos($data, ',') + 1);
      $type = strtolower($type[1]);

      // Only handle image with extension below
      if(!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ]))
        return $data;

      $data = base64_decode($data);
      if($data === false)
        return $data; // base64_decode failed

      $file_md5 = md5($data);
      //list($width, $height) = getimagesize($data);

      if(!\Illuminate\Support\Facades\Storage::disk($disk)->exists($file_md5))
        \Illuminate\Support\Facades\Storage::disk($disk)->put($file_md5, $data);

      return $file_md5;

    }

    return '';

  }

}

if( !function_exists('ceiling') )
{
  function ceiling($number, $significance = 1)
  {
    return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
  }
}

if(!function_exists('rand_image')){

  function rand_image($count = 1){

    $image_urls = [
      'https://files.vlad.studio/sequoia/joy/sketchy_unfocused/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/i_feel_good/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/man_with_very_long_hand/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/bookworm/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/perfect_boobs/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/sleeping_whales/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/axolotl/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/hug/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/bird_bird_bird_bird_bird/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/still/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/blank_canvas/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/xmas_windows/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/early_morning/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/coffee_station/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/lower_antelope_2/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/tinyliving/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/starry_night/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/bat_and_her_pet/thumbs/1024x1024.jpg',
      'https://files.vlad.studio/sequoia/joy/cats/thumbs/1024x1024.jpg'
    ];

    if($count == 1) return $image_urls[rand(0, count($image_urls) - 1)];
    return array_splice($image_urls, 0, $count);

  }

}

if(!function_exists('random_dark_color')){

  function random_dark_color() {
    $color = '';
    for($i = 0 ; $i < 3 ; $i++)
      $color .= str_pad( dechex( mt_rand( 0, 127 ) ), 2, '0', STR_PAD_LEFT);
    return $color;
  }

}