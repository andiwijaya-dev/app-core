<!DOCTYPE html>
<html>
<head>

  <title>{{ $title ?? 'Untitled' }}</title>
  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta http-equiv=”Content-Type” content=”text/html;charset=UTF-8″>
  {{--  <meta http-equiv="Content-Security-Policy" content="default-src 'self' {{ $default_src ?? '' }};script-src 'self' {{ $script_src ?? '' }};style-src 'self' 'unsafe-inline' {{ $style_src ?? '' }};img-src 'self' @stack('img-src');font-src 'self' {{ $font_src ?? '' }}">--}}

  <link rel="preload" href="/css/preload.css?v={{ assets_version() }}" as="style" />
  <link rel="stylesheet" id="preload" href="/css/preload.css?v={{ assets_version() }}" media="screen" />

  @foreach(glob(public_path('css/slurp/*.css')) as $path)
    <link rel="stylesheet" href="/css/slurp/{{ basename($path) }}?v={{ assets_version() }}" media="print" />
  @endforeach
  <link rel="stylesheet" href="/css/all.min.css" media="print" />

  <script defer type="text/javascript" src="/js/jquery-3.5.1.js"></script>
  @foreach(glob(public_path('js/slurp/*.js')) as $path)
    <script defer type="text/javascript" src="/js/slurp/{{ basename($path) }}?v={{ assets_version() }}"></script>
  @endforeach

  @stack('head')

</head>
<body class="{{ $body['class'] ?? '' }}">

<div class="splash">
  @yield('splash')
</div>

<div class="screen">
  <div class="content">
    @yield('content')
  </div>
</div>

</body>
</html>
