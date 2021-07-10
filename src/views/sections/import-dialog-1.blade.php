<form method="post" class="async" action="{{ request()->path() }}">
  <div class="modal-head p-3 relative">
    <h1 class="font-size-6">Import</h1>
    <p class="mt-1">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ut dui viverra, tincidunt est vel, volutpat eros. Nam sed orci quam. Cras gravida turpis in nisl condimentum posuere</p>
    <div class="dock-top-right">
      <span class="p-3 fa fa-times cl-hv-gray-500" data-event data-click-close="#import-dialog"></span>
    </div>
  </div>
  <div class="modal-body p-3">
    <div class="bg-gray-100 b-3 p-3">
      <label>Masukkan file import</label>
      <br />
      <input type="file" name="file" />
    </div>
  </div>
  <div class="modal-foot align-right p-3">
    <button class="primary px-3 font-weight-600" name="action" value="analyse">Lanjut</button>
  </div>
</form>