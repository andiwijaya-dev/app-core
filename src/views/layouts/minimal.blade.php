<!DOCTYPE html>
<html>
<head>

  <title>{{ $title ?? ($seo['title'] ?? env('APP_NAME')) }}</title>

  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv=”Content-Type” content=”text/html;charset=UTF-8″>
  <meta name="description" content="{!! $meta_description ?? ($seo['description'] ?? '') !!}">
  <meta name="keywords" content="{!! $meta_keywords ?? ($seo['keyword'] ?? '') !!}">
  @if(isset($meta_canonical) || isset($seo->canonical))<link rel="canonical" href="{!! $meta_canonical ?? ($seo['canonical'] ?? '') !!}" />@endif
  @if(env('GOOGLE_CLIENT_ID'))<meta name="google-signin-client_id" content="{{ env('GOOGLE_CLIENT_ID') }}">@endif
  @if(isset($noindex))<meta name="robots" content="noindex">@endif

  @if(isset($og))
    <meta property="og:url"           content="{{ $og['url'] ?? '' }}" />
    <meta property="og:type"          content="{{ $og['type'] ?? '' }}" />
    <meta property="og:title"         content="{{ $og['title'] ?? '' }}" />
    <meta property="og:description"   content="{{ $og['description'] ?? '' }}" />
    <meta property="og:image"         content="{{ $og['image'] ?? '' }}" />
  @endif

  <style type="text/css">
    .splash{ position: fixed; left:50%; top:50%; transform: translate3d(-50%, -50%, 0); }
    body .screen{ visibility: hidden; }
  </style>

  <script>
    function c(){
      _c++;
      if(_c >= document.querySelectorAll("link[rel=stylesheet]").length){
        document.body.classList.add('css-loaded');

        if(typeof $ != 'undefined' && typeof $.doc_init == 'function' && $._doc_init !== true){
          window.setTimeout(function(){
            $.doc_init();
          }, 23);
        }
      }
      this.media = 'all';
    }
    _c = 0;

    window.scriptBuffer = [];

    @if(config('webhistory.enabled') && ($tracker_url = config('webhistory.hosts', [])[\Illuminate\Support\Facades\Request::getHost()] ?? ''))
      window.__tracker_enabled = 1;
      window.__tracker_url = '{{ $tracker_url }}';
    @endif

  </script>
  <script type="text/javascript" src="/js/jquery.min.js" defer></script>
  <script type="text/javascript" src="/js/exif.js" defer></script>
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
<body class="{{ isset($body_class) ? ' ' . $body_class : '' }}">

  @stack('body-pre')

  @section('splash')
    <div class="splash">
      <h5>Loading...</h5>
    </div>
  @endsection

  @if(!isset($no_splash) || !$no_splash)
  @yield('splash')
  @endif

  <div class="screen{{ isset($screen_class) ? ' ' . $screen_class : '' }}">
    @yield('screen')
  </div>

  @stack('body-post')

</body>
</html>