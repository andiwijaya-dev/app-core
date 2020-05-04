@if(strlen($extends) > 0)
  @extends($extends)
@endif

@section('upper-options')
  @if($exportable)
    <button class="more has-right" type="button" onclick="$.fetch('//{{ \Illuminate\Support\Facades\Request::getHost() . '/' .  \Illuminate\Support\Facades\Request::path() }}/create')"><label>New...</label></button><button class="more has-left" type="button" data-click-popup=".action-popup">
      <label>&nbsp;<span class="icon icon-circle-down" style="color:rgba(255, 255, 255, .6);position: relative;top:2px"></span>&nbsp;</label>
    </button>
    <div class="action-popup popup" data-ref=".has-right">
      <a class="item block async" href="{{ \Illuminate\Support\Facades\Request::url() }}?action=export"><span class="icon icon-download"></span>Export...</a>
    </div>
  @else
    <button class="more hpad-1" type="button" onclick="$.fetch('//{{ \Illuminate\Support\Facades\Request::getHost() . '/' .  \Illuminate\Support\Facades\Request::path() }}/create')"><label><span class="fa fa-plus hmarr-1"></span>New...</label></button>
  @endif
@endsection

@section('upper')

  <div class="pad-2 hidden-sm">
    <div class="rowc valign-middle">
      <div class="col-lg-4 col-sm-3">
        <h3>{{ $title }}</h3>
      </div>
      <div class="col-lg-8 col-sm-9 align-right">
        <button class="hidden" name="action" value="search"></button>

        @yield('upper-options')

        <span class="textbox mw20v">
          <input type="text" class="list-search" name="search" value="{{ $search }}"/>
          <span class="icon icon-search"></span>
        </span>
        <button class="hpad-1" type="button" data-action="filterbar-toggle"><label><span class="icon icon-equalizer"></span></label></button>
      </div>
    </div>
  </div>

  <div class="header hidden-lg">
    <div class="row0 valign-middle">
      <div class="col-sm-2">
        <span class="fa fa-bars pad-2" data-action="sidebar-open"></span>
      </div>
      <div class="col-sm-8 align-center"><a href="//{{ \Illuminate\Support\Facades\Request::getHost() . '/' .  \Illuminate\Support\Facades\Request::path() }}"><h5>{{ $title }}</h5></a></div>
      <div class="col-sm-2 align-right">
        <div>
          <span class="icon icon-equalizer pad-2" data-action="filterbar-open"></span>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('desktop-list')

  <div class="pad-1">
    <div class="grid">
      <table>
        <thead>
        @component($view_grid_head, [ 'sorts'=>$sorts ?? [] ])@endcomponent
        </thead>
      </table>
      <div class="grid-content v-scrollable" style="max-height:80vh;" onscroll="if(this.scrollTop + this.clientHeight > this.scrollHeight - 10) $('.load-more-btn').click();">
        <table>
          <thead></thead>
          <tbody>
          @foreach($items as $idx=>$item)
            @component($view_grid_item, [ 'item'=>$item, 'idx'=>$idx ])@endcomponent
          @endforeach
          </tbody>
        </table>
        <div class="load-more-cont">
          @if($next_items_after > 0)
            <div class="pad-1 align-center"><button class="min load-more-btn" name="action" value="load-more|{{ $next_items_after }}"><label class="less">Load More</label></button></div>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection

@section('mobile-list')

  <div class="vpad-1 mobile-content">
    <div class="feed">
      <div class="feed-content">
        @foreach($items as $idx=>$item)
          @component($view_feed_item, [ 'item'=>$item, 'idx'=>$idx ])@endcomponent
        @endforeach
      </div>
    </div>
    <div class="load-more-cont">
      @if($next_items_after > 0)
        <div class="pad-1 align-center"><button class="min load-more-btn" name="action" value="load-more|{{ $next_items_after }},sm"><label class="less">Load More</label></button></div>
      @endif
    </div>
  </div>

  <div class="main-btn">
    <div><span class="icon icon-plus" onclick="$.fetch('//{{ \Illuminate\Support\Facades\Request::getHost() . '/' .  \Illuminate\Support\Facades\Request::path() }}/create')"></span></div>
  </div>

@endsection

@section('filter')

  <div class="filterbar">
    <div class="pad-lg-1 hpad-sm-1">

      <div class="row2 valign-middle">
        <div class="col-10">
          <h5>Edit View</h5>
        </div>
        <div class="col-2 align-right">
          <span class="fa fa-times selectable pad-1" data-action="filterbar-close"></span>
        </div>
      </div>

      <div class="row vmart-1">

        <div class="col-12">
          <div class="vmart-3">
            <button class="hpad-1" name="action" value="reset"><label>Reset</label></button>
          </div>
        </div>

      </div>

      <br /><br />

    </div>
  </div>

@endsection

@section('content')

  <form method="get" class="async">

    <div class="content-board">

      <div class="pad-lg-2">
        @yield('upper')

        <div class="hidden-sm desktop-list-cont">
          @yield('desktop-list')
        </div>

        <div class="hidden-lg after-header mobile-list-cont">
          @yield('mobile-list')
        </div>
      </div>

      <div class="filter-cont">
        @yield('filter')
      </div>

      <div class="content-board-popup-cont"></div>

    </div>

  </form>

@endsection