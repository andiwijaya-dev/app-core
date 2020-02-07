@extends('admin.app')

@section('step-1-text')

  <h4>Masukkan file untuk import (csv, xls atau xlsx)</h4>
  <div class="fileupload vmart-1">
    <button class="hpad-2">
      <label>Pilih File</label>
      <span class="icon-remove fa fa-times"></span>
    </button>
    <input class="hidden" type="file" name="file" />
  </div>

  @if(isset($samples) && is_array($samples) && count($samples) > 0)
    <br /><br /><br />
    <strong>Sample</strong>
    <br />
    @foreach($samples as $sample)
      <a class="more hmarr-2 vpad-1 inline-block" download href="{{ $sample }}">{{ basename($sample) }}</a>
    @endforeach
  @endif

@endsection

@section('detail-1')

  @yield('step-1-text')

  <input type="hidden" name="step" value="1" />
  <div class="detail-tooltip"></div>

@endsection

@section('detail-2')

  <input type="hidden" name="file" value="" />
  <input type="hidden" name="step" value="2" />

  <div class="detail-tooltip"></div>

  <div class="row2">

    @foreach($columns as $column)
      <div class="col-lg-4">
        <label class="less">{{ $column['text'] ?? 'No Text' }}{{ isset($column['optional']) && $column['optional'] ? '(optional)' : '' }}</label>
        <div class="dropdown vmart-1"{{ isset($column['optional']) && $column['optional'] ? '' : 'data-validation=required' }}>
          <select name="columns[{{ $column['name'] ?? '' }}]">
            @if(isset($column['optional']) && $column['optional'])
              <option value="" selected>Tidak dipakai</option>
            @else
              <option value="" disabled selected>Pilih</option>
            @endif
            @if(isset($headers))
              @foreach($headers as $index=>$header)
                @if($header)
                  <option value="{{ $index }}"{{ !is_null($column['index']) && $column['index'] == $index ? ' selected' : '' }}>{{ $header }}</option>
                @endif
              @endforeach
            @endif
          </select>
          <span class="icon fa fa-caret-down"></span>
        </div>
      </div>
    @endforeach

  </div>

  @yield('detail-2-ext')

@endsection

@section('detail-3')

  <input type="hidden" name="step" value="3" />

  @if(isset($errors) && isset($total))
    @if(count($errors) > 0)
      <h5>Import Gagal</h5>
      <br /><br />

      <table class="table">
        <tr>
          <th style="width:100px" class="align-left">Baris</th>
          <th class="align-left">Error</th>
        </tr>
        @foreach($errors as $error)
          <tr>
            <td>{{ $error['row'] }}</td>
            <td>{{ $error['message'] }}</td>
          </tr>
        @endforeach
      </table>
    @else
      <h5>Import berhasil</h5>
      <br /><br />
      <p>{{ $total }} data berhasil diimport</p>
      <input type="hidden" name="step" value="3" />
      @if(count($warnings) > 0)
        <br /><br />
        <p>Terdapat <b>{{ count($warnings) }}</b> warning untuk import ini:</p>
        <table class="table">
          <tr>
            <th style="width:100px" class="align-left">Baris</th>
            <th class="align-left">Error</th>
          </tr>
          @foreach($warnings as $warning)
            <tr>
              <td>{{ $warning['row'] }}</td>
              <td>{{ $warning['message'] }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    @endif
  @endif


@endsection

@section('detail')

  <div id="import-modal" class="modal w-640"
       data-onopen="socket.emit('join', '{{ $channel }}');"
       data-onclose="socket.emit('leave', '{{ $channel }}')" style="height:600px; max-height:80%">

    <form method="post" class="async" action="/{{ $path }}" enctype="multipart/form-data"
          data-progress-cont="#import-modal .progressbar" data-progress-max-percentage="20"
          data-skip-validation-on-action="back">
      @csrf

      <div class="modal-head pad-2">
        <div class="row">
          <div class="col-3">
            <h3>Import</h3>
          </div>
          <div class="col-6 align-center">
            <div class="legend">
              <span class="">
                <div>
                  <span class="circle-fake"></span>
                  <span class="line"></span>
                  <span class="circle active"></span>
                </div>
                <span class="text">Pilih File</span>
              </span>
              <span class="">
                <div>
                  <span class="circle-fake"></span>
                  <span class="line"></span>
                  <span class="circle"></span>
                </div>
                <span class="text">Mapping Kolom</span>
              </span>
              <span class="">
                <div>
                  <span class="circle-fake"></span>
                  <span class="line"></span>
                  <span class="circle"></span>
                </div>
                <span class="text">Hasil</span>
              </span>
            </div>
          </div>
          <div class="col-3 align-right">
            <span class="fa fa-times less pad-1" onclick="$(this).closest('.modal').close()"></span>
          </div>
        </div>
      </div>

      <div class="modal-body pad-2">

        <div class="row">
          <div class="col-12 import-modal-col">

            @yield('detail-1')

          </div>
        </div>

      </div>

      <div class="modal-foot pad-2">

        <div class="row">
          <div class="col-12">
            <div class="progressbar stripes vmart-1">
              <span></span>
            </div>
          </div>

          <div class="col-12 align-right">
            <button class="hpad-2 hidden" name="action" value="back"><label>Kembali</label></button>
            <button class="more hpad-2" name="action" value="next"><label>Lanjut</label></button>
          </div>
        </div>

      </div>

    </form>

  </div>

  <script>

    if(typeof socket == 'undefined'){

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

    }
  </script>

@endsection