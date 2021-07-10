<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TablePage1Controller extends ActionableController
{
  protected $extends = '';
  protected $model = null;
  protected $columns = [];
  protected $view = 'andiwijaya.table-page-1';
  protected $title = 'Untitled';
  protected $items_per_page = 20;

  public function view(Request $request)
  {
    View::share([
      'extends'=>$this->extends,
      'columns' => $this->columns,
      'title' => $this->title
    ]);

    return view_content($this->view);
  }

  public function load(Request $request)
  {
    $page = explode('|', $request->input('action'))[1] ?? 1;

    list($data, $next_page, $count) = $this->datasource($request, $page);

    return $page <= 1 ?
      htmlresponse()
        ->value('.table', $data, ['next_page' => $next_page ])
        ->html('.grid .count', $count)
      :
      htmlresponse()->append('.table', $data, ['next_page' => $next_page]);
  }

  protected function datasource(Request $request, $page)
  {
    $model = $this->getBuilder();

    if (strlen(($search = $request->input('search'))) > 0) $model->search($search);

    $offset = ($page - 1) * $this->items_per_page;

    $data = $model
      ->limit($this->items_per_page + 1)
      ->offset($offset)
      ->get();

    $count = $page == 1 ? $model->count() : -1;

    $next_page = count($data) > $this->items_per_page ? $page + 1 : -1;
    $data = $data->splice(0, $this->items_per_page);

    return [
      $data,
      $next_page,
      $count
    ];
  }

  protected function getBuilder()
  {
    return $this->model::select('*');
  }

  public function __construct()
  {
    foreach ($this->columns as $idx => $column) {

      $align = $column['align'] ?? '';
      if (!$align) {
        $datatype = $column['datatype'] ?? '';
        switch ($datatype) {
          case 'number':
            $align = 'right';
            break;
        }
      }
      if (in_array($align, ['center', 'right']))
        $this->columns[$idx]['align'] = 'align-' . $align;
    }
  }
}