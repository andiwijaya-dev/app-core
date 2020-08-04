<div class="item" data-id="{{ $discussion->id }}" data-parent=".chat-content" onclick="$.fetch('/chat-admin/{{ $discussion->id }}')">
  <div class="row">
    <div class="col-2">
      <span class="img unloaded rat-77 rounded" data-src="{{ strpos($discussion->avatar_image_url, '://') !== false ? $discussion->avatar_image_url : '/images/' . $discussion->avatar_image_url }}"></span>
    </div>
    <div class="col-10">
      <strong>{{ $discussion->key }}</strong><br />
      <p>{!! \Illuminate\Support\Str::limit($discussion->latest_message->text ?? '') !!}</p>
      <small class="less">{{ isset($discussion->latest_message) ? $discussion->latest_message->created_at->diffForHumans() : '' }}</small>
    </div>
  </div>
</div>