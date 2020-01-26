<?php

namespace Andiwijaya\AppCore\Models\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

trait CMSListUpdateTrait{

  public function cmsListUpdate(){

    if(redis_available()){

      $keys = Redis::keys(implode('-', [ 'cms_list', (new \ReflectionClass($this))->getShortName(), '*' ]));

      foreach($keys as $key){

        $params = json_decode(Redis::get($key), 1);
        $module = $params['module'];
        $section = $params['section'];
        $view = $params['view'];

        if(View::exists($view)){
          $sections = view($view, array_merge($params, [ 'items'=>[ $this ]]))->renderSections();
          $html = $sections[$section] ?? '';

          $grid = '.' . Str::slug($module) . '-grid';

          if($this->wasRecentlyCreated){
            $result = [
              '.' . Str::slug($module) . '-grid tbody'=>'<<' . $html,
              'script'=>"$('{$grid}').grid_update()"
            ];
          }
          else{
            $result = [
              '.' . Str::slug($module) . '-grid tr[data-id=' . $this->id . ']'=>substr($html, strpos($html, '<td'), strrpos($html, '</tr>') - strpos($html, '<td')),
              'script'=>"$('{$grid}').grid_update()"
            ];
          }

          Redis::publish($key, json_encode($result));
        }

      }

    }

  }

  public function cmsListDelete(){

    if(redis_available()){

      $keys = Redis::keys(implode('-', [ 'cms_list', (new \ReflectionClass($this))->getShortName(), '*' ]));

      foreach($keys as $key){

        $params = json_decode(Redis::get($key), 1);
        $module = $params['module'];
        $grid = '.' . Str::slug($module) . '-grid';

        Redis::publish($key, json_encode([
          'script'=>implode(';', [
            "$('{$grid} tr[data-id={$this->id}]').remove()",
            "$('{$grid}').grid_update()"
          ])
        ]));

      }

    }

  }

}