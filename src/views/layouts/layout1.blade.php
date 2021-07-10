@extends('andiwijaya::layouts.minimal', [ 'screen_class'=>'layout1'])

@section('screen')

  <div class="header hidden-lg hidden-md hidden-xl cls valign-middle">
    <div class="pc-20">
      <span class="fa fa-bars pad-2 sidebar-open"></span>
    </div>
    <div class="pc-60 align-center"><a href="{{ request()->url() }}"><h5>{{ $title ?? 'Untitled' }}</h5></a></div>
    <div class="pc-20 align-right">
      @yield('options')
    </div>
  </div>

  @yield('sidebar')

  <div class="content">
  @yield('content')
  </div>

@endsection