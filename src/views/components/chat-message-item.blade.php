<div class="message-item message-{{$item->id}} direction-{{ $item->direction == \Andiwijaya\AppCore\Models\ChatMessage::DIRECTION_IN ? 'in' : 'out' }}">
  <p>{{ $item->text }}</p><br />
  @if(isset($item->images[0]))
    <div class="pad-1">
      @foreach($item->images as $image)
        <span class="img unloaded can-preview" data-src="/images/{{ $image }}" style="width:3em;height:3em"></span>
      @endforeach
    </div>
  @endif
  <small class="less">{{ $item->created_at->format('j M Y H:i') }}</small>
  @if(isset($item->extra['user']))<small class="less hmarl-1">{{ $item->extra['user'] }}</small>@endif
</div>