@if(count($items) > 0)

  @if($items->first()->has_prev)
    <div class="align-center load-prev">
      <button class="min" name="action" value="load-prev|{{ $items->first()->id }}"><span class="icon-more"><span class="fa fa-ellipsis-h pad-1"></span></span></button>
    </div>
  @endif

  @foreach($items as $item)
    @component('andiwijaya::components.customer-chat-message', [ 'item'=>$item ])@endcomponent
  @endforeach

@endif
