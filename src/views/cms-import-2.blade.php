<form method="post" class="async" action="{{ $path }}">

  <div class="modal-head p-2">
    <div class="clf">
      <div class="p-1">
        <h1>{{ $title ?? '' }}</h1>
        <p class="less">{!! $description ?? '' !!}</p>
        @if(strlen(($sample_url ?? '')) > 0)
          <br />
          <p>Sampel dapat <a href="{{ $sample_url }}" class="more">didownload disini</a></p>
        @endif
        @csrf
      </div>
      <span>
        <span class="fa fa-times p-1 cl-gray-300 modal-close"></span>
      </span>
    </div>
  </div>

  <div class="modal-body p-2">

    <div class="cls">
      <div class="w-100p">
        <div class="bg-light bordered p-1">
          <label>Select file to import</label>
          <div class="mt-1">
            <input type="file" name="file" class="import-file"/>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="modal-foot p-2 align-right">
    <button name="action" value="analyse" class="max hp-1"><label>Upload</label></button>
  </div>

</form>