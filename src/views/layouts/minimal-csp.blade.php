@section('splash')
  @if(!request()->ajax())
@component('andiwijaya::components.splash-1')@endcomponent
  @endif
@endsection

<!DOCTYPE html>
<html>
<head>

  <title>{{ $title ?? ($seo['title'] ?? env('APP_NAME')) }}</title>

  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta http-equiv=”Content-Type” content=”text/html;charset=UTF-8″>
  <meta name="description" content="{!! $meta_description ?? ($seo['description'] ?? '') !!}">
  <meta http-equiv="Content-Security-Policy" content="default-src 'self' {{ $default_src ?? '' }};script-src 'self' {{ $script_src ?? '' }};style-src 'self' 'unsafe-inline' {{ $style_src ?? '' }};img-src 'self' @stack('img-src');font-src 'self' {{ $font_src ?? '' }}">
  <meta name="ad-tracking" content="{{ config('webhistory.hosts', [])[\Illuminate\Support\Facades\Request::getHost()] ?? '' }}">
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

  {{--<link rel="prefetch" href="/css/preload.css" as="style" />
  <link rel="stylesheet" class="preload" href="/css/preload.css"/>--}}

  <style type="text/css" class="preload">
    @media screen{
      .splash{ position: fixed; left:50%; top:50%; transform: translate3d(-50%, -50%, 0); }
      .screen{ visibility: hidden; }
    }
  </style>

  <link rel="stylesheet" href="/css/all.min.css" media="print"/>
  @if(isset($debug) && $debug)
    @foreach(glob(public_path('/css/' . ($css ?? 'default') . '/*.css')) as $idx=>$path)
      <link id="link{{ $idx }}" rel="stylesheet" href="{{ '/css/' . ($css ?? 'default') . '/' . basename($path) }}?v={{ assets_version () }}" media="print"/>
    @endforeach
  @else
    <link rel="stylesheet" href="{{ env('APP_CDN_HOST') }}/css/{{ $css ?? 'default' }}.css?v={{ assets_version () }}" media="print"/>
  @endif

  <script type="text/javascript" src="{{ env('APP_CDN_HOST') }}/js/jquery.min.js" defer></script>
  <script type="text/javascript" src="{{ env('APP_CDN_HOST') }}/js/exif.js" defer></script>
  @if(isset($debug) && $debug)
    @foreach(glob(public_path("/js/" . ($js ?? 'default') . "/*.js")) as $path)
      <script type="text/javascript" src="{{ asset("/js/" . ($js ?? 'default') . "/" . basename($path)) }}?v={{ assets_version() }}" defer></script>
    @endforeach
  @else
    <script type="text/javascript" src="{{ env('APP_CDN_HOST') }}/js/{{ $js ?? 'default' }}.js?v={{ assets_version() }}" defer></script>
  @endif

  @stack('head')

</head>
<body class="{{ isset($body_class) ? ' ' . $body_class : '' }}">

  @stack('body-pre')

  @if(!isset($no_splash) || !$no_splash)
  @yield('splash')
  @endif

  <div class="screen{{ isset($screen_class) ? ' ' . $screen_class : '' }}">

    @yield('screen')
    @yield('screen-post')

  </div>

  @stack('body-post')

</body>
</html>