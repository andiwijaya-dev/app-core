@extends('andiwijaya::layouts.minimal')

@section('content')

  <div class="pad-2 bordered centered xmw-320 bg-white">
    <form method="post" class="async">
      <div class="row">
        <div class="col-12 align-center">
          <span class="fa fa-2x fa-lock pad-1"></span><br />
          <div class="error-badge badge red"></div>
          @csrf
        </div>
        <div class="col-12 vmart-1"></div>
        <div class="col-12">
          <label>Email/ID</label>
          <div class="textbox vmart-1" data-validation="required">
            <input type="text" name="user_id"/>
          </div>
        </div>
        <div class="col-12">
          <label>Password</label>
          <div class="textbox vmart-1" data-validation="required">
            <input type="password" name="password"/>
          </div>
        </div>
        <div class="col-12">
          <button class="more block vmart-3"><strong class="upper inline-block pad-1">Login</strong></button>
        </div>
      </div>
    </form>
  </div>

  <script>

    $(function(){

      $('*[name=user_id]').select();
    })

  </script>

@endsection