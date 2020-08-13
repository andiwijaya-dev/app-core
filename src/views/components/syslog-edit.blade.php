<div class="head pad-2">
  <div class="srow">
    <div>
      <h3>System</h3>
    </div>
    <span>
      <span class="fa fa-times selectable pad-1" data-action="modal.close"></span>
    </span>
  </div>
</div>
<div class="body pad-1">
  <div class="row">

    <div class="col-6">
      <strong>Type</strong><br />
      <label>{{ $instance->type_text }}</label>
    </div>

    <div class="col-6">
      <strong>Console</strong><br />
      <label>{{ $instance->data['console'] ? 'Yes' : 'No' }}</label>
    </div>

    <div class="col-12">
      <strong>Message</strong><br />
      <label>{{ $instance->message }}</label>
    </div>

    <div class="col-12">
      <strong>Method</strong><br />
      <label>{{ $instance->data['method'] }} {{ $instance->data['url'] }}</label>
    </div>

    <div class="col-12">
      <strong>Data</strong>
      <pre class="v-scrollable mh-2 bg-light pad-1 mar-0">{!! json_encode($instance->data['data'], JSON_PRETTY_PRINT) !!}</pre>
    </div>

    <div class="col-12">
      <strong>Trace</strong>
      <pre class="v-scrollable mh-3 bg-light pad-1 mar-0">{!! $instance->data['traces'] !!}</pre>
    </div>

  </div>
</div>
<div class="foot pad-1 align-right">
  <button class="hpad-1" type="button" data-action="modal.close"><label>@lang('text.close')</label></button>
</div>
