@if(isset($discussion))

  @component('andiwijaya::components.chat-admin-message-items', compact('discussion', 'messages', 'prev_id', 'last_id', 'view_message_item', 'storage'))@endcomponent

  <div class="chat-admin-mobile-header hidden cls">
    <div class="pc-20">
      <span class="fa fa-chevron-left pad-2" onclick="$('.chat-admin').chatadmin_close()"></span>
    </div>
    <div class="pc-60 align-left">
      <h5>{{ $discussion->name ?? $discussion->key }}</h5>
      <div class="block ellipsis">
        @if(isset($discussion->extra['phone_number']))
          <a href="tel:{{ $discussion->extra['phone_number'] }}"><span class="fa fa-mobile-alt"></span> {{ $discussion->extra['phone_number'] }}</a>
        @endif
        @if(isset($discussion->extra['whatsapp_number']))
          <a href="https://wa.me/{{ $discussion->extra['whatsapp_number'] }}"><span class="fab fa-whatsapp"></span> {{ $discussion->extra['whatsapp_number'] }}</a>
        @endif
      </div>
    </div>
    <div class="pc-20 align-right">

    </div>
  </div>

@else
  <div class="align-center"><strong class="less">@lang('text.chat-admin--no-discussion-selected')</strong></div>

@endif