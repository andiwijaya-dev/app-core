<form method="post" class="async" action="{{ $path }}">

  <div class="head pad-1">
    <div class="srow">
      <div class="pad-1">
        <h1>Import Selesai</h1>
        <p class="less">Proses import selesai, dibawah ini detil proses import.</p>
        @csrf
      </div>
      <span>
        <span class="fa fa-times pad-1 selectable" data-action="modal.close"></span>
      </span>
    </div>
  </div>

  <div class="body pad-1">

    <div class="cls valign-middle">

      @if(isset($results['summary']))
      <div class="pc-100">
        <div class="cls">
          <div class="pc-25 pad-1 bg-light">
            <strong>New</strong><br />
            <h5>{{ $results['summary']['new'] ?? 'N/A' }}</h5>
          </div>
          <div class="pc-25 pad-1 bg-light">
            <strong>Update</strong><br />
            <h5>{{ $results['summary']['update'] ?? 'N/A' }}</h5>
          </div>
          @if(($results['summary']['delete'] ?? 0) > 0)
          <div class="pc-25 pad-1 bg-light">
            <strong>Delete</strong><br />
            <h5>{{ $results['summary']['delete'] ?? 'N/A' }}</h5>
          </div>
          @endif
          <div class="pc-25 pad-1 bg-light">
            <strong>Warning</strong><br />
            <h5>{{ $results['summary']['warning'] ?? 'N/A' }}</h5>
          </div>
        </div>
      </div>
      @endif

      <div class="pc-100">

        <div class="accordion hmar-1">

          @if(isset($results['errors']))
            <div class="item">
              <div class="cls valign-middle">
                <div class="pc-40"><strong>Error</strong></div>
                <div class="pc-50"><label>{{ count($results['errors']) }}</label></div>
                <div class="pc-10 align-right"></div>
              </div>
              <div>
                @foreach($results['errors'] as $text)
                  <label class="block pad-1">{{ $text }}</label>
                @endforeach
              </div>
            </div>
          @endif

          @if(isset($results['warnings']))
            <div class="item">
              <div class="cls valign-middle">
                <div class="pc-40"><strong>Warning</strong></div>
                <div class="pc-50"><label>{{ count($results['warnings']) }}</label></div>
                <div class="pc-10 align-right"></div>
              </div>
              <div>
                @foreach($results['warnings'] as $text)
                  <label class="block pad-1">{{ $text }}</label>
                @endforeach
              </div>
            </div>
          @endif

          <div class="item">
            <div class="cls valign-middle">
              <div class="pc-40"><strong>Time Ellapsed</strong></div>
              <div class="pc-50"><label>{{ $ellapsed }}s</label></div>
              <div class="pc-10"></div>
            </div>
            <div></div>
          </div>

        </div>

      </div>

    </div>

  </div>

  <div class="foot pad-2 align-right">
    <button type="button" onclick="$(this).closest('.modal').modal_close()" class="max hpad-1"><label>Selesai</label></button>
  </div>

</form>