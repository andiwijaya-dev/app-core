(function() {

  $.fn.extend({

    slider:function(){

      $(this).each(function(){

        var step = this.getAttribute('data-step');
        var has_step = parseInt(step) > 0 ? true : false;

        // rail
        var button_width = $('.slider-butt-1', this).outerWidth();
        $('.slider-rail', this).css({ left:button_width / 2, right:button_width  / 2 });

        var instance = this;
        $('.slider-butt-0, .slider-butt-1', this)
          .on('mousedown touchstart', function(e){

            var button = this;

            var ev = e.originalEvent;
            var x = typeof ev.touches != 'undefined' ? ev.touches[0].clientX : ev.clientX;
            $(this).data('drag-start-x', x);

            var max_distance = $(instance).outerWidth() - $(button).outerWidth();
            var step_distance = has_step ? max_distance / step : 0;

            if(has_step){
              var left = $(button)[0].offsetLeft;
              var current_step = Math.round(left / max_distance * step);
            }

            $(document.body)
              .on('mousemove touchmove', function(e){

                var ev = e.originalEvent;
                var x = typeof ev.touches != 'undefined' ? ev.touches[0].clientX : ev.clientX;
                var distance = x - parseFloat($(button).data('drag-start-x'));

                var left = $(button)[0].offsetLeft;

                if(has_step){

                  if(distance > 0){

                    var step_amount = Math.ceil(Math.abs(distance) / max_distance * step);
                    var next_step = current_step + step_amount > step ? step : current_step + step_amount;
                    left = next_step * step_distance;

                  }
                  else if(distance < 0){

                    var step_amount = Math.ceil(Math.abs(distance) / max_distance * step);
                    var next_step = current_step - step_amount < 0 ? 0 : current_step - step_amount;
                    left = next_step * step_distance;

                  }

                }
                else{

                  if(isNaN(left)) left = 0;
                  if(left < 0) left = 0;
                  var max_left = $(instance).outerWidth() - $(button).outerWidth();
                  left += distance;
                  if(left > max_left) left = max_left;
                  $(button).data('drag-start-x', x);

                }

                var value = null;
                if(has_step)
                  value = Math.round(left / max_distance * step);
                else
                  value = Math.round(left / max_distance);
                $.fire_event($(instance).attr('data-onchanging'), [ value ], instance);

                $(button)[0].style.left = left;
                $('.slider-rail-highlight', instance).css({ width:left });


              })
              .on('mouseup mouseleave touchend', function(e){

                e.preventDefault();
                $(document.body).off('mousemove touchmove mouseup mouseleave touchend');
                $(button).removeData('drag-start-x');

                // On change event
                var value = null;
                var left = $(button)[0].offsetLeft;
                var changed = false;
                if(has_step){
                  value = Math.round(left / max_distance * step);
                  changed = true;
                }
                else{
                  value = Math.round(left / max_distance);
                  changed = true;
                }
                if(changed){
                  $.fire_event($(instance).attr('data-onchange'), [ value ], instance);
                }

              });

          })

        $(this).data('init', 1);
        $.fire_event($(this).attr('data-oninit'), [], this);

      })

    },

    slider_reset:function(){

      $('.slider-butt-0').css({ left:0 });
      $('.slider-butt-1').css({ right:0 });
      $('.slider-rail-highlight').css({ left:0, right:0 });

    },

    slider_val:function(val){

      if(!$(this).hasClass('slider')) return;

      var left = $('.slider-butt-1', this)[0].offsetLeft;
      var step = $(this).attr('data-step');
      var has_step = parseInt(step) > 0 ? true : false;
      var max_distance = $(this).outerWidth() - $('.slider-butt-1', this).outerWidth();
      var step_distance = has_step ? max_distance / step : 0;

      if(typeof val == 'undefined'){

        return Math.round(left / max_distance * step);

      }

      else{

        left = Math.round(step_distance * val);
        $('.slider-butt-1', this)[0].style.left = left;
        $('.slider-rail-highlight', this).css({ width:left });

      }

    }

  })

  $.extend({

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('slider');

})();