<section class="header">
  <div class="pre-header">
    <div class="wrapper vpad-1">
      <div class="srow">
        @if(isset($pre_1) && is_array($pre_1))
          @foreach($pre_1 as $pre)
          <span class="hpad-2 hidden-sm">
            @if(isset($pre['target']))<a href="{{ $pre['target'] }}">@endif
            <span class="{{ $pre['icon'] ?? '' }}"></span>
            <small>{{ $pre['text'] ?? '' }}</small>
            @if(isset($pre['target']))</a>@endif
          </span>
          @endforeach
        @endif
        <div class="hidden-sm"></div>
        @if(isset($pre_2) && is_array($pre_2))
          @foreach($pre_2 as $pre)
            <span class="hpad-2 align-sm-center stretch-sm">
              @if(isset($pre['target']))<a href="{{ $pre['target'] }}">@endif
                <span class="hmarr-1 {{ $pre['icon'] ?? '' }}"></span>
                <small>{{ $pre['text'] ?? '' }}</small>
                @if(isset($pre['target']))</a>@endif
            </span>
          @endforeach
        @endif
      </div>
    </div>
  </div>
  <div class="header-main">
    <div class="wrapper">
      <div class="srow valign-middle">
            <span class="hidden-lg hpad-2">
              <span class="icon-hamburger"></span>
            </span>
        <span class="stretch-sm">
              <span class="header-logo vmar-1"></span>
            </span>
        <div class="hidden-sm">

          @if(isset($navs) && is_array($navs))
          <nav>

            @foreach($navs as $idx=>$nav)
            <span class="item">
              <a href="{{ $nav['target'] ?? '' }}">{{ $nav['text'] ?? 'Unnamed' }}</a>
              @if(isset($nav['items']) && is_array($nav['items']))
              <div class="subnav">
                <div class="wrapper">
                  <div class="cls">
                    @foreach($nav['items'] as $idx=>$item)
                      @switch(($type = $item['type'] ?? 0))

                        @case(1)
                        <div class="item pc-25">
                          <strong>{{ $item['text'] ?? 'Unnamed' }}</strong>
                          @foreach(($sub_items = $item['items'] ?? []) as $sub_item)
                            <a href="{{ $sub_item['target'] ?? '' }}" class="block vmar-1">{{ $sub_item['text'] ?? 'Unnamed' }}</a>
                          @endforeach
                        </div>
                        @break

                        @case(2)
                          <div class="pc-50 cls">
                          @foreach(($sub_items = $item['items'] ?? []) as $sub_item)
                            <div class="pc-50 srow">
                              <span>
                                <span class="img unloaded img-96" data-src="{{ $sub_item['image_url'] ?? '' }}"></span>
                              </span>
                              <div>
                                <label>{{ $sub_item['title'] ?? 'Untitled' }}</label><br />
                                <small class="line-through">Rp. {{ number_format($sub_item['price'] ?? 0) }}</small><br />
                                <strong class="more">Rp. {{ number_format($sub_item['price_discount'] ?? 0) }}</strong><br />
                                <a href="{{ $sub_item['target'] ?? '' }}" class="more">Lihat</a>
                              </div>
                            </div>
                          @endforeach
                          </div>
                        @break

                      @endswitch
                    @endforeach
                  </div>
                </div>
              </div>
              @endif
            </span>
            @endforeach

          </nav>
          @endif

        </div>
        <span class="srow valign-middle">

              <div class="searchbox hmarr-2 hidden-sm">
                <span class="icon-search"></span>
                <input type="text" placeholder="sofa 2 seater"/>
              </div>

              <span class="pad-1 hidden-lg">
                <span class="icon-search"></span>
              </span>

              <span class="pad-1">
                <span class="cart">
                  <a href="/cart"><span class="icon-cart"></span></a>
                  <span class="badge"><span><span>2</span></span></span>
                </span>
              </span>

              <span class="pad-1">
                <a href="#" class="hpad-1 more">LOGIN</a>
              </span>

            </span>
      </div>
    </div>
  </div>
  <div class="header-overlay"></div>
</section>

@push('body-post')

  <div class="header-mobile-nav hidden-lg">

    <div class="pad-1">
      <div class="srow valign-middle">
        <div>
          <span class="header-logo"></span>
        </div>
        <span class="hpad-1">
          <span class="icon-times"></span>
        </span>
      </div>
    </div>

    <div class="line"></div>

    <div class="cls sp0-sm left-pane">
      <div class="pc-30 bg-light bordered-right pad-1 tabs" data-cont=".right-pane">
        @if(isset($navs) && is_array($navs))
          @foreach($navs as $idx=>$nav)
            <span class="item{{ $idx == 0 ? ' active' : '' }} block pad-0 mar-0 vpad-1 hpad-05">{{ $nav['text'] ?? 'Unnamed' }}</span>
          @endforeach
        @endif
      </div>
      <div class="pc-70 pad-1 right-pane">

        @if(isset($navs) && is_array($navs))
          @foreach($navs as $idx=>$nav)
            <div>
              @foreach($nav['items'] as $idx=>$item)
                @switch(($type = $item['type'] ?? 0))
                  @case(1)
                  <div class="item">
                    <strong class="block vpad-1">{{ $item['text'] ?? 'Unnamed' }}</strong>
                    @foreach(($sub_items = $item['items'] ?? []) as $sub_item)
                      <a href="{{ $sub_item['target'] ?? '' }}" class="block vpad-1">{{ $sub_item['text'] ?? 'Unnamed' }}</a>
                    @endforeach
                  </div>
                  @break
                  @case(2)
                  @foreach(($sub_items = $item['items'] ?? []) as $sub_item)
                    <div class="item">
                      <div class="srow vmart-2">
                  <span>
                    <span class="img unloaded img-96" data-src="{{ $sub_item['image_url'] ?? '' }}"></span>
                  </span>
                        <div>
                          <label>{{ $sub_item['title'] ?? 'Untitled' }}</label><br />
                          <small class="line-through">Rp. {{ number_format($sub_item['price'] ?? 0) }}</small><br />
                          <strong class="more">Rp. {{ number_format($sub_item['price_discount'] ?? 0) }}</strong><br />
                          <a href="{{ $sub_item['target'] ?? '' }}" class="more">Lihat</a>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  @break
                @endswitch
              @endforeach
            </div>
          @endforeach
        @endif

      </div>
    </div>
  </div>

@endpush