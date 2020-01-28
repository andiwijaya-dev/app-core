@extends('admin.app')

@section('detail')

  @if(isset($item))

    <div id="{{ \Illuminate\Support\Str::slug($module) }}-detail" class="modal" style="width:400px">

      <div class="modal-head">
        <div class="row valign-middle">
          <div class="col-6">
            <h3>{{ isset($item['id']) ? 'Update ' . ucwords($title) : ucwords($title) . ' Baru' }}</h3>
          </div>
          <div class="col-6 align-right">
            <span class="fa fa-times selectable pad-1" onclick="$(this).closest('.modal').close()"></span>
          </div>
          <div class="col-12 vmart-2 align-center hidden">
        <span class="tabs block-sm" data-cont=".detail-tabs-cont">
          <span class="item active">Deskripsi</span><span class="item">Diskusi/Pertanyaan</span><span class="item">Ulasan</span>
        </span>
          </div>
        </div>
      </div>

      <div class="modal-body">
        <div class="detail-tabs-cont">

          <div>
            <form method="post" enctype="multipart/form-data" class="async">
              @csrf

              <input type="hidden" name="id" value="{{ isset($item->id) ? $item->id : '' }}" />

              <div class="row">

                <div class="col-4">
                  <strong class="less">Kode</strong>
                  <div class="textbox vmart-1">
                    <input type="text" name="code" value="{{ isset($item->code) ? $item->code : '' }}" />
                  </div>
                </div>

                <div class="col-8">
                  <strong class="less">Nama</strong>
                  <div class="textbox vmart-1">
                    <input type="text" name="name" value="{{ isset($item->name) ? $item->name : '' }}" />
                  </div>
                </div>

                <div class="col-12 align-right vmart-3 vpadt-3">
                  <button class="more hpad-2" name="action" value="save">
                    <label>
                      <span class="fa fa-save vmarr-1"></span>
                      Simpan
                    </label>
                  </button>
                  <button class="more hpad-2" name="action" value="save-and-close">
                    <label>
                      <span class="fa fa-save vmarr-1"></span>
                      Simpan & Tutup
                    </label>
                  </button>
                </div>

              </div>
            </form>
          </div>

          <div class="row">

          </div>

        </div>
      </div>

    </div>

  @endif

@endsection

@section('header')

  @if(isset($columns))

    <tr>
      @foreach($columns as $idx=>$column)
        @php if(!isset($column['active']) || !$column['active']) continue; @endphp

        @switch($column['name'])

          @case('_options')
          <th style="width:{{ isset($column['width']) ? $column['width'] : 50 }}px" data-column-idx="{{ $idx }}"><label></label><span class="resizer"></span></th>
          @break

          @case('images')
          <th style="width:{{ isset($column['width']) ? $column['width'] : 100 }}px" data-column-idx="{{ $idx }}"><label></label><span class="resizer"></span></th>
          @break

          @default
          <th style="width:{{ isset($column['width']) ? $column['width'] : 50 }}px" data-column-idx="{{ $idx }}">
            <label>
              {{ $column['text'] ? $column['text'] : collect(explode('_', $column['name']))->map(function($t){ return ucwords($t); })->implode(' ') }}
            </label>
            <span class="resizer"></span>
          </th>
          @break

        @endswitch
      @endforeach
      <th style="width:100%"></th>
    </tr>

  @endif

@endsection

@section('header-row')

  <button type="button" class="more" onclick="$.fetch('/{{ $module }}/create')"><label><span class="fa fa-plus"></span>&nbsp;Tambah</label></button>

@endsection

