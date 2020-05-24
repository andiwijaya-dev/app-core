<div class="item hpad-sm-2" data-id="{{ $discussion->id }}" data-parent=".chat-content" onclick="$.fetch(window.location + '/{{ $discussion->id }}')">
  <div class="rowc">
    <div class="col-2">
      <span class="img unloaded rat-77 rounded" data-src="/images/{{ $discussion->avatar_image_url }}"></span>
    </div>
    <div class="col-10">
      <strong>{{ $discussion->key }}</strong><br />
      <p>{!! isset($discussion->latest_message) ? $discussion->latest_message->text : '' !!}</p>
      <small class="less">{{ isset($discussion->latest_message) ? $discussion->latest_message->created_at->diffForHumans() : '' }}</small>
    </div>
  </div>
</div>