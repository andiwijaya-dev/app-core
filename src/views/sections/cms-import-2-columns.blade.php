<form method="post" class="async" action="{{ $path }}">

  <div class="modal-head p-2">
    <div class="clf">
      <div class="p-2">
        <h1>{{ $title ?? '' }}</h1>
        <p class="less">{!! $description ?? '' !!}</p>
        <input type="hidden" name="_filename" value="{{ $filename }}" />
        <input type="hidden" name="step" value="2" />
        @csrf
      </div>
      <span>
        <span class="fa fa-times p-2 modal-close cl-gray-300"></span>
      </span>
    </div>
  </div>

  <div class="modal-body p-2">

    <div class="cls valign-middle">

      @foreach($columns as $key=>$column)
        @if(!($column['optional'] ?? 0))
          <div class="w-40p"><strong>{{ $column['text'] ?? '' }}</strong></div>
          <div class="w-60p">
            <div class="dropdown" data-validation="required">
              <select name="{{ $key }}">
                <option value="" disabled selected>Map Columns</option>
                @foreach($csv_columns as $text)
                  <option value="{{ $text }}"{{ in_array($text, ($column['columns'] ?? [])) ? ' selected' : '' }}>{{ $text }}</option>
                @endforeach
              </select>
              <span class="icon fa fa-caret-down"></span>
            </div>
          </div>
        @else
          <div class="w-40p"><label>{{ $column['text'] ?? '' }}</label></div>
          <div class="w-60p">
            <div class="dropdown">
              <select name="{{ $key }}">
                <option value="" selected>Unused</option>
                @foreach($csv_columns as $text)
                  <option value="{{ $text }}"{{ in_array($text, ($column['columns'] ?? [])) ? ' selected' : '' }}>{{ $text }}</option>
                @endforeach
              </select>
              <span class="icon fa fa-caret-down"></span>
            </div>
          </div>
        @endif

      @endforeach

    </div>

    @yield('addons')

  </div>

  <div class="modal-foot p-2 align-right">
    <a href="{{ $path }}?action=back&step=2" class="async p-2">Back</a>
    <button name="action" value="run" class="max"><label>Proceed</label></button>
  </div>

</form>