<form method="post" class="async">

  <div class="head pad-1">
    <div class="srow">
      <div class="pad-1">
        @if(($page->id ?? 0))
          <h1>Edit page</h1>
        @else
          <h1>Create new page</h1>
        @endif
      </div>
      @csrf
      <input type="hidden" name="id" value="{{ $page->id ?? '' }}" />
      <span>
        <span class="fa fa-times selectable pad-1" data-action="modal.close"></span>
      </span>
    </div>
  </div>

  <div class="body pad-1">
    <div class="cls">

      <div class="pc-100">
        <label>Aktif</label>
        <div class="switch">
          <input type="hidden" name="is_active" value="0" />
          <input type="checkbox" id="is_active" name="is_active" value="1"{{ ($page->is_active ?? 0) ? ' checked' : '' }} />
          <label for="is_active"><span></span></label>
          <span>Tidak Aktif</span>
          <span>Aktif</span>
        </div>
      </div>

      <div class="pc-100">
        <label>Path</label>
        <div class="srow sp0 valign-middle">
          @if(($url ?? ''))
            <span>
              <label class="hpadr-1">{{ $url }}</label>
            </span>
          @endif
          <div>
            <div class="textbox" data-validation="required" style="max-width:480px">
              <input type="text" name="path" value="{{ $page->path ?? '' }}"/>
            </div>
          </div>
        </div>
      </div>

      <div class="pc-100">
        <label>Title</label>
        <div class="textbox" data-validation="required" style="max-width:600px">
          <input type="text" name="title" value="{{ $page->title ?? '' }}"/>
        </div>
      </div>

      <div class="pc-100">
        <label>Description</label>
        <div class="textarea" data-validation="required">
          <textarea name="description">{!! $page->description ?? '' !!}</textarea>
        </div>
      </div>

      <div class="pc-100">
        <label>Keywords</label>
        <div class="textbox" style="max-width:420px">
          <input type="text" name="keywords" value="{{ $page->keywords ?? '' }}"/>
        </div>
      </div>

      <div class="pc-100">
        <label>H1</label>
        <div class="textbox" style="max-width:560px">
          <input type="text" name="h1" value="{{ $page->h1 ?? '' }}"/>
        </div>
      </div>

      @yield('custom-field')

      <div class="pc-100">
        <div class="line vmar-3"></div>
      </div>

      <div class="pc-100">

        <div class="repeater section-repeater">
          <div class="template">
            <!-- @component('andiwijaya::components.page-builder-section-item')@endcomponent -->
          </div>
          <div>
            @if(isset($page->sections))
              @foreach($page->sections as $idx=>$section)
                @component('andiwijaya::components.page-builder-section-item', [ 'idx'=>$idx, 'section'=>$section ])@endcomponent
              @endforeach
            @endif
          </div>
        </div>

        <div class="pad-1 align-center">
          <button class="less" type="button" onclick="$('.section-repeater').repeater_add({}, function(item){ tinymce_init_item(item) })"><label>Add Section</label></button>
        </div>

      </div>

    </div>
  </div>

  <div class="foot pad-1 align-right">
    <button class="more hpad-1" name="action" value="save"><label>Save</label></button>
    <button class="max hpad-1" name="action" value="save-and-close"><label>Save & Close</label></button>
  </div>

</form>