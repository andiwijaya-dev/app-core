@section('type-create')
  @if($item->type == \Andiwijaya\AppCore\Models\Log::TYPE_CREATE)
  <table class="table vmart-2">
    <tr>
      <th style="width:150px"><label>Nama</label></th>
      <td><label>Nilai</label></td>
    </tr>
    @foreach($item->data as $key=>$value)
      @if(is_array($value))
        @foreach($value as $idx=>$obj)
          @foreach($obj as $sub_key=>$sub_value)
            <tr>
              <th><label>{{ $key }}[{{ $idx }}].{{ $sub_key }}</label></th>
              <td><label>{{ is_scalar($sub_value) ? $sub_value : 'N/A' }}</label></td>
            </tr>
          @endforeach
        @endforeach
      @else
        <tr>
          <th><label>{{ $key }}</label></th>
          <td><label>{{ is_scalar($value) ? $value : 'N/A' }}</label></td>
        </tr>
      @endif
    @endforeach
  </table>
  @endif
@endsection

@section('type-update')
  @if($item->type == \Andiwijaya\AppCore\Models\Log::TYPE_UPDATE)
  <table class="table vmart-2">
    <tr>
      <th style="width:150px"><label>Nama</label></th>
      <td><label>Nilai Awal</label></td>
      <td><label>Diubah</label></td>
    </tr>
    @foreach($item->data as $key=>$value)
      @if(is_assoc($value))
        <tr>
          <th><label>{{ $key }}</label></th>
          <td><label>{{ $value['_value'] }}</label></td>
          <td><label>{{ $value['_updates'] }}</label></td>
        </tr>
      @elseif(is_array($value))
        @foreach($value as $idx=>$obj)
          @foreach($obj['_updates'] as $sub_key=>$sub_value)
            <tr>
              <th><label>{{ $key }}[{{ $idx }}].{{ $sub_key }}</label></th>
              <td><label>{{ is_scalar($obj[$sub_key]) ? $obj[$sub_key] : 'N/A' }}</label></td>
              <td><label>{{ is_scalar($sub_value) ? $sub_value : 'N/A' }}</label></td>
            </tr>
          @endforeach
        @endforeach
      @endif
    @endforeach
  </table>
  @endif
@endsection

@section('type-remove')
  @if($item->type == \Andiwijaya\AppCore\Models\Log::TYPE_REMOVE)
    <h3>Remove</h3>
  @endif
@endsection

<div id="log-detail" class="modal w-420" style="height:600px;max-height:80%">

  <div class="modal-head pad-1">
    <div class="row">
      <div class="col-10">
        <h3>Log Detail</h3>
      </div>
      <div class="col-2 align-right">
        <span class="fa fa-times less pad-1" onclick="$(this).closest('.modal').close()"></span>
      </div>
    </div>
  </div>
  <div class="modal-body pad-1">
    <div class="row">
      <div class="col-4">
        <label>Waktu</label>
      </div>
      <div class="col-8">
        <label>{{ $item->timestamp }}</label>
      </div>
      <div class="col-4">
        <label>Tipe</label>
      </div>
      <div class="col-8">
        <label>{{ $item->type }}</label>
      </div>
      <div class="col-4">
        <label>User</label>
      </div>
      <div class="col-8">
        <label>{{ $item->user->name ?? '' }}</label>
      </div>
      <div class="col-12">
        @switch($item->type)
          @case(\Andiwijaya\AppCore\Models\Log::TYPE_CREATE)
            @yield('type-create')
          @break
          @case(\Andiwijaya\AppCore\Models\Log::TYPE_UPDATE)
            @yield('type-update')
          @break
          @case(\Andiwijaya\AppCore\Models\Log::TYPE_REMOVE)
            @yield('type-remove')
          @break
        @endswitch
        {{--<pre>{{ json_encode($item, JSON_PRETTY_PRINT) }}</pre>--}}
      </div>
    </div>
  </div>
  <div class="modal-foot pad-1">
    <div class="row">
      <div class="col-12 align-right">
        <button class="hpad-2" type="button" onclick="$(this).closest('.modal').close()"><label>Close</label></button>
      </div>
    </div>
  </div>

</div>