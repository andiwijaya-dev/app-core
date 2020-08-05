<tr data-id="{{ $item->id ?? '' }}" data-parent=".grid-content tbody" onclick="$.grid_select(this)">
  <td>
    <div class="pad-1 align-center">
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async"><span class="fa fa-ellipsis-h selectable"></span></a>
    </div>
  </td>
  <td><span class="img unloaded rat-88" data-src="/images/"></span></td>
  <td><label>{{ $item->name ?? '' }}</label></td>
  <td></td>
  <td><label>{{ $item->created_at }}</label></td>
</tr>
