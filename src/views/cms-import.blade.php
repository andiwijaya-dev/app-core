<form method="post" class="async" action="{{ $path }}">

  <div class="head pad-1">
    <div class="srow">
      <div class="pad-1">
        <h1>{{ $title ?? '' }}</h1>
        <p class="less">{{ $description ?? '' }}</p>
        @csrf
      </div>
      <span>
        <span class="fa fa-times pad-1 selectable" data-action="modal.close"></span>
      </span>
    </div>
  </div>

  <div class="body pad-1">

    <div class="cls">
      <div class="pc-100">
        <div class="bg-light bordered pad-1">
          <label>@lang('text.cms-import-section1-text1')</label>
          <div class="vmart-1">
            <input type="file" name="file" class="import-file"/>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="foot pad-2 align-right">
    <button name="action" value="analyse" class="max hpad-1"><label>@lang('text.upload')</label></button>
  </div>

</form>