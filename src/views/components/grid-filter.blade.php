<div id="filter-modal" class="modal w640">
  <form class="async">

    <div class="modal-head pad-2">
      <div class="row">
        <div class="col-9"><h3>Filter</h3></div>
        <div class="col-3 align-right"><span class="fa fa-times less hpad-1" onclick="$(this).closest('.modal').close()"></span></div>
      </div>
    </div>

    <div class="modal-body pad-2">

      <div class="filter-item-cont">
        @foreach($filters as $idx=>$filter)
          @component('andiwijaya::components.grid-filter-item', [ 'idx'=>$idx, 'columns'=>$columns, 'filter'=>$filter ])@endcomponent
        @endforeach
      </div>

      <div class="row">
        <div class="col-12 align-center">
          <button type="button" onclick="filter_add_item()"><label>Tambah Filter</label></button>
        </div>
      </div>

    </div>

    <div class="modal-foot pad-2 align-right">
      <button class="more hpad-3" name="action" value="apply-filters"><label>Filter</label></button>
      <button class="min hpad-3" type="button" onclick="$(this).closest('.modal').close()"><label>Batal</label></button>
    </div>

  </form>
</div>

<script>

  function filter_add_item(){

    $('.filter-item-cont').append(`@component('andiwijaya::components.grid-filter-item', [ 'idx'=>'', 'columns'=>$columns, 'filter'=>[ 'name'=>'', 'values'=>[] ] ])@endcomponent`);
    filter_rearrange();

  }

  function filter_add_subitem(){

    $('.filter-subitem-cont', $(this).closest('.filter-item')).append(`@component('andiwijaya::components.grid-filter-subitem', [ 'idx'=>'', 'sub_idx'=>'', 'columns'=>$columns, 'item'=>[ 'operand'=>'', 'operator'=>'', 'value'=>'' ] ])@endcomponent`);
    filter_rearrange();

  }

  function filter_item_remove(){

    $(this).closest('.filter-item').remove();

    filter_rearrange();

  }

  function filter_subitem_remove(){

    var filter_item = $(this).closest('.filter-item');

    $(this).closest('.filter-subitem').remove();

    if($('.filter-subitem-cont', filter_item).children().length == 0) $(filter_item).remove();

    filter_rearrange();

  }

  function filter_rearrange(){

    $('.filter-item-cont>.rowc').each(function(){

      var item_idx = $(this).index();

      $('.filter-name', this).attr('name', 'filters[' + item_idx + '][name]');

      $('.filter-subitem-cont>.row0', this).each(function(){

        var subitem_idx = $(this).index();

        subitem_idx == 0 ? $('.filter-operand', this).parent().addClass('hidden') : $('.filter-operand', this).parent().removeClass('hidden');

        $('.filter-operand', this).attr('name', 'filters[' + item_idx + '][values][' + subitem_idx + '][operand]');
        $('.filter-operator', this).attr('name', 'filters[' + item_idx + '][values][' + subitem_idx + '][operator]');
        $('.filter-value', this).attr('name', 'filters[' + item_idx + '][values][' + subitem_idx + '][value]');

      })

    })

  }

</script>