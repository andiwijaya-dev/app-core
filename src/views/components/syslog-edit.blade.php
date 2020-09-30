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

    <div class="col-2">
      <strong>Type</strong><br />
      <label>{{ $instance->type_text }}</label>
    </div>

    <div class="col-2">
      <strong>Console</strong><br />
      <label>{{ $instance->data['console'] ? 'Yes' : 'No' }}</label>
    </div>

    <div class="col-8">
      <strong>Session ID</strong><br />
      <label>{{ $instance->data['session_id'] ?? '-' }}</label>
    </div>

    <div class="col-4">
      <div>
        <strong>IP Address</strong><br />
        <label>{{ $instance->data['remote_ip'] ?? '-' }}</label>
      </div>
      <div class="vmart-05">
        <strong>Timestamp</strong><br />
        <label>{{ $instance->created_at }}</label>
      </div>
    </div>

    <div class="col-8">
      <strong>User Agent</strong><br />
      <label class="block">{{ $instance->data['user_agent'] ?? '-' }}</label>
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

    <div class="col-12">
      <strong>Session</strong>
      <pre class="v-scrollable mh-3 bg-light pad-1 mar-0">{!! json_encode($instance->data['session'] ?? [], JSON_PRETTY_PRINT) !!}</pre>
    </div>

    <div class="col-12">
      <strong>Cookies</strong>
      <pre class="v-scrollable mh-3 bg-light pad-1 mar-0">{!! json_encode($instance->data['cookies'] ?? [], JSON_PRETTY_PRINT) !!}</pre>
    </div>

  </div>
</div>
<div class="foot pad-1 align-right">
  <button class="hpad-1" type="button" data-action="modal.close"><label>Close</label></button>
</div>
