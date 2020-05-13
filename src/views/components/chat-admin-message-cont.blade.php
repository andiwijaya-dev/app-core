@if(isset($discussion))
  <form method="get" class="async" action="/{{ \Illuminate\Support\Facades\Request::path() }}">
    <input type="hidden" name="id" value="" />

    <div class="message-head hidden-sm">
      <div class="flexrow">
        <div>
          <span class="img unloaded" style="width:48px;height:48px" data-src="/images/avatar1.png"></span>
        </div>
        <div class="stretch hmarl-1">
          <h5>{{ $discussion->name ?? $discussion->key }}</h5><br />
          <small>{{ $discussion->created_at->diffForHumans() }}</small>
        </div>
      </div>
    </div>

    <div class="message-list" data-id="{{ $discussion->id }}">
      @component('andiwijaya::components.chat-admin-message-items', compact('discussion', 'messages', 'prev_id', 'last_id', 'view_message_item', 'storage'))@endcomponent
    </div>
  </form>

  <div class="chat-admin-mobile-header hidden">
    <div class="col-sm-2">
      <span class="fa fa-chevron-left pad-2" onclick="$('.chat-admin').chatadmin_close()"></span>
    </div>
    <div class="col-sm-8 align-center"><a href="#"><h5>{{ $discussion->name ?? $discussion->key }}</h5></a></div>
    <div class="col-sm-2 align-right">
      <div>
        <span class="fa fa-ellipsis-h pad-2"></span>
      </div>
    </div>
  </div>

@else
  <div class="align-center"><strong class="less">@lang('text.chat-admin--no-discussion-selected')</strong></div>

@endif