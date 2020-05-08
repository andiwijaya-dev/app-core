<!DOCTYPE html>
<html>
<head>

  <meta name="viewport" content="width=device-width, user-scalable=no" />

  <link rel="stylesheet" href="/css/all.min.css" />
  @if(true)
    @foreach(glob(public_path('/css/' . ($theme ?? 'clean') . '/*.css')) as $css)
      <link rel="stylesheet" href="{{ asset("/css/" . ($theme ?? 'clean') . "/" . basename($css)) }}?v={{ time() }}" />
    @endforeach
  @else
    <link rel="stylesheet" href="{{ asset("/css/" . ($theme ?? 'clean') . ".css") }}" />
  @endif
  @stack('styles')

  <script src="/vendor/andiwijaya/js/socket.io.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>

  @if(true)
    @foreach(glob(public_path('vendor/andiwijaya/js/default/*.js')) as $js)
      <script type="text/javascript" src="{{ asset("vendor/andiwijaya/js/default/" . basename($js)) }}?v={{ time() }}"></script>
    @endforeach
  @else
    <script type="text/javascript" src="{{ asset("vendor/andiwijaya/js/default.js") }}"></script>
  @endif
  @stack('scripts')

</head>
<body>

<div class="screen layout1">

  <div class="header hidden-lg row0 valign-middle">
    <div class="col-sm-2">
      <span class="fa fa-bars pad-2" data-action="sidebar-open"></span>
    </div>
    <div class="col-sm-8 align-center"><a href="#"><h5>{{ $title ?? 'Untitled' }}</h5></a></div>
    <div class="col-sm-2 align-right">
      <div>
        <span class="fa fa-ellipsis-h pad-2" data-action="filterbar-open"></span>
      </div>
    </div>
  </div>

  <div class="sidebar">
    @yield('sidebar')
  </div>

  <div class="main">
    @yield('content')
  </div>

  <div class="modal-cont"></div>

  <div class="sidebar-cont"></div>

</div>

</body>
</html>