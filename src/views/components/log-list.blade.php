<div class="grid min">
  <table>
    <tbody>
    @if(isset($logs))
      @foreach($logs as $log)
        <tr>
          <td style="width:150px"><label>{{ $log->timestamp }}</label></td>
          <td style="width:40px">
            <div class="align-center">
              <span class="fa fa-bars pad-1 less" onclick="$.fetch('/log/{{ $log->id }}', { data:{ parent:'{{ $parent ?? '' }}' } })"></span>
            </div>
          </td>
          <td style="width:200px"><label>{{ $log->type_text }}</label></td>
          <td style="width:100px"><label>{{ $log->user->name ?? '' }}</label></td>
        </tr>
      @endforeach
    @endif
    </tbody>
  </table>
</div>