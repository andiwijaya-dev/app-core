@if($message->direction == \Andiwijaya\AppCore\Models\ChatMessage::DIRECTION_IN)
  <div class="message direction-in" data-id="{{ $message->id }}">
    <span>
      <div class="text">
        <p>{!! $message->text !!}</p>
      </div>
      @if(is_array($message->images) && count($message->images) > 0)
        <div class="images pad-1 vpadb-0">
          @foreach($message->images as $idx=>$image)
            <span class="img unloaded" style="width:24px;height:24px;background:#eee" data-src="{{ \Illuminate\Support\Facades\Storage::disk($storage ?? 'images')->url($image) }}"
                  data-preview="{{ \Illuminate\Support\Facades\Storage::disk($storage ?? 'images')->url($image) }}"></span>
          @endforeach
        </div>
      @endif
      <div class="hpad-1">
        <small class="less">{{ $message->created_at->diffForHumans() }}</small>
      </div>
    </span>
  </div>

@else
  <div class="message direction-out" data-id="{{ $message->id }}">
    <span>
      <div class="text">
        <p>{!! $message->text !!}</p>
      </div>
      @if(is_array($message->images) && count($message->images) > 0)
        <div class="images pad-1 vpadb-0">
          @foreach($message->images as $idx=>$image)
            <span class="img unloaded" style="width:24px;height:24px;background:#eee" data-src="{{ \Illuminate\Support\Facades\Storage::disk($storage ?? 'images')->url($image) }}"
                  data-preview="{{ \Illuminate\Support\Facades\Storage::disk($storage ?? 'images')->url($image) }}"></span>
          @endforeach
        </div>
      @endif
      <div class="hpad-1">
        <small class="less">{{ $message->created_at->diffForHumans() }}</small>
      </div>
    </span>
  </div>

@endif

