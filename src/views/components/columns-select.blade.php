@if(isset($columns))

  <div id="columns-modal" class="modal w-320">

    <form class="async">

      <div class="modal-head pad-2">
        <div class="row0 nowrap valign-middle">
          <div class="col-st"><h3>Pilih Kolom</h3></div>
          <div class="col-ft"><span class="fa fa-times less pad-1" onclick="$(this).closest('.modal').close()"></span></div>
        </div>
      </div>

      <div class="modal-body pad-2">
        <div>
          @foreach($columns as $idx=>$column)
            <div>
              <input id="column-{{ $idx }}" type="checkbox" name="{{ $column['name'] }}"{{ isset($column['active']) && $column['active'] ? ' checked' : '' }} value="1"/>
              <label for="column-{{ $idx }}">{{ $column['text'] }}</label>
            </div>
          @endforeach
        </div>
      </div>

      <div class="modal-foot pad-2 align-right">
        <button class="hpad-2 more" name="action" value="apply-columns"><label>Pilih Kolom</label></button>
        <button class="hpad-2 min" type="button" onclick="$(this).closest('.modal').close()"><label>Batal</label></button>
      </div>

    </form>

  </div>

@endif