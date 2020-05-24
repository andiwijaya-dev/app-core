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

  <style type="text/css">
    .splash{ position: fixed; left:50%; top:50%; transform: translate3d(-50%, -50%, 0); }
    body .screen{ visibility: hidden; }
    body.css-loaded.script-loaded .screen{ visibility: visible; }
  </style>

  <script>
    function c(){
      _c++;
      if(_c >= document.querySelectorAll("link").length){
        document.body.classList.add('css-loaded');
        typeof $ != 'undefined' && typeof $.doc_init == 'function' && $._doc_init !== true ? $.doc_init() : null
      }
      this.media = 'all';
    }
    _c = 0;
  </script>
  <script type="text/javascript" src="/js/jquery.min.js" defer></script>
  @if(isset($debug) && $debug)
    @foreach(glob(public_path("/js/" . ($js ?? 'default') . "/*.js")) as $path)
      <script type="text/javascript" src="{{ asset("/js/" . ($js ?? 'default') . "/" . basename($path)) }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" defer></script>
    @endforeach
  @else
    <script type="text/javascript" src="/js/{{ $js ?? 'default' }}.js?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" defer></script>
  @endif

  <link rel="stylesheet" href="/css/all.min.css" media="print" onload="c.apply(this)"/>
  @if(isset($debug) && $debug)
    @foreach(glob(public_path('/css/' . ($css ?? 'clean') . '/*.css')) as $path)
      <link rel="stylesheet" href="{{ '/css/' . ($css ?? 'clean') . '/' . basename($path) }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" media="print" onload="c.apply(this)"/>
    @endforeach
  @else
    <link rel="stylesheet" href="{{ asset("/css/" . ($css ?? 'default')) }}.css?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}" media="print" onload="c.apply(this)"/>
  @endif
  <noscript>
    @if(isset($debug) && $debug)
      @foreach(glob(public_path('/css/' . ($css ?? 'style') . '/*.css')) as $path)
        <link rel="stylesheet" href="{{ '/css/' . ($css ?? 'style') . '/' . basename($path) }}?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}"/>
      @endforeach
    @else
      <link rel="stylesheet" href="{{ asset("/css/" . ($css ?? 'default')) }}.css?v={{ env('APP_ENV') == 'production' ? env('APP_VERSION') : time()}}"/>
    @endif
  </noscript>

  @stack('head')

</head>
<body class="{{ $theme_mode ?? '' }}">

  @yield('body-pre')

  @section('splash')
    <div class="splash">
      <h5>Loading...</h5>
    </div>
  @endsection

  @yield('splash')

  <div class="screen">

    @yield('header')

    @yield('content')

    @yield('footer')

  </div>

  @yield('body-post')

</body>
</html>