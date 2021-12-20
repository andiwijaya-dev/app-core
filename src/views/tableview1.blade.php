@if(isset($extends))
  @extends($extends)
@endif

@section('options-area')
  <button name="action" value="load" class="rounded s-48x">
    <svg enable-background="new 0 0 32 32" height="24" viewBox="0 0 32 32" width="24" xmlns="http://www.w3.org/2000/svg"><g><path d="m3.999 18.785c.3 0 .597-.134.793-.39l.435-.565c.651 3.862 3.356 7.166 7.196 8.499 1.182.41 2.395.607 3.592.607 3.584 0 7.033-1.768 9.098-4.881.305-.46.179-1.081-.281-1.386-.462-.307-1.08-.179-1.387.281-2.249 3.394-6.512 4.83-10.365 3.49-3.197-1.11-5.427-3.893-5.903-7.125l.856.659c.439.335 1.066.254 1.402-.183.337-.438.256-1.065-.182-1.402l-2.371-1.825c-.121-.149-.291-.247-.481-.306-.41-.172-.891-.073-1.173.295l-2.02 2.623c-.337.438-.256 1.065.182 1.402.181.14.396.207.609.207z"/><path d="m7.169 11.33c.459.305 1.08.178 1.387-.281 2.249-3.394 6.516-4.828 10.365-3.49 3.197 1.11 5.427 3.893 5.903 7.125l-.856-.659c-.439-.336-1.066-.255-1.402.183-.337.438-.256 1.065.182 1.402l2.371 1.825c.113.139.268.232.441.295.135.063.276.107.42.107.3 0 .597-.134.793-.39l2.02-2.623c.337-.438.256-1.065-.182-1.402s-1.066-.255-1.402.183l-.435.565c-.651-3.862-3.356-7.166-7.196-8.499-4.72-1.638-9.936.119-12.69 4.273-.305.461-.179 1.081.281 1.386z"/></g></svg>
  </button>
@endsection

@section('filterbar')
  <div class="flex valign-middle">
    @if($searchable)
      <span data-type="textbox" class="bg-white b-3 rounded-3 mr-1 flex valign-middle" data-event data-keyenter-click="[value=load]"
            data-change-click="[value=load]">
        <svg height="18" viewBox="0 0 32 32" width="18" xmlns="http://www.w3.org/2000/svg" class="mx-1" fill="var(--gray-400)"><path d="m30.71 29.29-8.56-8.55a12.0379 12.0379 0 1 0 -1.41 1.41l8.55 8.56a1.0269 1.0269 0 0 0 1.42 0 1.008 1.008 0 0 0 0-1.42zm-17.71-6.29a10 10 0 1 1 10-10 10.0165 10.0165 0 0 1 -10 10z"/></svg>
        <input type="text" class="p-1" name="search" value="" placeholder="Cari..."/>
          <button name="action" value="load" class="hidden"></button>
        <span class="textbox-icon textbox-clear px-1">
          <svg height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg" class="mt-1"><path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10z"/><path d="m16.242 7.757a1 1 0 0 0 -1.414 0l-2.828 2.829-2.828-2.829a1 1 0 1 0 -1.414 1.414l2.828 2.829-2.828 2.829a1 1 0 0 0 0 1.414 1.027 1.027 0 0 0 1.414 0l2.828-2.829 2.828 2.829a1.015 1.015 0 0 0 1.414 0 1 1 0 0 0 0-1.414l-2.828-2.829 2.828-2.829a1 1 0 0 0 0-1.414z"/></svg>
        </span>
      </span>
    @endif

    @if(count($filters) > 0)
      <span class="filter-area"></span>

      <button class="p-1 px-2 bg-gray-100 b-3 rounded-3 flex valign-middle" name="action" value="open-filters">
        <svg style="vertical-align: middle" height="18" viewBox="0 0 24 24" fill="var(--gray-500)" width="18" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m13 4c0-.55228-.4477-1-1-1s-1 .44772-1 1v7h-7c-.55228 0-1 .4477-1 1s.44772 1 1 1h7v7c0 .5523.4477 1 1 1s1-.4477 1-1v-7h7c.5523 0 1-.4477 1-1s-.4477-1-1-1h-7z" fill-rule="evenodd"/></svg>
        <span class="cl-gray-500">Tambah Filter</span>
      </button>
    @endif
  </div>
@endsection

@section('header-row-1')
  <div>
    <div class="flex valign-top">
      <div>
        <h1 class="font-size-6">{{ $title ?? 'Untitled' }}</h1>
      </div>
      <span class="options-area">
        @yield('options-area')
      </span>
    </div>
  </div>
@endsection

@section('content')

  <form method="post" class="async" action="">

    <div class="sticky pb-0" data-event data-sticky-after=".header">
      @yield('header-row-1')
      <div class="my-2 h-scrollable nowrap filterbar">
        @yield('filterbar')
      </div>
      <button name="action" value="load" class="hidden"></button>
      <div class="table-head rt-1">
        <table>
          <thead>
          <tr>
            {!! $column_html ?? '' !!}
          </tr>
          </thead>
        </table>
      </div>
    </div>

    <div>
      <div data-type="table" id="{{ $id }}" data-event data-init-click="[value=load]" data-table-head=".table-head">
        <div class="table-body h-scrollable"></div>
        <input type="hidden" name="_tableview1_id" value="{{ $id }}" />
      </div>
    </div>

    <div class="sticky sticky-bottom bg-white">
      <div class="table-foot"></div>
    </div>

  </form>

@endsection