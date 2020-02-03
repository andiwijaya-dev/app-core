<div class="message-item message-{{$item->id}} direction-{{ $item->direction == \Andiwijaya\AppCore\Models\ChatMessage::DIRECTION_IN ? 'in' : 'out' }}">
  <p>{{ $item->message }}</p>
  @if(isset($item->extra['images'][0]))
  <div class="pad-1">
    @foreach($item->extra['images'] as $image)
      <span class="img unloaded can-preview" data-src="/images/{{ $image }}" style="width:2.5em;height:2.5em"></span>
    @endforeach
  </div>
  @endif
  <small class="less hmarl-1">{{ $item->created_at }} {{ $item->direction }}</small>
</div>