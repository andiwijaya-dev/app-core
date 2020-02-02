<div class="chat-item chat-item-{{ $item->id }}{{ isset($highlight) && $highlight ? ' highlight' : '' }}">
  <div class="row nowrap">
    <div class="col-2">
      <span class="img unloaded rat-88" data-src="{{ $item->image_url }}"></span>
    </div>
    <div class="col-st">
      <strong onclick="$.fetch('/chat/{{ $item->id }}')">{{ $item->title }}</strong>
      <p class="vmart-0">Test chat</p>
    </div>
    <div class="col-ft">
      @if(isset($item->latest_message->id))
        <span class="" style="width:.5em;height:.5em;border-radius:1em;background:green"></span>
        <small>{{ $item->latest_message->created_at->diffForHumans() }}</small>
      @endif
    </div>
  </div>
</div>