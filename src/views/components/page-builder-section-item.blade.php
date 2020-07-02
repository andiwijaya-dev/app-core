<div class="item vmart-2">
  <div>
    <input type="hidden" name="sections[{{ $idx ?? '' }}][type]" value="99" />
    <div>
      <div class="srow sp0 valign-middle">
        <div><strong>Section Title</strong></div>
        <span>
          <span class="fa fa-times selectable vpad-1" onclick="$(this).closest('.repeater').repeater_remove(this)"></span>
        </span>
      </div>
    </div>
    <div class="textbox" data-validation="required">
      <input type="text" name="sections[{{ $idx ?? '' }}][data][title]" value="{{ $section->data['title'] ?? '' }}"/>
    </div>
  </div>

  <div>
    <strong class="block vpad-1">HTML</strong>
    <div>
      <textarea id="section_html_{{ $idx ?? '' }}" class="section-html" name="sections[{{ $idx ?? '' }}][data][html]">{!! $section->data['html'] ?? '' !!}</textarea>
    </div>
  </div>
</div>