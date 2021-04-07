@extends($view_extends)

@section('content')

  <div class="p-3">
    <div class="flex">
      <span>
        <h1>{{ $title }}</h1>
      </span>
      <div class="align-right">
        <span data-type="dropdown" class="valign-middle">
          <select>
            @foreach($presets as $preset)
              <option>{{ $preset['name'] }}</option>
            @endforeach
          </select>
          <span class="fa fa-caret-down cl-gray-500 dropdown-icon"></span>
        </span>
        <button class="valign-middle">
          <span class="fa fa-ellipsis-h"></span>
        </button>
      </div>
    </div>
  </div>

  <div class="px-3">

    <div data-type="table" class="table">
      <div class="table-head">
        <table>
          <thead>
            <tr>
              @foreach($columns as $column)
                <th width="{{ $column['width'] }}">{{ $column['text'] }}<span class="table-column-resize"></span></th>
              @endforeach
            </tr>
          </thead>
        </table>
      </div>
      <div class="table-body">
        <table>
          <thead>
            <tr>
              @foreach($columns as $column)
              <th width="{{ $column['width'] }}"></th>
              @endforeach
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

@endsection