(function(){

  $.fn.extend({

    modal_open:function(options){

      options = typeof options != 'object' ? {} : options;

      if($(this).closest('.modal-cont').length == 0){
        $('.modal-cont').append(this);
        $(this).modal_open(options);
        return;
      }
      
      if($(this).hasClass('closing')){
        if(typeof $__MODAL_POST_CLOSE == 'undefined') $__MODAL_POST_CLOSE = [];
        $__MODAL_POST_CLOSE.push([ [ this, 'modal_open' ], options ]);
        return;
      }

      $('.modal-cont').addClass('active').append(this);
      $(document.body).addClass('no-scroll');
      $('.modal-cont').data('last-scroll-top', document.body.scrollTop);
      $(this).addClass('on');

      var css = {};
      if(typeof options['width'] != 'undefined'){
        var width = options['width'];
        if(width.indexOf('%') > 0) width = Math.round(window.innerWidth * (parseInt(width) / 100));
        css['width'] = width;
      }
      if(typeof options['height'] != 'undefined'){
        var height = options['height'];
        if(height.indexOf('%') > 0) height = Math.round(window.innerHeight * (parseInt(height) / 100));
        css['height'] = height;
      }
      $(this).css(css);


      if($(this).outerHeight() > window.innerHeight){
        $(this).addClass('has-scroll');
      }
      else{

        $(this).removeClass('has-scroll');

        if(!isNaN(parseInt($(this).css('height')))){
          $('.modal-body', this).css({ height:parseInt($(this).css('height')) - ($('.modal-head', this).outerHeight() + $('.modal-foot', this).outerHeight())});
        }

      }

      $('.modal-body', this).off('scroll').on('scroll', function(){
        $.lazy_load();
      })

      if(typeof options['parent'] != 'undefined')
        $(options['parent']).modal_state(0);

      var instance = this;
      window.setTimeout(function(){
        $(instance).addClass('active');
      }, 200);

      $(instance).on('transitionend', function(){
        $.lazy_load();
        $(instance).off('transitionend');
        $.fire_event($(instance).attr('data-onopen'), [], instance);
      })
      $.lazy_load();

      $(this).data('options', options);

      return this;

    },

    modal_close:function(){

      if($(this).hasClass('closing')) return;

      $(this).addClass('closing');
      $(this).removeClassThen('active', [ 'on', 'closing' ], function(){

        if(typeof $__MODAL_POST_CLOSE != 'undefined' && $__MODAL_POST_CLOSE.length > 0){
          do{
            var arr = $__MODAL_POST_CLOSE.splice(0, 1);
            if(typeof arr[0] != 'undefined'){
              var callback = arr[0];
              $.fire_event(callback[0], [ callback[1] ]);
            }
          }
          while($__MODAL_POST_CLOSE.length > 0);
        }

      });

      var options = $(this).data('options');
      if(typeof options != 'undefined' && typeof options['parent'] != 'undefined')
        $(options['parent']).modal_state(1);

      if($('.modal.active').length <= 0 && $('.modal-sm.active').length <= 0){
        $('.modal-cont').removeClass('active');
        $(document.body).removeClass('no-scroll');

        if(typeof $('.modal-cont').data('last-scroll-top') != 'undefined'){
          document.body.scrollTop = $('.modal-cont').data('last-scroll-top');
          $(this).removeData('last-scroll-top');
        }
      }

      return this;

    },

    /**
     * Get/set modal state
     * state:
     *  0: disabled
     *  1: enabled
     */
    modal_state:function(state){

      $(this).each(function(){

        switch(parseInt(state)){

          case 0:
            if($('.modal-overlay', this).length == 0)
              $(this).append("<div class='modal-overlay'></div>");
            $(this).addClass('modal-disabled');
            break;

          case 1:
            $(this).removeClass('modal-disabled');
            break;
        }

      })

    }

  })

  $(function(){

    $('.modal-cont').on('click', function(e){

      if($(e.target).closest('.modal').length == 0 &&
        $(e.target).closest('.modal-sm').length == 0 &&
        $(e.target).closest('.modal-lg').length == 0){
        if($(e.target).closest('body').length > 0)
          $('.modal.active, .modal-sm.active').modal_close();
      }
    })

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('modal');

})();