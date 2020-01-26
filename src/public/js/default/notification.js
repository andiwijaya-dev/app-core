(function() {

  $.fn.extend({

  })

  $.extend({

    notify:function(params){

      if($('.notification').length <= 0)
        $('.screen').append(`<div class="notification">
    <div class="row">
      <div class="col-11">
        <strong class="title">Title</strong>
        <p class="description"></p>
      </div>
      <div class="col-1 align-right">
        <span class="icon-remove fa fa-times less pad-1"></span>
      </div>
    </div>
  </div>`);

      if($('.notification').hasClass('active')){
        $('.notification').removeClassThen('active', 'on', true);
        window.setTimeout(function(){
          $.notify(params);
        }, 300);
        return;
      }

      $('.title', '.notification').html($.val('title', params, { d:'No title' }));
      $('.description', '.notification').html($.val('description', params, { d:'' }));

      $('.icon-remove', '.notification').off('click').on('click', function(){
        $('.notification').removeClassThen('active', 'on', true);
      })

      $('.notification').addClassThen('on', 'active');

      if(parseInt((timeout = $.val('timeout', params))) > 0){
        window.setTimeout(function(){
          $('.notification').removeClassThen('active', 'on', true);
        }, timeout);
      }

    }

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('notification');

})();