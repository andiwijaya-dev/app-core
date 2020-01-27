<html>
<head>
    @if(false)
    @foreach(glob(public_path('vendor/andiwijaya/css/default/*.css')) as $css)
      <link rel="stylesheet" href="{{ asset("vendor/andiwijaya/css/default/" . basename($css)) }}?v={{ time() }}" />
    @endforeach
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset("vendor/andiwijaya/css/default.css") }}" />
    @yield('pre-css')

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

    @if(false)
    @foreach(glob(public_path('vendor/andiwijaya/js/default/*.js')) as $js)
      <script type="text/javascript" src="{{ asset("vendor/andiwijaya/js/default/" . basename($js)) }}?v={{ time() }}"></script>
    @endforeach
    @endif

    <script type="text/javascript" src="{{ asset("vendor/andiwijaya/js/default.js") }}"></script>
  @yield('pre-js')
</head>
<body>

<div class="screen">

  @if(\Illuminate\Support\Facades\Session::get('user_id') > 0)
    <div class="sidebar pad-1">

      @includeIf('admin.sidebar')

    </div>
  @endif

  <div class="content">
    @yield('content')
  </div>

  <div class="modal-cont">
    @yield('modal')
  </div>

  <div class="popup-cont"></div>

</div>

@yield('script')

</body>
</html>