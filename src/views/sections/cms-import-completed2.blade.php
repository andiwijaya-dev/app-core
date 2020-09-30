<form method="post" class="async" action="{{ $path }}">

  <div class="head pad-1">
    <div class="srow">
      <div class="pad-1">
        <h1>Import sedang Berjalan</h1>
        <p class="less">Proses import sedang berjalan, kamu akan menerima notifikasi setelah proses import ini selesai.</p>
        @csrf
      </div>
      <span>
        <span class="fa fa-times pad-1 selectable" data-action="modal.close"></span>
      </span>
    </div>
  </div>

  <div class="body pad-1">

    <div class="cls valign-middle">

      <div class="pc-100">



      </div>

    </div>

  </div>

  <div class="foot pad-2 align-right">
    <button type="button" onclick="$(this).closest('.modal').modal_close()" class="max hpad-1"><label>OK</label></button>
  </div>

</form>