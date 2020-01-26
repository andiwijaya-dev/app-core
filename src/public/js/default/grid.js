(function() {

  $.fn.extend({

    grid:function(options){

      options_exp = JSON.stringify(typeof globalOptions != 'object' ? {} : globalOptions);

      $(this).each(function(){

        var options = eval("(" + options_exp + ")");
        var id = options[id] = $.uniqid();

        var attr_length = this.attributes.length;
        var removed_attr = [];
        for(var i = 0 ; i < attr_length ; i++){
          var name = this.attributes[i].name;
          if(name.indexOf('data-') >= 0){
            name = name.substr(5);
            if(!$.in_array(name, [ 'type', 'name', 'store', 'parent-ctlid' ])){
              options[name] = this.attributes[i].value;
              removed_attr.push(this.attributes[i].name);
            }
          }
        }
        for(var i = 0 ; i < removed_attr.length ; i++)
          this.removeAttribute(removed_attr[i]);

        $('.resizer', this)
          .off('mousedown')
          .on('mousedown', function(e){

            var startX = e.clientX;
            var instance = this;
            var th = $(instance).closest('th');
            $(document.body).on('mousemove', function(e){
              var distance = e.clientX - startX;
              startX = e.clientX;
              th.css({ width:th.outerWidth() + distance });
            });
            $(document.body).on('mouseup mouseleave', function(){
              $(document.body).off('mousemove mouseup mouseleave');

              var grid = $(instance).closest('.grid');
              var options = $(grid).data('options');
              var column_idx = $(th).attr('data-column-idx');
              var width = $(th).outerWidth();
              $.fire_event($.val('onresize', options), [ column_idx + '=' + width ], grid);

            });

          });
        
        $(this).attr('data-id', id);
        $(this).data('options', options);

        $(this).grid_update();

      })

    },

    grid_val:function(arr){

      if(typeof arr == 'undefined'){

        var arr = [];

        $("tbody tr", this).each(function(){

          var obj = $(this).data('value');
          if($.type(obj) != 'object') obj = {};

          arr.push($(this).val());

        });

        return arr;

      }

      else{


      }

    },

    grid_update:function(){

      if($('.grid-foot', this).length == 0)
        $(this).append("<div class='grid-foot'></div>");

      if($('tbody>tr', this).length === 0){
        $('.grid-foot', this).html("<div class='pad-1 align-center'>Tidak ada data</div>");
      }
      else{
        $('.grid-foot', this).html('');
      }

    }

  })

  $.extend({

    grid_scroll_event:function(){

      $('.grid .grid-load-more').each(function(){

        if($.is_in_viewport(this)){
          this.click();
        }

      })

    }

  })

  $(function(){

    $(window).on('scroll', $.grid_scroll_event);

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('grid');

})();