<tr data-id="{{ $item->id ?? '' }}">
  <td><label>{{ $item->id }}</label></td>
  <td>
    <div class="pad-1 align-center">
      <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async"><span class="fa fa-ellipsis-h selectable"></span></a>
    </div>
  </td>
  <td><label>{{ $item->name ?? '' }}</label></td>
  <td></td>
</tr>