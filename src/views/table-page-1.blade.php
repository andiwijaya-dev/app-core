@if(\Illuminate\Support\Facades\View::exists($extends))
@extends($extends)
@endif

@section('content')

  <div class="mw-1365x mx-auto">
    <div class="mx-5">
      <form method="post" class="async" action="" data-event data-init-click="[value=load]">

        <div class="py-3">
          <div class="flex valign-middle">
            <div>
              <h1 class="font-size-6">{{ $title ?? 'Untitled' }}</h1>
            </div>
            <span>
              @yield('right-pane')
            </span>
          </div>
        </div>

        <div>
          <div data-type="table" class="bg-white">
            <div class="table-head">
              <table>
                <thead>
                  <tr>
                    @foreach($columns as $column)
                      <th width="{{ $column['width'] }}px" class="{{ $column['align'] ?? '' }}">{{ $column['text'] ?? '' }}<div class="table-resize"></div></th>
                    @endforeach
                    <th width="100%"></th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="table-body v-scrollable h-70h">
              <template>
                <tr data-id="{id}">
                  @foreach($columns as $column)
                    @if(isset($column['html']))
                      {!! $column['html'] !!}
                    @else
                      <td class="{{ $column['align'] ?? '' }}">
                        <label class="ellipsis">{{ '{' . ($column['name'] . '|' . ($column['datatype'] ?? 'string')) . '}' }}</label>
                      </td>
                    @endif
                  @endforeach
                  <td></td>
                </tr>
              </template>
            </div>
            <div class="table-foot">
              <div class="grid table-5">
                <div>
                  <div class="p-2">
                    <label class="cl-gray-500">Total Data</label>
                    <strong class="cl-gray-500 count">-</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </form>
    </div>
  </div>

@endsection