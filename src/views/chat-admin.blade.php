@extends($extends)

@push('head')
  <script type="text/javascript" src="/js/socket.io.js" defer></script>
@endpush

@section('content')

  <div class="content chat-admin">

    <div class="pad-1 chat-admin-head hidden-sm">

      <div class="row0">
        <div class="col-6">
          <h3 class="hpadl-1">{{ $title ?? 'Chat' }}</h3>
        </div>
        <div class="col-6 align-right">
          <button class="min hpad-1 chat-admin-options" type="button" data-click-popup=".action-popup"><label><span class="fa fa-ellipsis-v"></span></label></button>
          <div class="action-popup popup" data-ref=".chat-admin-options">
            <a class="item block nowrap" href="{{ \Illuminate\Support\Facades\Request::url() }}?action=export">
              <span class="fa fa-cloud-download-alt hmarr-05 selectable"></span>
              Download
            </a>
          </div>
        </div>
      </div>

    </div>

    <div class="chat-admin-body">

      <div class="chat-list">
        <form method="get" class="async">
          <div class="chat-list-head">
            <div class="row0 unread-or-all">
              <div class="col-6">
                <input type="radio" id="filter_0" name="filter" value=""{{ $filter != 'all' ? ' checked' : '' }}/>
                <label for="filter_0">@lang('text.chat-admin--filter-unread')</label>
              </div>
              <div class="col-6">
                <input type="radio" id="filter_1" name="filter" value="all"{{ $filter == 'all' ? ' checked' : '' }}/>
                <label for="filter_1">@lang('text.chat-admin--filter-all')</label>
              </div>
            </div>

            <div class="pad-1">
              <button class="hidden" name="action" value="search"></button>
              <div class="textbox">
                <input type="text" class="list-search" name="search" placeholder="@lang('text.search')..." value="{{ $search ?? '' }}"/>
                <span class="icon fa fa-search"></span>
              </div>
              <button class="block hidden" name="action" value="fetch"></button>
            </div>
          </div>

          <div class="chat-list-body">
            @component('andiwijaya::components.chat-admin-discussion-items', compact('discussions', 'view_discussion_item', 'view_discussion_no_item', 'after_id'))@endcomponent
          </div>
        </form>
      </div>

      <div class="chat-message">
        <form method="get" class="async">
          <div class="chat-message-head">
            @if(isset($discussion))
              @component('andiwijaya::components.chat-admin-message-head', compact('discussion'))@endcomponent
            @endif
          </div>

          <div class="chat-message-body">
            @if(isset($discussion))
              @component('andiwijaya::components.chat-admin-message-foot', compact('discussion'))@endcomponent
            @endif
          </div>
        </form>

        <form method="post" class="async" action="/{{ \Illuminate\Support\Facades\Request::path() }}">
          <div class="chat-message-foot">
            @csrf
            @if(isset($discussion))
              @component('andiwijaya::components.chat-admin-message-foot', compact('discussion'))@endcomponent
            @endif
          </div>
        </form>
      </div>

    </div>

  </div>

  <script>

    window.scriptBuffer.push(function(){

      $.wsListen('{{ $channel_discussion }}', '{{ env('UPDATER_HOST') }}');
    });

  </script>

@endsection