@section('items')

  @if(isset($items) && isset($columns))
    @if(count($items) > 0)
      @foreach($items as $item)
        <tr data-id="{{ $item->id }}">

          @foreach($columns as $column)
            @php if(!isset($column['active']) || !$column['active']) continue; @endphp

            @if(method_exists($controller, ($methodName = 'column' . str_replace(' ', '', ucwords(str_replace('_', ' ', $column['name']))))))

              <td>{!!  $controller->{$methodName}($item, $column) !!}</td>

            @else

              @switch($column['name'])

                @case('_options')
                <td>
                  <div class="align-center">
                    <span class="fa fa-bars selectable pad-1 less" onclick="$.fetch.apply(this, [ '/{{ $module }}/{{ $item->id }}' ])"></span>
                    <span class="fa fa-times selectable pad-1 less" onclick="$.confirm('Hapus {{ $item->code }}?', function(){ $.fetch('/{{ $module }}/{{ $item->id }}', { method:'DELETE' }) })"></span>
                  </div>
                </td>
                @break

                @case('is_active')
                <td class="align-center">
                  @if($item->{$column['name']})
                    <span class="fa fa-check-circle cl-green"></span>
                  @else
                    <span class="fa fa-minus-circle"></span>
                  @endif
                </td>
                @break

                @case('images')
                <td>
                  <div class="pad-1">
                    <span class="img unloaded rat-88" data-src="/images/{{ $item->default_image_url }}"></span>
                  </div>
                </td>
                @break

                @case('created_at')
                @case('updated_at')
                <td><label>{{ $item->{$column['name']} }}</label></td>
                @break

                @default
                  @if(isset($column['data_type']))
                    @switch($column['data_type'])

                      @case('boolean')
                      <td class="align-center">
                        @if($item->{$column['name']})
                          <span class="fa fa-check"></span>
                        @else
                          <span class="fa fa-minus"></span>
                        @endif
                      </td>
                      @break

                      @default
                      <td>
                        @if(strpos($column['name'], '_html') !== false)
                          {!! is_scalar($item->{$column['name']}) ?  $item->{$column['name']} : gettype($item->{$column['name']}) !!}
                        @else
                          <label>{!! is_scalar($item->{$column['name']}) ?  $item->{$column['name']} : gettype($item->{$column['name']}) !!}</label>
                        @endif
                      </td>
                      @break
                    @endswitch
                  @else
                    <td>
                      @if(strpos($column['name'], '_html') !== false)
                        {!! is_scalar($item->{$column['name']}) ?  $item->{$column['name']} : gettype($item->{$column['name']}) !!}
                      @else
                        <label>{!! is_scalar($item->{$column['name']}) ?  $item->{$column['name']} : gettype($item->{$column['name']}) !!}</label>
                      @endif
                    </td>
                  @endif
                @break

              @endswitch

            @endif

          @endforeach

          <td></td>
        </tr>
      @endforeach
    @endif
  @endif

@endsection

@section('paging')

  @if(isset($items) && $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
    @if($items->hasMorePages())
      <tr>
        <td colspan="10" class="align-center">
          <input type="hidden" name="page" value="{{ $items->currentPage() + 1 }}" />
          <button class="grid-load-more" name="action" value=""><label>Load More</label></button>
        </td>
      </tr>
    @endif
  @endif

@endsection

@section('content')

  @if(isset($columns))

    <div class="pad-1">

      <form class="async">
        <div class="row valign-middle">

          <div class="col-6">
            <h3 class="hpad-1">{{ ucwords($title) }}</h3>
            <button class="hidden" name="action" value="search"><label>Search</label></button>
            @yield('header-row')
          </div>

          <div class="col-6 align-right">
            <span class="textbox">
              <span class="fa fa-search icon"></span>
              <input type="text" name="search" placeholder="Cari..." value="{{ $search }}"/>
            </span>
            <button name="action" value="select-column"><label>Columns <span class="fa fa-caret-down"></span></label></button>
            <button name="action" value="open-filter"><label><span class="fa fa-filter"></span> Filters</label></button>
          </div>

          <div class="col-12">
            <div class="panel vmart-1">
              <div class="grid {{ \Illuminate\Support\Str::slug($module) }}-grid" id="{{ \Illuminate\Support\Str::slug($module) }}" data-onresize="$.fetch('/{{ $module }}?action=resize-column&value=' + arguments[0])">
                <table>
                  <thead>
                  @yield('header')
                  </thead>
                  <tbody>
                  @yield('items')
                  </tbody>
                  <tfoot>
                  @yield('paging')
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

        </div>
      </form>

    </div>

  @endif

@endsection

@section('script')

  @if(isset($channel))
  <script>
    /*if (typeof Notification == 'function' && Notification.permission !== "granted")
      Notification.requestPermission();*/

    var socket = io('{{ env('UPDATER_HOST') }}');
    socket.on('connected', function(message){

      @if(env('APP_DEBUG')) console.log([ 'connected', message ]); @endif

    });
    socket.on('notify', function(channel, message){

      @if(env('APP_DEBUG')) console.log([ 'notify', channel, message ]); @endif

      try{
        var result = eval('(' + message + ')');
        $.process_xhr_response(result);
      }
      catch(e){
        @if(env('APP_DEBUG')) console.log(e); @endif
      }

    });
    socket.emit('join', '{{ $channel }}');
  </script>
  @endif

@endsection