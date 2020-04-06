@switch($item->direction)

  @case(\Andiwijaya\AppCore\Models\ChatMessage::DIRECTION_OUT)
  <div class="message direction-in">
    <div class="text">
      {!! $item->text !!}
    </div>
    @if(isset($item->images[0]))
      <div class="pad-1">
        @foreach($item->images as $image)
          <span class="img unloaded" data-src="/images/{{ $image }}" data-preview="/images/{{ $image }}" style="width:3em;height:3em"></span>
        @endforeach
      </div>
    @endif
    <div>
      <small class="less">{{ $item->created_at->diffForHumans() }}</small>
    </div>
  </div>
  @break

  @case(\Andiwijaya\AppCore\Models\ChatMessage::DIRECTION_IN)
  <div class="message direction-out">
    <div class="text">
      {!! $item->text !!}
    </div>
    @if(isset($item->images[0]))
      <div class="pad-1">
        @foreach($item->images as $image)
          <span class="img unloaded" data-src="/images/{{ $image }}" data-preview="/images/{{ $image }}" style="width:3em;height:3em"></span>
        @endforeach
      </div>
    @endif
    <div>
      <small class="less">{{ $item->created_at->diffForHumans() }}</small>
    </div>
  </div>
  @break

@endswitch