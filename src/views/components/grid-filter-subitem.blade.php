<div class="row0 filter-subitem">
  <div class="col-3">
    <div class="dropdown{{ $sub_idx == 0 ? ' hidden' : '' }}">
      <select class="filter-operand" name="filters[{{ $idx }}][values][{{ $sub_idx }}][operand]">
        <option value="or"{{ $item['operand'] == 'or' ? ' selected' : '' }}>Atau</option>
        <option value="and"{{ $item['operand'] == 'and' ? ' selected' : '' }}>Dan</option>
      </select>
      <span class="icon fa fa-caret-down"></span>
    </div>
  </div>
  <div class="col-3">
    <div class="dropdown hmarl-1 vmarb-1">
      <select class="filter-operator" name="filters[{{ $idx }}][values][{{ $sub_idx }}][operator]">
        <option>=</option>
        <option value="contains"{{ $item['operator'] == 'contains' ? ' selected' : '' }}>Berisi</option>
        <option value="begins_with"{{ $item['operator'] == 'begins_with' ? ' selected' : '' }}>Dimulai</option>
        <option value="ends_with"{{ $item['operator'] == 'ends_with' ? ' selected' : '' }}>Diakhiri</option>
      </select>
      <span class="icon fa fa-caret-down"></span>
    </div>
  </div>
  <div class="col-6">
    <div class="row0 valign-middle">
      <div class="col-st">
        <div class="textbox hmarl-1" data-validation="required">
          <input class="filter-value" name="filters[{{ $idx }}][values][{{ $sub_idx }}][value]" type="text" value="{{ isset($item['value']) ? $item['value'] : '' }}" />
        </div>
      </div>
      <div class="col-ft">
        <span class="fa fa-times pad-1" onclick="filter_subitem_remove.apply(this)"></span>
      </div>
    </div>
  </div>
</div>