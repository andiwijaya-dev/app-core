@if(count($discussions) > 0)
  @foreach($discussions as $idx=>$discussion)
    @component($view_discussion_item, [ 'discussion'=>$discussion, 'idx'=>$idx ])@endcomponent
  @endforeach
@else
  @component($view_discussion_no_item)@endcomponent
@endif

@if($after_id > 0)
  <div class="load-more">
    <button class="min block" name="action" value="load-more|{{ $after_id }}"><label class="less">@lang('text.load-more')...</label></button>
  </div>
@endif