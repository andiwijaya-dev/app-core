@extends('andiwijaya::layouts.minimal', [ 'theme_mode'=>'layout1' ])

@section('sidebar')

@endsection

@section('header')

  <div class="header hidden-lg row0 valign-middle">
    <div class="col-sm-2">
      <span class="fa fa-bars pad-2 sidebar-open"></span>
    </div>
    <div class="col-sm-8 align-center"><a href="#"><h5>{{ $title ?? 'Untitled' }}</h5></a></div>
    <div class="col-sm-2 align-right">
      @yield('options')
    </div>
  </div>

  @yield('sidebar')

@endsection