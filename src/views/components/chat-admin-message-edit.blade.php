<div class="flexrow">
  <div class="stretch hpadr-1">
    <div class="textbox " data-validation="required">
      <input type="text" name="text" />
    </div>
  </div>
  <div>
    @csrf
    <input type="hidden" name="id" value="{{ $discussion->id }}" />
    <input type="hidden" name="last_id" />
    <button type="button" onclick="$('.chat-admin').chatadmin_add_file()"><label><span class="fa fa-image"></span></label></button>
    <button class="hpad-2"><label>@lang('text.send')</label></button>
  </div>
</div>
<div class="images pad-1 vpadb-0 repeater">
  <div class="template">
    <!--
    <span class='item recently-created'>
      <input type="file" class="hidden" name="images[][file]" accept="image/*" />
      <input type="hidden" class="image-name" name="images[][name]" />
      <span class="img" style="width:24px;height:24px"></span></span>
    </span>
    -->
  </div>
  <div></div>
</div>