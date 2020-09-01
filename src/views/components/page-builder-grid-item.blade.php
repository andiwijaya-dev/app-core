<tr data-id="{{ $item->id ?? '' }}" data-parent=".grid-content tbody">
  <td>
    <div class="pad-1 align-center">
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async pad-1" data-push-state="0"><span class="fa fa-ellipsis-h selectable"></span></a>
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async pad-1" data-push-state="0" data-confirm="Are you sure to remove this?" data-method="DELETE"><span class="fa fa-times selectable"></span></a>
    </div>
  </td>
  <td>
    @if($item->is_active)
      <span class="badge green"><span>Aktif</span></span>
    @else
      <span class="badge gray"><span>Tidak</span></span>
    @endif
  </td>
  <td><label>{{ $item->path ?? '' }}</label></td>
  <td><label>{{ $item->created_at->diffForHumans() }}</label></td>
</tr>