<form method="post" class="async" action="{{ $path }}">

  <div class="head p-2">
    <div class="clf">
      <div class="p-2">
        <h1>Import Selesai</h1>
        <p class="less">Proses import selesai, dibawah ini detil proses import.</p>
        @csrf
      </div>
      <span>
        <span class="fa fa-times p-2 cl-gray-300 modal-close"></span>
      </span>
    </div>
  </div>

  <div class="body p-2">

    <div class="cls valign-middle">

      @if(isset($results['summary']))
      <div class="w-100p">
        <div class="cls">
          <div class="w-25p p-2 bg-light">
            <strong>New</strong><br />
            <h5>{{ $results['summary']['new'] ?? 'N/A' }}</h5>
          </div>
          <div class="w-25p p-2 bg-light">
            <strong>Update</strong><br />
            <h5>{{ $results['summary']['update'] ?? 'N/A' }}</h5>
          </div>
          @if(($results['summary']['delete'] ?? 0) > 0)
          <div class="w-25p p-2 bg-light">
            <strong>Delete</strong><br />
            <h5>{{ $results['summary']['delete'] ?? 'N/A' }}</h5>
          </div>
          @endif
          <div class="w-25p p-2 bg-light">
            <strong>Warning</strong><br />
            <h5>{{ $results['summary']['warning'] ?? 'N/A' }}</h5>
          </div>
        </div>
      </div>
      @endif

      <div class="w-100p">

        <div class="accordion mx-1">

          @if(isset($results['errors']))
            <div class="item">
              <div class="cls valign-middle">
                <div class="w-40p"><strong>Error</strong></div>
                <div class="w-50p"><label>{{ count($results['errors']) }}</label></div>
                <div class="w-10p align-right"></div>
              </div>
              <div>
                @foreach($results['errors'] as $text)
                  <label class="block p-2">{{ $text }}</label>
                @endforeach
              </div>
            </div>
          @endif

          @if(isset($results['warnings']))
            <div class="item">
              <div class="cls valign-middle">
                <div class="w-40p"><strong>Warning</strong></div>
                <div class="w-50p"><label>{{ count($results['warnings']) }}</label></div>
                <div class="w-10p align-right"></div>
              </div>
              <div>
                @foreach($results['warnings'] as $text)
                  <label class="block p-2">{{ $text }}</label>
                @endforeach
              </div>
            </div>
          @endif

          <div class="item">
            <div class="cls valign-middle">
              <div class="w-40p"><strong>Time Ellapsed</strong></div>
              <div class="w-50p"><label>{{ $ellapsed }}s</label></div>
              <div class="w-10p"></div>
            </div>
            <div></div>
          </div>

        </div>

      </div>

    </div>

  </div>

  <div class="foot p-2 align-right">
    <button type="button" class="max modal-close"><label>Selesai</label></button>
  </div>

</form>