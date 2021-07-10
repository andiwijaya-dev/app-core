<div class="modal-head p-3 relative">
  <h1 class="font-size-6">Hasil</h1>
  <p class="mt-1">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ut dui viverra, tincidunt est vel, volutpat eros. Nam sed orci quam. Cras gravida turpis in nisl condimentum posuere</p>
  <div class="dock-top-right">
    <span class="p-3 fa fa-times cl-hv-gray-500" data-event data-click-close="#import-dialog"></span>
  </div>
</div>
<div class="modal-body p-3">
  <div class="grid grid-3 gap-3">

    @if(isset($new) || isset($update) || isset($remove))
    <div>
      <div class="bg-gray-100 b-3 p-3 rounded-2">
        <label>Baru</label>
        <h2>{{ $new ?? '-' }}</h2>
      </div>
    </div>
    <div>
      <div class="bg-gray-100 b-3 p-3 rounded-2">
        <label>Update</label>
        <h2>{{ $update ?? '-' }}</h2>
      </div>
    </div>
    <div>
      <div class="bg-gray-100 b-3 p-3 rounded-2">
        <label>Hapus</label>
        <h2>{{ $remove ?? '-' }}</h2>
      </div>
    </div>
    @endif

    @if(isset($logs) && is_array($logs) && count($logs) > 0)
    <div class="grid-span-3">
      <div data-type="table">
        <div class="table-head">
          <table>
            <thead>
              <tr>
                <th width="480px">Info<div class="table-resize"></div></th>
                <th width="100%"></th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="table-body v-scrollable mh-30h">
          <tbody>
            @foreach($logs as $log)
              <tr>
                <td><label class="ellipsis">{{ $log }}</label></td>
                <td></td>
              </tr>
            @endforeach
          </tbody>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
<div class="modal-foot align-right p-3">
  <button class="px-3 font-weight-600 primary" type="button" data-event data-click-close="#import-dialog">Selesai</button>
</div>