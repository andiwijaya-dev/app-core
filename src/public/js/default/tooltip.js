(function() {

  $.fn.extend({

    tooltip_val:function(val, mode){

      mode = typeof mode == 'undefined' ? 1 : mode;

      $(this).each(function(){

        $(this).html(val);

        if(mode == 1) $(this).removeClass([ 'error', 'info' ]).addClass('info');
        else $(this).removeClass([ 'error', 'info' ]).addClass('error');

      })

    }

  })

  $.extend({

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('tooltip');

})();