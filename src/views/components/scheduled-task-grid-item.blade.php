<tr data-id="{{ $item->id ?? '' }}" data-parent=".grid-content tbody">
  <td>
    <div class="pad-1 align-center">
      <a href="/scheduled-task/{{ $item->id }}" class="async" data-push-state="0"><span class="fa fa-ellipsis-h selectable pad-05"></span></a>
      @if($item->flag != 's')
        <a href="/scheduled-task/{{ $item->id }}" class="async" data-method="DELETE" data-confirm="Confirm task removal?" data-push-state="0"><span class="fa fa-times selectable pad-05"></span></a>
      @endif
      <a href="/scheduled-task/{{ $item->id }}?action=run" class="async" data-push-state="0" data-confirm="Run this task?"><span class="fa fa-fast-forward selectable pad-05"></span></a>
    </div>
  </td>
  <td>{!! $item->status_html !!}</td>
  <td><label>{!! $item->description !!}</label></td>
  <td>
    <label>
      @switch($item->repeat)
        @case(\Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_NONE)
          Once
        @break
      @endswitch
    </label>
  </td>
  <td>
    <label>
      @if(!$item->start)
        Immediately
      @else
        {{ $item->start }}
      @endif
    </label>
  </td>
  <td>
    <label>
      <span class="badge green"><span>{{ $item->count }}</span></span>
      <span class="badge red"><span>{{ $item->error }}</span></span>
    </label>
  </td>
</tr>