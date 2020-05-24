<form method="post" class="async" enctype="multipart/form-data">

  <div class="head pad-1">
    <div class="row valign-middle">
      <div class="col-10">
        <h1></h1>
        @csrf
        <input type="hidden" name="id" value="{{ 1 }}" />
      </div>
      <div class="col-2 align-right">
        <span class="pad-1 selectable fa fa-times" data-action="modal.close"></span>
      </div>
    </div>
  </div>

  <div class="body pad-1">

    <div class="row">

      <div class="col-6">

      </div>

      <div class="col-12">
        <button class="block" type="button" onclick="$('button[value=delete]', $(this).closest('.modal')).click()">
          <label>Delete</label>
        </button>
      </div>

    </div>

  </div>

  <div class="foot pad-1 align-right">
    <div class="row">
      <div class="col-12">
        <button class="more hpad-2" name="action" value="save"><label>Save</label></button>
        <button class="hidden" name="action" value="delete" data-confirm="Are you sure to remove this?"></button>
      </div>
    </div>
  </div>

</form>