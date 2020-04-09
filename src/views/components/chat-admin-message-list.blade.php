@if(count($items) > 0)

  @if($items->first()->has_prev)
    <div class="align-center load-prev">
      <button class="min" name="action" value="load-prev|{{ $items->first()->id }}"><label class="more pad-1 inline-block">Muat chat sebelumnya...</label></button>
    </div>
  @endif

  @foreach($items as $message)
    @component('andiwijaya::components.chat-message-item', [ 'item'=>$message ])@endcomponent
  @endforeach

@endif