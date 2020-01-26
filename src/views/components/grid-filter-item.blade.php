<div class="rowc filter-item">
  <div class="col-4">
    <div class="row0 nowrap valign-middle">
      <div class="col-ft">
        <span class="fa fa-times" onclick="filter_item_remove.apply(this)"></span>
      </div>
      <div class="col-st">
        <div class="dropdown hmarl-1" data-validation="required">
          <select class="filter-name" name="filters[{{ $idx }}][name]">
            <option value="" disabled{{ !in_array($filter['name'], collect($columns)->pluck('name')->toArray()) ? ' selected' : '' }}>Pilih Kolom</option>
            @foreach($columns as $column)
              <option value="{{ $column['name'] }}"{{ $filter['name'] == $column['name'] ? ' selected' : '' }}>{{ $column['text'] }}</option>
            @endforeach
          </select>
          <span class="icon fa fa-caret-down"></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-8">
    <div class="filter-subitem-cont">
      @foreach($filter['values'] as $sub_idx=>$item)
        @component('andiwijaya::components.grid-filter-subitem', [ 'idx'=>$idx, 'sub_idx'=>$sub_idx, 'item'=>$item ])@endcomponent
      @endforeach
    </div>
    <div class="row">
      <div class="col-12 align-center">
        <button type="button" onclick="filter_add_subitem.apply(this)"><label>Tambah</label></button>
      </div>
    </div>
  </div>
</div>