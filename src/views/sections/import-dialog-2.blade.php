<form method="post" class="async" action="{{ request()->path() }}">
  <div class="modal-head p-3 relative">
    <h1 class="font-size-6">Mapping Kolom</h1>
    <p class="mt-1">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ut dui viverra, tincidunt est vel, volutpat eros. Nam sed orci quam. Cras gravida turpis in nisl condimentum posuere</p>
    <div class="dock-top-right">
      <span class="p-3 fa fa-times cl-hv-gray-500" data-event data-click-close="#import-dialog"></span>
    </div>
  </div>
  <div class="modal-body p-3">
    <div class="grid grid-2 gap-2 valign-middle">
      @foreach($columns as $column)
        <div>
          @if(($column['optional'] ?? 0))
            <label class="p-2">{!! $column['text'] !!}</label>
          @else
            <strong class="p-2">{!! $column['text'] !!}*</strong>
          @endif
          @if(isset($column['remark']))
            <small class="cl-gray-600 block px-2">{!! $column['remark'] !!}</small>
          @endif
        </div>
        <div>
          <div data-type="dropdown">
            <select name="{{ $column['name'] }}">
              <option></option>
              @foreach($data_columns as $data_column)
                <option value="{{ $data_column }}"
                  {{ in_array($data_column, $column['columns'] ?? []) ? ' selected' : '' }}>
                  {{ $data_column }}
                </option>
              @endforeach
            </select>
            <span class="dropdown-icon fa fa-caret-down"></span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
  <div class="modal-foot align-right p-3">
    <button class="px-3 font-weight-600" name="action" value="view">Sebelum</button>
    <button class="primary px-3 font-weight-600" name="action" value="proceed">Proses</button>
  </div>
</form>