<tr data-id="{{ $item->id ?? '' }}" data-parent=".grid-content tbody">
  <td>
    <div class="pad-1 align-center">
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async"><span class="fa fa-ellipsis-h selectable"></span></a>
    </div>
  </td>
  <td><label>{{ $item->path ?? '' }}</label></td>
  <td><label>{{ $item->created_at->diffForHumans() }}</label></td>
</tr>