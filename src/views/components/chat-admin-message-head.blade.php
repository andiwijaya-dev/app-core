<input type="hidden" name="id" value="{{ $discussion->id }}" />

<div class="hidden-sm">
  <div class="srow pad-1">
    <span>
      <span class="img unloaded rounded" style="width:48px;height:48px" data-src="{{ strpos($discussion->avatar_image_url, '://') !== false ? $discussion->avatar_image_url : '/images/' . $discussion->avatar_image_url }}"></span>
    </span>
    <div class="hmarl-1">
      <h5>{{ $discussion->name ?? $discussion->key }}</h5>
      <div class="cls sp0 valign-middle">
        <div class="pc-33 customer-is-online">
          @component('andiwijaya::components.chat-admin-online-status', compact('customer_is_online', 'discussion'))@endcomponent
        </div>
        @if(isset($discussion->extra['phone_number']))
          <div class="pc-33">
            <label><span class="fa fa-mobile-alt"></span> {{ $discussion->extra['phone_number'] }}</label>
          </div>
        @endif
        @if(isset($discussion->extra['whatsapp_number']))
          <div class="pc-33">
            <label><span class="fab fa-whatsapp"></span> {{ $discussion->extra['whatsapp_number'] }}</label>
          </div>
        @endif
      </div>

    </div>
  </div>
</div>