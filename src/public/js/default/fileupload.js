(function() {

  $.fn.extend({

    fileupload:function(options){

      $(this).each(function(){

        var instance = this;

        $('button', this).attr('type', 'button').off('click').on('click', function(){
          $("input[type=file]", instance).click();
        });

        $('label', instance).data('html', $('label', instance).html());

        $("input[type=file]", instance).on('change', function(){

          if(this.files.length > 0){

            $('.icon-remove', instance).removeClass('hidden');
            $('label', instance).html(this.files[0].name);

          }

        })

        $('.icon-remove', this).addClass('hidden').on('click', function(e){
          e.preventDefault();
          e.stopPropagation();

          $("input[type=file]", instance).val('');
          $('label', instance).html($('label', instance).data('html'));
          $('.icon-remove', instance).addClass('hidden');

        });

      })

    },

    fileupload_validate:function(){

      console.log('1');

    }

  })

  $.extend({

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('fileupload');

})();