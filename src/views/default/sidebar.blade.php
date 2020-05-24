<div class="head vpadb-1 pad-1">
  <div class="flexrow valign-middle">
    <div>
      <span class="img valign-middle" style="width:42px;height:42px;background:#eee;background-image:url('/images/avatar1.png');border-radius:42px"></span>
    </div>
    <div class="stretch">
      <div class="pad-1">
        <small class="block ellipsis mar-0 pad-0" style="max-width:50px !important;">Business</small>
        <strong>User</strong>
      </div>
    </div>
    <div data-click-popup=".sidebar-head-popup">
      <span class="fa fa-bell selectable hpad-1"></span>
    </div>
  </div>
  <div class="popup sidebar-head-popup" data-ref=".sidebar>.head">
    <div class="popup-body v-scrollable">
      @for($i = 0 ; $i < 20 ; $i++)
        <div class="item flexrow">
          <div class="pad-1"><span class="fa fa-circle hmarr-05"></span></div>
          <div class="stretch ellipsis">
            <label>New chat received</label><br />
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet elementum elit. </p>
          </div>
        </div>
      @endfor
    </div>
  </div>
</div>

<div class="body pad-1 hpad-2">

  <div class="sidemenu">
    <div class="item toggle-submenu">
      <strong class="text less"><span class="fa fa-map hmarr-1"></span>Group 2</strong>
    </div>
    <div class="submenu">
      <div class="item">
        <span class="fa fa-layer-group hmarr-05"></span>
        <a href="" class="text">Module 1</a>
      </div>
      <div class="item">
        <span class="fa fa-layer-group hmarr-05"></span>
        <a href="" class="text">Module 2</a>
      </div>
    </div>

    <div class="item vmart-2">
      <a href="/project" class="text strong less"><span class="fa fa-bullseye hmarr-1"></span>Group 2</a>
    </div>

    <div style="height:10vh"></div>

    <div class="item">
      <a href="" class="text strong less"><span class="fa fa-cog hmarr-1"></span>Settings</a>
    </div>
  </div>

</div>

<div class="foot align-center">
  <div class="row">
    <div class="col-4 align-center">
      <a href="/edit-profile"><span class="fa fa-id-badge pad-1"></span></a>
    </div>
    <div class="col-4 align-center">
      <a href="/logout"><span class="fa fa-sign-out-alt pad-1"></span></a>
    </div>
    <div class="col-4 align-center">
    </div>
  </div>
</div>