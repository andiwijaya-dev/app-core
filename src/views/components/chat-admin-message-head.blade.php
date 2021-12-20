<input type="hidden" name="id" value="{{ $discussion->id }}" />

<div class="hidden-sm relative">
  <div class="srow pad-1">
    <span>
      @if(strlen($discussion->avatar_image_url) > 0)
      <span class="img unloaded rounded" style="width:48px;height:48px" data-src="{{ strpos($discussion->avatar_image_url, '://') !== false ? $discussion->avatar_image_url : '/images/' . $discussion->avatar_image_url }}"></span>
      @else
        <span class="img rounded" style="width:48px;height:48px;background:#f5f5f5"></span>
      @endif
    </span>
    <div class="hmarl-1">
      <h5>{{ $discussion->name ?? $discussion->key }}</h5>
      <div class="cls sp0 valign-middle">
        <div class="pc-33 customer-is-online">
          @component('andiwijaya::components.chat-admin-online-status', compact('customer_is_online', 'discussion'))@endcomponent
        </div>
        @if(isset($discussion->extra['phone_number']) && strlen($discussion->extra['phone_number']) > 0)
          <div class="pc-33">
            <small><span class="fa fa-mobile-alt"></span> {{ $discussion->extra['phone_number'] }}</small>
          </div>
        @elseif(strlen($discussion->mobile_number) > 0)
          <div class="pc-33">
            <small><span class="fa fa-mobile-alt"></span> {{ $discussion->mobile_number }}</small>
          </div>
        @endif

        @if(isset($discussion->extra['whatsapp_number']) && strlen($discussion->extra['whatsapp_number']) > 0)
          <div class="pc-33">
            <a href="https://wa.me/{{ str_replace('+', '', $discussion->extra['whatsapp_number']) }}" target="_blank" class="nowrap"><small><span class="fab fa-whatsapp"></span> {{ $discussion->extra['whatsapp_number'] }}</small></a>
          </div>
        @elseif(strlen($discussion->whatsapp_number) > 0)
          <div class="pc-33">
            <a href="https://wa.me/{{ str_replace('+', '', $discussion->whatsapp_number) }}" target="_blank" class="nowrap"><small><span class="fab fa-whatsapp"></span> {{ $discussion->whatsapp_number }}</small></a>
          </div>
        @endif
      </div>

    </div>
  </div>
  <div style="position: absolute;top:0;right:0" class="vpad-1">
    <a href="?action=open-discussion-detail&id={{ $discussion->id }}" class="async pad-1" data-push-state="0">
      <span class="fa fa-ellipsis-v" style="color:#999"></span>
    </a>
  </div>
</div>