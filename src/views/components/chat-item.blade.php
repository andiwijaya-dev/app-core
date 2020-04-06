<div class="chat-item chat-item-{{ $item->id }}{{ isset($highlight) && $highlight ? ' highlight' : '' }}"
     onclick="$.fetch('{{ $path }}/{{ $item->id }}')">
  <div>
    <span class="img unloaded" style="width:28px;height:28px" data-src="{{ $item->image_url }}"></span>
  </div>
  <div>
    <strong>{{ $item->name ?? $item->key }}</strong>
    @if(isset($item->latest_message->id))
      <p class="vmart-0 less">{{ substr($item->latest_message->text, 0, 30) }}</p>
    @endif
  </div>
  <div>
    @if(isset($item->latest_message->id))
      <small>{{ $item->latest_message->created_at->diffForHumans() }}</small>
    @endif
  </div>
</div>