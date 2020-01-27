<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\Log;

class CMSLogController extends CMSListController
{
  protected $default_columns = [
    [ 'name'=>'_options', 'text'=>'Pilihan', 'active'=>1, 'width'=>100 ],
    [ 'name'=>'timestamp', 'text'=>'Waktu', 'active'=>1, 'width'=>150 ],
    [ 'name'=>'type', 'text'=>'Tipe', 'active'=>1, 'width'=>100 ],
    [ 'name'=>'data', 'text'=>'Data', 'active'=>1, 'width'=>150 ],
    [ 'name'=>'user_agent', 'text'=>'Browser', 'active'=>1, 'width'=>150 ],
    [ 'name'=>'remote_ip', 'text'=>'IP Address', 'active'=>1, 'width'=>150 ],
  ];

  protected $title = 'Log';

  protected $model = Log::class;

  protected $module = 'log';

  protected $list_view = 'andiwijaya::cms-log';

}