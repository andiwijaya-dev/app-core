(function() {

  $.fn.extend({


  })

  $.extend({

    confirm:function(text, callback){

      var description = '';

      if($.type(text) == 'object'){
        description = $.val('description', text, { d:'' });
        text = $.val('text', text, { d:'(tidak ada teks)' });
      }

      if($('.confirm-popup').length <= 0)
        $('.popup-cont').append("<div class='confirm-popup popup'></div>");

      $('.confirm-popup').html("<div class='row'>" +
        "<div class='col-12'>" +
        "<h5>" + text + "</h5>" +
        "<p class='vmarb-1'>" + description + "</p>" +
        "</div>" +
        "<div class='col-12 align-center'>" +
        "<button class='more ok-btn'><label>OK</label></button>" +
        "<button class='hmarl-1 cancel-btn'><label>Batal</label></button>" +
        "</div>" +
        "</div>");

      $('.ok-btn', '.confirm-popup').click(function(){
        $.fire_event(callback);
        $('.confirm-popup').popup_close();
      })

      $('.cancel-btn', '.confirm-popup').click(function(){
        $('.confirm-popup').popup_close();
      })

      $('.confirm-popup').popup_open({ });

    }

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('template');

})();