(function() {

  $.fn.extend({

    progressbar_val:function(percentage, resetAfterMs){

      if(!$(this).hasClass('progressbar')) return;

      $(this).each(function(){

        var instance = this;

        $('span', this).css({ width:percentage + '%' });

        if(parseInt(resetAfterMs) > 0){

          window.setTimeout(function(){
            $(instance).progressbar_reset();
          }, parseInt(resetAfterMs));
        }

      })

    },

    progressbar_reset:function(){

      if(!$(this).hasClass('progressbar')) return;

      $(this).each(function(){

        var instance = this;

        $('span', this).addClass('hidden').css({ width:0 });

        window.setTimeout(function(){

          $('span', instance).removeClass('hidden');

        }, 1000)

      })

    }

  })

  $.extend({

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('progressbar');

})();