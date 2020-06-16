<input type="hidden" name="id" value="{{ $discussion->id }}" />

<div class="hidden-sm">
  <div class="srow pad-1">
    <span>
      <span class="img unloaded rounded" style="width:48px;height:48px" data-src="/images/{{ $discussion->avatar_image_url }}"></span>
    </span>
    <div class="hmarl-1">
      <h5>{{ $discussion->name ?? $discussion->key }}</h5><br />
      <small>{{ $discussion->created_at->diffForHumans() }}</small>
    </div>
  </div>
</div>