@extends('andiwijaya::cms-list')

@section('detail')

  <div id="user-detail" class="modal w-560" style="height:480px;max-height:80%">

    <form class="async" method="post">
      @csrf

      <div class="modal-head pad-2">
        <div class="row">
          <div class="col-10">
            <h3>User</h3>
            <input type="hidden" name="id" value="{{ $item->id ?? '' }}" />
          </div>
          <div class="col-2 align-right">
            <span class="icon fa fa-times pad-1 less" onclick="$(this).closest('.modal').close()"></span>
          </div>
          <div class="col-12 align-center">
          <span class="tabs min" data-cont=".user-tab-cont">
            <span class="item active">Detil User</span>
            <span class="item">Privileges</span>
          </span>
          </div>
        </div>
      </div>

      <div class="modal-body pad-2 user-tab-cont">

        <div class="row">

          <div class="col-4">
            <label>Kode</label>
            <div class="textbox vmart-1" data-validation="required">
              <input type="text" name="code" value="{{ $item->code ?? '' }}"/>
            </div>
          </div>

          <div class="col-8"></div>

          <div class="col-8">
            <label>Nama</label>
            <div class="textbox vmart-1" data-validation="required">
              <input type="text" name="name" value="{{ $item->name ?? '' }}"/>
            </div>
          </div>
          <div class="col-4"></div>

          <div class="col-6">
            <label>Email</label>
            <div class="textbox vmart-1" data-validation="required">
              <input type="text" name="email" value="{{ $item->email ?? '' }}"/>
            </div>
          </div>
          <div class="col-6"></div>

          <div class="col-4">
            <label>Password</label>
            <div class="textbox vmart-1">
              <input type="password" name="password"/>
            </div>
          </div>
          <div class="col-4">
            <label>Ulang Password</label>
            <div class="textbox vmart-1">
              <input type="password" name="password_confirmation"/>
            </div>
          </div>

        </div>

        <div class="row hidden">

          <div class="col-12">

            <div class="grid privileges-grid">
              <table>
                <thead>
                <tr>
                  <th style="width:160px"><label onclick="pv_toggle_all()">Modul</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('list')"><label>List</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('create')"><label>Create</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('update')"><label>Update</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('delete')"><label>Delete</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('import')"><label>Import</label></th>
                  <th style="width:54px" class="align-center" onclick="pv_toggle('export')"><label>Export</label></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                @if(isset($modules))
                  @foreach($modules as $idx=>$obj)
                    @component('andiwijaya::components.user-privilege-item', [ 'item'=>$obj, 'user'=>$item ?? [], 'idx'=>$idx ])@endcomponent
                  @endforeach
                @endif
                </tbody>
              </table>
            </div>
            <script>

              function pv_toggle(key){

                var checked = $('.privilege-' + key + ':checked', '.privileges-grid').length > 0 ? true : false;
                $('.privilege-' + key, '.privileges-grid').attr('checked', !checked);

              }

              function pv_toggle_mod(tr){

                var checked = $('*[class*=privilege-]:checked', tr).length > 0 ? true : false;
                $('*[class*=privilege-]', tr).attr('checked', !checked);

              }

              function pv_toggle_all(){

                var checked = $('*[class*=privilege-]:checked', '.privileges-grid').length > 0 ? true : false;
                $('*[class*=privilege-]', '.privileges-grid').attr('checked', !checked);

              }

            </script>

          </div>

        </div>

      </div>

      <div class="modal-foot pad-2">
        <div class="row">
          <div class="col-12 align-right">
            <button class="hpad-2 more" name="action" value="save-and-close"><label>Simpan</label></button>
            <button class="hpad-2" onclick="$(this).closest('.modal').close()"><label>Tutup</label></button>
          </div>
        </div>
      </div>

    </form>

  </div>

@endsection