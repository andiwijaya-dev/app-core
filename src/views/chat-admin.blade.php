@extends($extends)

@section('content')

  <div class="chat-admin">

    <div class="pad-2 vpad-1 vpadb-0 chat-admin-head hidden-sm">

      <div class="row">
        <div class="col-6">
          <h3>Chat</h3>
        </div>
        <div class="col-6 align-right">
          <button class="min hpad-1 chat-admin-options" type="button" data-click-popup=".action-popup"><label><span class="fa fa-ellipsis-v"></span></label></button>
          <div class="action-popup popup" data-ref=".chat-admin-options">
            <a class="item block" href="{{ \Illuminate\Support\Facades\Request::url() }}?action=export"><span class="fa fa-cloud-download-alt hmarr-05"></span> @lang('text.download-all')</a>
          </div>
        </div>
      </div>

    </div>

    <div class="chat-admin-body">

      <div>
        <div class="chat-list">
          <div class="vpadt-0">

            <form method="get" class="async">
              <div class="hpad-lg-2 chat-list-section0">
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

                <div class="vmar-1 vmarb-2 hpad-sm-2">
                  <button class="hidden" name="action" value="search"></button>
                  <div class="textbox">
                    <input type="text" class="list-search" name="search" placeholder="@lang('text.search')..." value="{{ $search ?? '' }}"/>
                    <span class="icon fa fa-search"></span>
                  </div>
                  <button class="block hidden" name="action" value="fetch"></button>
                </div>
              </div>

              <div class="chat-list-section0-fixed"></div>

              <div class="hpad-lg-2">
                <div class="chat-content">
                  @component('andiwijaya::components.chat-admin-discussion-items', compact('discussions', 'view_discussion_item', 'view_discussion_no_item', 'after_id'))@endcomponent
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="pad-1">

        <div class="message-cont">
          @component('andiwijaya::components.chat-admin-message-cont', compact('messages', 'discussion'))@endcomponent
        </div>

        <form method="post" class="async" action="/{{ \Illuminate\Support\Facades\Request::path() }}">
          <div class="message-edit">
            @if(isset($discussion))
              @component('andiwijaya::components.chat-admin-message-edit', compact('discussion'))@endcomponent
            @endif
          </div>
        </form>

      </div>

    </div>

  </div>

  <script>

    $.wsListen('{{ $channel_discussion }}', '{{ env('UPDATER_HOST') }}');

  </script>

@endsection