<div class="sidebar-head pad-1">

  <div class="account-bar">
    <div class="row valign-middle">
      <div class="col-4">
        <span class="img unloaded rat-66 fw avatar" data-src="/images/logo-kliknss-1.png?v={{ time() }}"></span>
      </div>
      <div class="col-8">
        <strong>{{ \Illuminate\Support\Facades\Session::get('user')->name ?? '' }}</strong><br />
        <a href="/profile" class="small more">Edit Profil</a>
      </div>
    </div>
  </div>

</div>

<div class="sidebar-body">

  <div class="menu vmart-3">
    <div class="head">
      <div class="pad-1">
        <span class="fa fa-tools"></span>
        <strong>HMC</strong>
      </div>
    </div>
    <div class="body hmarl-2">
      <a href="/motorcycle" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'motorcycle' ? ' active' : '' }}">Motor</a>
    </div>
  </div>

  <div class="menu vmart-2">
    <div class="head">
      <div class="pad-1">
        <span class="fa fa-tools"></span>
        <strong>Sparepart</strong>
      </div>
    </div>
    <div class="body hmarl-2">
      <a href="/sparepart" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'sparepart' ? ' active' : '' }}">Sparepart</a>
      <a href="/catalog" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'catalog' ? ' active' : '' }}">Katalog</a>
    </div>
  </div>

  <div class="menu vmart-2">
    <div class="head">
      <div class="pad-1">
        <span class="fa fa-tools"></span>
        <strong>M2W</strong>
      </div>
    </div>
    <div class="body hmarl-2">
      <a href="/m2w" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w' ? ' active' : '' }}">M2W</a>
      <a href="/m2w/motorcycle" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/motorcycle' ? ' active' : '' }}">M2W Motor</a>
      <a href="/m2w/area" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/area' ? ' active' : '' }}">M2W Area</a>
      <a href="/m2w/insurance" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/insurance' ? ' active' : '' }}">M2W Asuransi</a>
      <a href="/m2w/interest1" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/interest1' ? ' active' : '' }}">M2W Bunga 1</a>
      <a href="/m2w/interest2" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/interest2' ? ' active' : '' }}">M2W Bunga 2</a>
      <a href="/m2w/plafon" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/plafon' ? ' active' : '' }}">M2W Plafon</a>
      <a href="/m2w/rew" class="hmarl-1 pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'm2w/rew' ? ' active' : '' }}">M2W REW</a>
    </div>
  </div>

  <div class="menu vmart-2">
    <div class="head">
      <div class="pad-1">
        <span class="fa fa-tools"></span>
        <strong>Data</strong>
      </div>
    </div>
    <div class="body hmarl-2">
      <a href="/banner" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'banner' ? ' active' : '' }}">Banner</a>
      <a href="/category" class="pad-1 block{{ \Illuminate\Support\Facades\Request::path() == 'category' ? ' active' : '' }}">Kategori</a>
      <a href="/province" class="pad-1 block">Propinsi</a>
      <a href="/city" class="pad-1 block">Kota</a>
      <a href="/kecamatan" class="pad-1 block">Kecamatan</a>
      <a href="/kelurahan" class="pad-1 block">Kelurahan</a>
      <a href="/branch" class="pad-1 block">Cabang</a>
      <a href="/customer" class="pad-1 block">Pelanggan</a>
    </div>
  </div>

  <br />

  <div class="menu">
    <div class="head">
      <div class="pad-1">
        <span class="fa fa-tools"></span>
        <strong>Transaksi</strong>
      </div>
    </div>
    <div class="body hmarl-2">
      <a href="/order" class="pad-1 block">Pesanan</a>
    </div>
  </div>

</div>

<div class="sidebar-foot">
  <div class="row">
    <div class="col-4 align-center">
      <a href="/user" class="pad-1 block"><span class="fa fa-user"></span></a>
    </div>
    <div class="col-4 align-center">
      <a href="/logout" class="pad-1 block"><span class="fa fa-power-off"></span></a>
    </div>
    <div class="col-4 align-center">
      <a href="/log" class="pad-1 block"><span class="fa fa-sticky-note"></span></a>
    </div>
  </div>
</div>