<form method="post" class="async">
  <div class="head pad-2">
    <div class="srow">
      <div>
        <h3>{{ isset($task->id) ? $task->description : 'New Scheduled Task' }}</h3>
        @csrf
        <input type="hidden" name="id" value="{{ $task->id ?? '' }}" />
      </div>
      <span>
        <span class="fa fa-times selectable pad-1" data-action="modal.close"></span>
      </span>
    </div>
    @if(isset($task->id))
    <div class="align-center">
      <span class="tabs less vmart-1" data-cont=".scheduled-task-edit-tabcont">
        <span class="item active">Properties</span><span class="item">Result</span>
      </span>
    </div>
    @endif
  </div>

  <div class="body pad-1 vpadt-1 scheduled-task-edit-tabcont">
    <div>
      <div class="row">
        <div class="col-4">
          <label>Status</label>
          <div class="dropdown vmart-1" data-validation="required">
            <select name="status"{{ $task->status > \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_ACTIVE || $readonly ? ' disabled' : '' }}>
              <option value="" disabled selected>Choose Status</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_DISABLED }}"{{ $task->status == \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_DISABLED ? ' selected' : '' }}>Inactive</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_ACTIVE }}"{{ $task->status == \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_ACTIVE ? ' selected' : '' }}>Active</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_COMPLETED }}"{{ $task->status == \Andiwijaya\AppCore\Models\ScheduledTask::STATUS_COMPLETED ? ' selected' : '' }} disabled>Completed</option>
            </select>
            <span class="icon fa fa-caret-down"></span>
          </div>
        </div>
        <div class="col-8"></div>
        <div class="col-12">
          <label>Description</label>
          <div class="textbox vmart-1" data-validation="required">
            <input type="text" name="description" value="{{ $task->description }}"{{ $readonly ? ' readonly' : '' }}/>
          </div>
        </div>
        <div class="col-12">
          <label>Command</label>
          <div class="textbox vmart-1" data-validation="required">
            <input type="text" name="command" value="{{ $task->command }}"{{ $readonly ? ' readonly' : '' }}/>
          </div>
        </div>
        <div class="col-6">
          <label>Start</label>
          <div class="textbox vmart-1">
            <input type="text" name="start" value="{{ $task->start }}"{{ $readonly ? ' readonly' : '' }}/>
          </div>
        </div>
        <div class="col-6"></div>
        <div class="col-4">
          <label>Repeat</label>
          <div class="dropdown vmart-1" data-validation="required">
            <select name="repeat"{{ $readonly ? ' disabled' : '' }}>
              <option value="" disabled selected>Pilih</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_NONE }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_NONE ? ' selected' : '' }}>None</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_MINUTELY }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_MINUTELY ? ' selected' : '' }}>Every Minute</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_EVERY_FIVE_MINUTE }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_EVERY_FIVE_MINUTE ? ' selected' : '' }}>Every 5 Minutes</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_EVERY_TEN_MINUTE }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_EVERY_TEN_MINUTE ? ' selected' : '' }}>Every 10 Minutes</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_HOURLY }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_HOURLY ? ' selected' : '' }}>Every Hour</option>
              <option value="{{ \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_DAILY }}" {{ $task->repeat == \Andiwijaya\AppCore\Models\ScheduledTask::REPEAT_DAILY ? ' selected' : '' }}>Every Day</option>
            </select>
            <span class="icon fa fa-caret-down"></span>
          </div>
        </div>
      </div>
    </div>
    <div class="hidden">
      <div class="cls">
        <div class="pc-100">

          <div class="grid">
            <table>
              <thead>
                <tr>
                  <th style="width:3%"></th>
                  <th style="width:10%"><label>Status</label></th>
                  <th style="width:15%"><label>Started</label></th>
                  <th style="width:10%"><label>Ellapsed</label></th>
                </tr>
              </thead>
              <tbody>
              @if(isset($task->id))
                @foreach($task->results as $result)
                  <tr>
                    <td>
                      <span class="fa fa-bars pad-1 hidden"></span>
                    </td>
                    <td><label>{{ $result->status_text }}</label></td>
                    <td><label>{{ $result->started_at->format('j M Y H:i') }}</label></td>
                    <td><label>{{ $result->ellapsed }}s</label></td>
                  </tr>
                @endforeach
              @endif
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="foot pad-2 align-right scheduled-task-edit-tabcont">
    <div>
      @if(!$readonly)
      <button class="max hpad-2" name="action" value="save"><label>Simpan</label></button>
      @endif
    </div>
    <div class="hidden"></div>
  </div>
</form>