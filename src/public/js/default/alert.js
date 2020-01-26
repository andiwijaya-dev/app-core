(function() {

  $.extend({

    alert:function(text, callback){

      var description = '';

      if($.type(text) == 'object'){
        description = $.val('description', text, { d:'' });
        text = $.val([ 'text', 'title' ], text, { d:'(tidak ada teks)' });
      }

      if($('.alert-popup').length <= 0)
        $('.popup-cont').append("<div class='alert-popup popup'></div>");

      $('.alert-popup').html("<div class='row'>" +
        "<div class='col-12'>" +
        "<h5>" + text + "</h5>" +
        "<pre class='vmarb-1'>" + description + "</pre>" +
        "</div>" +
        "<div class='col-12 align-center'>" +
        "<button class='more ok-btn'><label>OK</label></button>" +
        "</div>" +
        "</div>");

      $('.ok-btn', '.alert-popup').click(function(){
        $.fire_event(callback);
        $('.alert-popup').popup_close();
      })

      $('.cancel-btn', '.alert-popup').click(function(){
        $('.alert-popup').popup_close();
      })

      $('.alert-popup').popup_open({ });

    }

  })

})();