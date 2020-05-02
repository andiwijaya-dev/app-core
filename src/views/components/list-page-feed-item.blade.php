<div class="item">
  <a href="/{{ \Illuminate\Support\Facades\Request::path() }}/{{ $item->id }}" class="async">
    <div class="row">
      <div class="col-2">

      </div>
      <div class="col-10">
        <strong class="more">{{ $item->id }}</strong><br />
        <label>{{ $item->name ?? '' }}</label><br />
        <small class="less">{{ $item->created_at->diffForHumans() ?? '' }}</small>
      </div>
    </div>
  </a>
</div>