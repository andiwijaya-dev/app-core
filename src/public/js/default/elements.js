(function(){

  /* NUMBERFIELD */
  $.fn.extend({

    numberfield:function(options){

      options = typeof options != 'object' ? {} : options;

      $(this).each(function(){

        var id = $.uniqid();

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

        // Initialize inner element
        if(this.innerHTML.trim().length == 0){


        }

        $(this).addClass('numberfield');

        $("input[type='text']", this).on('change', function(){
          $.fire_event($.val('onchange', $(this).closest('.numberfield').data('options')), [], $(this).closest('.numberfield'));
        }).val(1);

        $('.min-btn', this).click(function(){
          var value = parseInt($('input', this.parentNode).val());
          var next_value = isNaN(value) ? 1 : value - 1;
          if(next_value < 1) next_value = 1;
          $('input', this.parentNode).val(next_value);

          $.fire_event($.val('onchange', $(this).closest('.numberfield').data('options')), [], $(this).closest('.numberfield'));
        })

        $('.plus-btn', this).click(function(){
          var value = parseInt($('input', this.parentNode).val());
          var next_value = isNaN(value) ? 1 : value + 1;
          $('input', this.parentNode).val(next_value);

          $.fire_event($.val('onchange', $(this).closest('.numberfield').data('options')), [], $(this).closest('.numberfield'));
        })

        $(this).data('id', id);
        $(this).data('options', options);

      })

    }

  })

  /* TEXTBOX */
  $.fn.extend({

    textbox:function(options){

      options_exp = JSON.stringify(typeof globalOptions != 'object' ? {} : globalOptions);

      $(this).each(function(){

        if($(this).attr('data-id') !== undefined) return;

        // Initialize
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

        // Autocomplete behaviour
        if(typeof options['src'] != 'undefined'){

          $('input', this).on('keyup', function(){

            if(this.value.length < 2) return;

            var instance = this.parentNode;
            var options = $(instance).data('options');

            var input = this;
            var key = this.value;
            window.setTimeout(function(){

              if(input.value == key){

                $.fire_event($.val('onbeforefetch', options), [ key ], instance);

                input.disabled = true;

                $.json(options['src'], {
                  data:{ search:key },
                  onsuccess:function(response){

                    if($.type(response) == 'object' && typeof response.data != 'undefined'){

                      var map = $.map_to_object($.val('map', options));

                      var html = [];
                      $(response.data).each(function(){

                        var text = $.val($.val('text', map, { d:'text' }), this);
                        var onclick = $.val($.val('onclick', map, { d:'onclick' }), this);

                        html.push("<div class='item'>" + text + "</div>");

                      })
                      $('.popup', instance).attr('data-caller-id', id).html(html.join('')).popup_open({ ref:instance });

                      $('.item', $('.popup[data-caller-id=' + id + ']')).click(function(){

                        var obj = response.data[$(this).index()];

                        $('.textbox[data-id=' + id + ']').val(this.innerText);
                        $(this).closest('.popup').popup_close();
                        $.fire_event($.val('onchange', options), [ obj ], instance);

                      })

                    }

                    input.disabled = false;
                  }
                })

              }

            }, 200)

          })

          $(this).append("<div class='popup' data-parent-id='" + id + "'></div>");

        }

        $(this).attr('data-id', id);
        $(this).data('options', options);

      })

    },

    textbox_reset:function(){

      $('input', this).val('');

    },

    textbox_select:function(){

      $('input', this).select();

    },

    textbox_val:function(value){

      if(typeof value == 'undefined')
        return $('input', this).val();
      else{

        $('input', this).val(value);

      }

    }

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('numberfield');
  __ux_types.push('textbox');

})();