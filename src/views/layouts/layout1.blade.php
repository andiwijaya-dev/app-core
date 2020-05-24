<!DOCTYPE html>
<html>
<head>

  <title>{{ $title ?? env('APP_NAME') }}</title>

  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta http-equiv=”Content-Type” content=”text/html;charset=UTF-8″>
  @if(isset($meta_description))<meta name="description" content="{!! $meta_description !!}">@endif
  @if(isset($meta_keywords))<meta name="keywords" content="{!! $meta_keywords !!}">@endif
  @if(isset($meta_canonical))<link rel="canonical" href="{!! $meta_canonical !!}" />@endif
  @if(env('GOOGLE_CLIENT_ID'))<meta name="google-signin-client_id" content="{{ env('GOOGLE_CLIENT_ID') }}">@endif

  <link rel="stylesheet" href="/css/all.min.css" />
@if(env('APP_NAME') == 'andiwijaya')
@foreach(glob(public_path('/css/' . ($theme ?? 'clean') . '/*.css')) as $css)
  <link rel="stylesheet" href="{{ asset("/css/" . ($theme ?? 'clean') . "/" . basename($css)) }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" />
@endforeach
@else
  <link rel="stylesheet" href="{{ asset("/css/" . ($theme ?? 'clean') . ".css") }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" />
@endif

  <script type="text/javascript" src="/js/jquery.min.js"></script>
  <script src="/js/socket.io.js"></script>
@if(env('APP_NAME') == 'andiwijaya')
@foreach(glob(public_path('/js/default/*.js')) as $js)
  <script type="text/javascript" src="{{ asset("/js/default/" . basename($js)) }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}"></script>
@endforeach
@else
  <script type="text/javascript" src="/js/default.js?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}"></script>
@endif

  @stack('head')

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

  @stack('body')

</div>

</body>
</html>