<input type="hidden" name="id" value="{{ $discussion->id }}" />

<div class="hidden-sm">
  <div class="srow pad-1">
    <span>
      <span class="img unloaded rounded" style="width:48px;height:48px" data-src="{{ strpos($discussion->avatar_image_url, '://') !== false ? $discussion->avatar_image_url : '/images/' . $discussion->avatar_image_url }}"></span>
    </span>
    <div class="hmarl-1">
      <h5>{{ $discussion->name ?? $discussion->key }}</h5><br />
      @if(isset($discussion->extra['phone_number']))
      <label><span class="fa fa-mobile-alt"></span> {{ $discussion->extra['phone_number'] }}</label><br />
      @endif
      <small>{{ $discussion->created_at->diffForHumans() }}</small>
    </div>
  </div>
</div>