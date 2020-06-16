<tr data-id="{{ $item->id ?? '' }}" data-parent=".grid-content tbody">
  <td>
    <div class="pad-1 align-center">
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async"><span class="fa fa-ellipsis-h selectable pad-1"></span></a>
      @if($item->flag != 's')
        <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async" data-method="DELETE" data-confirm="Confirm task removal?"><span class="fa fa-times selectable pad-1"></span></a>
      @endif
    </div>
  </td>
  <td style="width:5%">{!! $item->status_html !!}</td>
  <td style="width:10%"><label>{!! $item->description !!}</label></td>
  <td style="width:10%"><label>{!! isset($item->last_instance->completed_at) ? $item->last_instance->completed_at->diffForHumans() : '' !!}</label></td>
</tr>