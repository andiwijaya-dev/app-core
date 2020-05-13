@if($prev_id > 0)
  <div class="align-center vmarb-2 load-prev">
    <button name="action" class="min block" value="load-prev|{{ $prev_id }}"><label class="less">@lang('text.load-prev')...</label></button>
  </div>
@endif

@if(count($messages) > 0)
  @foreach($messages as $idx=>$message)
    @component($view_message_item, compact('idx', 'message', 'prev_id', 'storage'))@endcomponent
  @endforeach
@else
  <div class="align-center"><strong class="less">No messages</strong></div>
@endif