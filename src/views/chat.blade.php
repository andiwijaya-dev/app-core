@if(isset($extends))
  @extends($extends)
@endif

@section('intro')

<form class="async" method="post" action="/chat">
  @csrf

  <div class="row2">

    <div class="col-12">
      <br />
      <h3>Hello,</h3>
      <p>Terima kasih atas kunjungannya. Silakan masukkan informasi dibawah ini untuk menghubungi kami:</p>
      <br />
      <br />
    </div>

    <div class="col-12">
      <strong>Email</strong>
      <div class="textbox" data-validation="required">
        <input type="text" name="key"/>
      </div>
    </div>

    <div class="col-12">
      <strong>Topic</strong>
      <div class="textbox" data-validation="required">
        <input type="text" name="topic"/>
      </div>
    </div>

    <div class="col-12">
      <button class="hpad-1 more" name="action" value="auth"><label>Submit</label></button>
    </div>

  </div>

</form>

@endsection

@section('intro-head')

  <div class="rowg valign-middle">
    <div class="col-ft">
      <span class="img unloaded" data-src="{{ rand_image(1) }}"></span>
    </div>
    <div class="col-st">
      <strong>Live chat</strong>
    </div>
    <div class="col-ft">
      <span class="icon-close fa fa-minus pad-1" onclick="$.chat_popup_close()"></span>
    </div>
  </div>

@endsection

@section('chat-head')

  @if(isset($item->id))
    <div class="rowg valign-middle">
      <div class="col-ft">
        <span class="img unloaded" data-src="{{ rand_image(1) }}"></span>
      </div>
      <div class="col-st">
        <strong>{{ $item->title }}</strong><br />
        <small>{{ $item->created_at }}</small>
      </div>
      <div class="col-ft">
        <button class="chat-close" onclick="$.chat_end()"><small>Selesai</small></button>
        <span class="icon-close fa fa-minus pad-1" onclick="$.chat_popup_close()"></span>
      </div>
    </div>
  @endif

@endsection

@section('chat-body')

  @if(isset($item))
  <div>

    @if($item->messages()->count() > 500)
    <div class="align-center">
      <span class="icon-more"><span class="fa fa-ellipsis-h pad-1" onclick="$.fetch('')"></span></span>
    </div>
    @endif

    @foreach($item->latest_messages as $message)
      @component('andiwijaya::components.customer-chat-message', [ 'item'=>$message ])@endcomponent
    @endforeach

    <div class="pad-1 hmar-1 status-typing hidden">
      <span class="fa fa-comments"></span>
      <label class="less">Typing...</label>
    </div>

  </div>
  @endif

  @if(isset($item))

  <script>

    $.wsConnect(
      function(){
        if(typeof last_discussion_channel != 'undefined') socket.emit('leave', last_discussion_channel);
        socket.emit('join', last_discussion_channel = 'customer-discussion-{{ $item->id }}');
      },
      {
        host:'{{ env('UPDATER_HOST') }}'
      }
    );

  </script>
  @endif

@endsection

@section('chat-foot')

  @if(isset($item))
  <form method="post" class="async" action="/chat">
    @csrf

    <div class="rowg">

      <div class="col-st">
        <div class="textbox" data-validation="required">
          <input type="text" name="text"/>
        </div>
      </div>

      <div class="col-ft">
        <button type="button" onclick="$.chat_popup_add_image()"><label>&nbsp;<span class="fa fa-image"></span>&nbsp;</label></button>
      </div>

      <div class="col-ft">
        <button class="hpad-2" name="action" value="send-message"><label>Send</label></button>
      </div>

    </div>

    <div class="row vpadt-0">
      <div class="col-12 image-cont"></div>
    </div>

  </form>
  @endif

@endsection

@section('chat-popup')

  <div class="chat-popup">
    <div class="chat-popup-head">
      @if(isset($item->id))
        @yield('chat-head')
      @else
        @yield('intro-head')
      @endif
    </div>
    <div class="chat-popup-body">
      @if(isset($item->id))
        @yield('chat-body')
      @else
        @yield('intro')
      @endif
    </div>
    <div class="chat-popup-foot">
      @if(isset($item->id))
        @yield('chat-foot')
      @else
      @endif
    </div>
    <div class="chat-popup-foot-post">
      <small>Supported by andiwijaya.me</small>
    </div>
  </div>

@endsection

@section('content')

  <div class="chat-badge"><label></label><span class="unread hidden"><span></span></span></div>

@endsection