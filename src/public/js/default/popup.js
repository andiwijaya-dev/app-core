(function(){

  $.fn.extend({

    popup_open:function(options){

      if(!$(this).hasClass('popup')) return $.error('Invalid thisArgs for popup_open');

      options = typeof options == 'undefined' || $.type(options) != 'object' ? {} : options;

      $(this).data('parent', $(this).parent());
      $(this).css({ height:'' });
      $('.popup-cont').append(this);
      $('.popup-cont').addClass('active');
      $(document.body).addClass('no-scroll');
      $(this).addClassThen('on', 'active');

      if(window.matchMedia("screen and (min-width:320px) and (max-width:800px)").matches){

        $(this).css({ 'height': window.innerHeight * .6 });

      }
      else{

        if(typeof options['ref'] != 'undefined'){

          var position = $(options['ref'])[0].getBoundingClientRect();
          var minWidth = $(options['ref']).outerWidth();
          var height = $(this).length > 0 && typeof $(this)[0].scrollHeight != 'undefined' ? $(this)[0].scrollHeight : 0;

          var left = position.x - 10;
          var top = position.y - 10;

          if($(this)[0].scrollHeight > window.innerHeight)
            height = window.innerHeight * .83;

          top = Math.round(position.y - (height / 2));
          if(top + height > window.innerHeight)
            top = window.innerHeight - height - 10;
          else if(top < 0)
            top = 10;

          $(this).css({ left:left, top:top, height:height, 'min-width':minWidth });
          $(this).data('ref', options['ref']);

        }

      }

      var popup_body = $('.popup-body', this);
      if(popup_body.length > 0){

        var popup_body_height = $(this).height() - (
          $('.popup-head', this).length > 0 ? $('.popup-head', this).outerHeight() : 0 +
          $('.popup-foot', this).length > 0 ? $('.popup-foot', this).outerHeight() : 0
        );
        popup_body.css({ height:popup_body_height });
      }

      $.fire_event($(this).attr('data-onopen'), [], this);

    },

    popup_close:function(){

      if(!$(this).hasClass('popup')) return;

      $(this).data('parent').append(this);
      $(this).removeClassThen('active', 'on');
      $(document.body).removeClass('no-scroll');
      $('.popup-cont').removeClass('active');

      $.fire_event($(this).attr('data-onclose'), [], this);

    },

    popup_toggle:function(options){

      if($(this).hasClass('active')){
        $(this).popup_close(options);
      }
      else{
        $(this).popup_open(options);
      }

    }

  })

  $(function(){
    $(document.body).on('click.popup', function(e){
      if($(e.target).closest('.popup').length <= 0)
        $('.popup.active').popup_close();
    })
  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('popup');

})();