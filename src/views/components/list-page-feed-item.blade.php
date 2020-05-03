<div class="item" data-id="{{ $item->id }}" data-parent=".feed-content">
  <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async">
    <div class="row">
      <div class="col-2">

      </div>
      <div class="col-10">
        <strong>{{ $item->name ?? '' }}</strong><br />
        <small class="less">{{ $item->created_at->diffForHumans() ?? '' }}</small>
      </div>
    </div>
  </a>
</div>