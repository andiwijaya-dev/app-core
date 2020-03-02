@extends($extends)

@section('message-list')

  @if(isset($chat))

    <div class="message-list-head pad-1">
      <div class="row">
        <div class="col-1">
          <span class="img unloaded message-chat-image-url rat-88" data-src="{{ $chat->image_url }}"></span>
        </div>
        <div class="col-11">
          <h3>{{ $chat->title }}</h3>
        </div>
      </div>
    </div>

    <div class="message-list-body v-scrollable pad-2 chat-{{ $chat->id }}">
      @if(count($chat->latest_messages) > 0)
        @foreach($chat->latest_messages as $message)
          @component('andiwijaya::components.chat-message-item', [ 'item'=>$message ])@endcomponent
        @endforeach
      @else
        <div class="pad-1"><label>Tidak ada pesan</label></div>
      @endif
    </div>

    <div class="message-list-foot pad-1">

      <form method="post" class="async" action="{{ $path }}" data-onsuccess="$('input[name=message]', this).val('');$('.images-cont', this).html('')">
        @csrf

        <input type="hidden" name="id" value="{{ $chat->id }}" />

        <div class="row nowrap valign-middle">
          <div class="col-ft images-cont"></div>
          <div class="col-st">
            <div class="textbox">
              <input type="text" name="text" placeholder="Ketik pesan disini..."/>
            </div>
          </div>
          <div class="col-ft">
            <button class="hpad-1" name="action" value="send-message"><label>Kirim</label></button>
            <button type="button" onclick="$(this).closest('.chat').chat_attach_image()"><label><span class="icon fa fa-image"></span></label></button>
          </div>
        </div>
      </form>

    </div>

  @else

    <div class="pad-2"><label>Tidak ada chat yang dipilih</label></div>

  @endif

@endsection

@section('chat-list')

  @if(isset($chats))
    @if(count($chats) > 0)
      @foreach($chats as $idx=>$item)
        @component('andiwijaya::components.chat-item', [ 'idx'=>$idx, 'item'=>$item, 'path'=>$path ])@endcomponent
        @if($idx < count($chats) - 1)<div style="height:1px;background:#eee"></div>@endif
      @endforeach
    @else
      <div class="pad-1 no-chat-item"><label>Tidak ada chat</label></div>
    @endif
  @endif

@endsection

@section('info')

  <div class="row0">

    <div class="col-12 align-center">
      <div class="info-head pad-1">
        <span class="tabs less" data-cont=".tab-cont">
          <span class="item active">Informasi</span><span class="item">Histori</span>
        </span>
      </div>
    </div>

    <div class="col-12 tab-cont">

      <div class="info-tab-cont hpad-3 v-scrollable">
      </div>

      <div class="info-tab-cont pad-2 v-scrollable">

      </div>

    </div>

  </div>

@endsection

@section('content')

  @if(isset($chats))

    <div class="chat" style="height:{{ $height }}">

      <div class="row chat-head">
        <div class="col-12">
          <div class="row valign-middle">
            <div class="col-8">
              <h3>Pesan</h3>
            </div>
            <div class="col-4 align-right">
              <form method="get" action="{{ $path }}">
                <button class="min" name="action" value="download">
                  <label class="more">
                    <span class="fa fa-download"></span>
                    Download
                  </label>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row0 chat-body" style="background:#fff">

        <div class="col-3">
          <form method="get" class="async" action="{{ $path }}">
            <div class="chat-list" style="border-right:solid 1px #f5f5f5;">
              <div class="chat-list-head align-center pad-1">
                <span class="tabs less" data-cont=".tab-cont">
                  <span class="item{{ \Illuminate\Support\Facades\Session::get('chat.display') == 'unreplied' ? ' active' : '' }}" onclick="window.location = '?display=unreplied'">Belum Dibalas
                  </span><span class="item{{ \Illuminate\Support\Facades\Session::get('chat.display') != 'unreplied' ? ' active' : '' }}" onclick="window.location = '?display=all'">Semua</span>
                </span>
              </div>
              <div class="chat-list-body pad-1 v-scrollable">
                @yield('chat-list')
              </div>
            </div>
          </form>
        </div>

        <div class="col-5">
          <div class="message-list">

            @yield('message-list')

          </div>
        </div>

        <div class="col-4">
          <div class="info-card" style="border-left:solid 1px #f5f5f5;height:60vh">

            @yield('info')

          </div>
        </div>

      </div>

    </div>
    <script>

      $.wsConnect(function(){
        socket.emit('join', 'chat-list');
      }, {
        host:'{{ env('UPDATER_HOST') }}',
        on_reconnect:function(){
          socket.emit('join', last_discussion_channel);
        }
      });

      @if(isset($chat->id))

      last_discussion_channel = '';

      $.wsConnect(
        function(){
          socket.emit('join', 'discussion-{{ $chat->id }}');
          last_discussion_channel = 'discussion-{{ $chat->id }}';
        },
        {
          host:'{{ env('UPDATER_HOST') }}'
        },
        true
      );

      @endif

    </script>

  @endif

@endsection