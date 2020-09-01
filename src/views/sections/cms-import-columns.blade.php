<form method="post" class="async" action="{{ $path }}">

  <div class="head pad-1">
    <div class="srow">
      <div class="pad-1">
        <h1>{{ $title ?? '' }}</h1>
        <p class="less">{!! $description ?? '' !!}</p>
        @csrf
      </div>
      <span>
        <span class="fa fa-times pad-1 selectable" data-action="modal.close"></span>
      </span>
    </div>
  </div>

  <div class="body pad-1">

    <div class="cls valign-middle">

      @foreach($columns as $key=>$column)
        @if(!($column['optional'] ?? 0))
          <div class="pc-40"><strong>{{ $column['text'] ?? '' }}</strong></div>
          <div class="pc-60">
            <div class="dropdown" data-validation="required">
              <select name="{{ $key }}">
                <option value="" disabled selected>@lang('text.column-select')</option>
                @foreach($csv_columns as $text)
                  <option value="{{ $text }}"{{ in_array($text, ($column['columns'] ?? [])) ? ' selected' : '' }}>{{ $text }}</option>
                @endforeach
              </select>
              <span class="icon fa fa-caret-down"></span>
            </div>
          </div>
        @else
          <div class="pc-40"><label>{{ $column['text'] ?? '' }}</label></div>
          <div class="pc-60">
            <div class="dropdown">
              <select name="{{ $key }}">
                <option value="" selected>@lang('text.column-unused')</option>
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

  </div>

  <div class="foot pad-2 align-right">
    <a href="{{ $path }}?action=back&step=2" class="async pad-2" data-push-state="0">@lang('text.back')</a>
    <button name="action" value="run" class="max hpad-1"><label>@lang('text.import-run')</label></button>
  </div>

</form>