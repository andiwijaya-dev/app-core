(function(){

  $.fn.extend({

    carousel:function(options){

      $(this).each(function(){

        // Scroll
        if($(this).hasClass('scrolled')){
          $(this).on('scroll', function(){ $.lazy_load(); })
        }
        else{

          this.firstElementChild.addEventListener('transitionend', function(){ $.lazy_load(); });

          this.addEventListener('touchstart', function(e){

            if(typeof $(this).data('drag-start-x') == 'undefined'){

              window.clearInterval($(this).data('id'));
              var matches = this.firstElementChild.style.transform.match(/translate3d\((.*?)(?=\,)/);
              var x_pos = matches != null && typeof matches[1] != 'undefined' ? matches[1].replace('%', '') : 0;
              x_pos = x_pos / 100 * this.firstElementChild.clientWidth;
              $(this).data('drag-start-x', x_pos);
              $(this).data('drag-start-transform', this.firstElementChild.style.transform);
              $(this).data('drag-start-time', (new Date()).getTime());
              $(this).data('drag-touch-start-x', e.touches[0].clientX);
              $(this).data('drag-touch-last-x', e.touches[0].clientX);
              this.firstElementChild.classList.add('no-animation');

            }

          }, { passive:true })
          this.addEventListener('touchmove', function(e){

            //e.preventDefault();
            if(typeof $(this).data('drag-start-x') != 'undefined'){
              var distance = e.touches[0].clientX - parseFloat($(this).data('drag-touch-start-x'));
              var start_x = parseFloat($(this).data('drag-start-x'));
              var next_x = (start_x + distance) + "px";
              this.firstElementChild.style.transform = "translate3d(" + next_x + ", 0, 0)";
              $(this).data('drag-touch-last-x', e.touches[0].clientX);
            }

          }, { passive:true })
          this.addEventListener('touchend', function(e){

            if(typeof $(this).data('drag-start-x') != 'undefined'){

              this.firstElementChild.classList.remove('no-animation');

              var duration = (new Date()).getTime() - parseFloat($(this).data('drag-start-time'));
              var distance = parseFloat($(this).data('drag-touch-last-x')) - parseFloat($(this).data('drag-touch-start-x'));

              if(Math.abs(distance) > 10){
                if(duration < 1200)
                  distance < 0 ? $(this).carousel_next() : $(this).carousel_prev();
                else
                  $(this).carousel_index(parseInt(this.getAttribute('data-index')));
              }
              else
                $(this).carousel_index(parseInt(this.getAttribute('data-index')));

              $(this).removeData('drag-start-x');
              $(this).removeData('drag-touch-last-x');

            }

          }, { passive:true })
          this.addEventListener('mousedown', function(e){

            if(typeof $(this).data('drag-start-x') == 'undefined'){

              window.clearInterval($(this).data('id'));
              var matches = this.firstElementChild.style.transform.match(/translate3d\((.*?)(?=\,)/);
              var x_pos = matches != null && typeof matches[1] != 'undefined' ? matches[1].replace('px', '') : 0;
              $(this).data('drag-start-x', x_pos);
              $(this).data('drag-start-transform', this.firstElementChild.style.transform);
              $(this).data('drag-start-time', (new Date()).getTime());
              $(this).data('drag-touch-start-x', e.clientX);
              $(this).data('drag-touch-last-x', e.clientX);
              this.firstElementChild.classList.add('no-animation');

            }

          })
          this.addEventListener('mousemove', function(e){

            if(typeof $(this).data('drag-start-x') != 'undefined'){
              e.preventDefault();
              var distance = e.clientX - parseFloat($(this).data('drag-touch-start-x'));
              var start_x = parseFloat($(this).data('drag-start-x'));
              var next_x = (start_x + distance) + "px";
              this.firstElementChild.style.transform = "translate3d(" + next_x + ", 0, 0)";
              $(this).data('drag-touch-last-x', e.clientX);
              $(this).data('dragged', 1);
            }

          })
          this.addEventListener('mouseup', function(e){
            if(typeof $(this).data('drag-start-x') != 'undefined'){

              this.firstElementChild.classList.remove('no-animation');

              var duration = (new Date()).getTime() - parseFloat($(this).data('drag-start-time'));
              var distance = parseFloat($(this).data('drag-touch-last-x')) - parseFloat($(this).data('drag-touch-start-x'));

              if(distance != 0){

                if(Math.abs(distance) > 10){
                  if(duration < 1200)
                    distance < 0 ? $(this).carousel_next() : $(this).carousel_prev();
                  else
                    $(this).carousel_index(parseInt(this.getAttribute('data-index')));
                }
                else
                  $(this).carousel_index(parseInt(this.getAttribute('data-index')));

              }

              $(this).removeData('drag-start-x');
              $(this).removeData('drag-touch-last-x');

            }
          })
          this.addEventListener('mouseout', function(e){

            if(typeof $(this).data('drag-start-x') != 'undefined'){

              this.firstElementChild.classList.remove('no-animation');

              var duration = (new Date()).getTime() - parseFloat($(this).data('drag-start-time'));
              var distance = parseFloat($(this).data('drag-touch-last-x')) - parseFloat($(this).data('drag-touch-start-x'));

              if(distance != 0){

                if(Math.abs(distance) > 10){
                  if(duration < 1200)
                    distance < 0 ? $(this).carousel_next() : $(this).carousel_prev();
                  else
                    $(this).carousel_index(parseInt(this.getAttribute('data-index')));
                }
                else
                  $(this).carousel_index(parseInt(this.getAttribute('data-index')));

              }

              $(this).removeData('drag-start-x');
              $(this).removeData('drag-touch-last-x');

            }

          })
          this.addEventListener('click', function(e){

            if(parseInt($(this).data('dragged')) == 1){
              e.preventDefault();
              $(this).removeData('dragged');
            }

          });
        }

        // Autoplay
        var autoplay = parseInt(this.getAttribute('data-autoplay'));
        autoplay = isNaN(autoplay) ? 0 : autoplay;
        if(this.classList.contains('animated') && autoplay == 0){
          autoplay = Math.round(Math.random() * 10000) + 5000;
          this.setAttribute('data-autoplay', autoplay);
        }

        $('.prev-btn, .next-btn', this).click(function(){
          this.classList.contains('prev-btn') ? $(this).closest('.carousel').carousel_prev() :
            $(this).closest('.carousel').carousel_next();
        })

        $.lazy_load();

      })

    },

    carousel_index:function(index){

      var el = $(this).attr('data-group');
      if(typeof el == 'undefined') el = this;
      else el = '.' + el;

      $(el).each(function(){

        var el = this;
        window.clearInterval($(el).data('id'));

        var iEl = el.firstElementChild;
        var clipWidth = iEl.clientWidth;
        var scrollWidth = iEl.scrollWidth;
        var count = Math.ceil(scrollWidth / clipWidth);

        if(index < 0) index = 0;
        if(index >= count) index = count - 1;

        var maxScroll = (scrollWidth - clipWidth) * -1;
        var transform_x = (index * (clipWidth * -1));
        if(transform_x < maxScroll) transform_x = maxScroll;
        transform_x +=  "px";
        var transform = 'translate3d(' + transform_x + ', 0, 0)';

        iEl.style.transform = transform;

        var autoplay = !isNaN(parseInt(this.getAttribute('data-autoplay'))) ? parseInt(this.getAttribute('data-autoplay')) : 0;
        if($('.img', el).length > 1 && autoplay > 999){
          var id = window.setTimeout(function(){ $(el).carousel_next(1); }, autoplay);
          $(el).data('id', id);
        }
        $(this).data('index', index);

        this.setAttribute('data-index', index);

      })

    },

    carousel_next:function(goBack){

      var el = $(this).attr('data-group');
      if(typeof el == 'undefined') el = this;
      else el = '.' + el;

      goBack = typeof goBack == 'undefined' ? false : goBack;

      $(el).each(function(){

        window.clearInterval($(this).data('id'));

        var iEl = this.firstElementChild;
        var clipWidth = iEl.clientWidth;
        var scrollWidth = iEl.scrollWidth;

        var index = parseInt(this.getAttribute('data-index'));
        if(isNaN(index)) index = 0;
        var count = Math.ceil(scrollWidth / clipWidth);

        var next_index = index + 1 >= count ? (goBack ? 0 : index) : index + 1;
        var maxScroll = (scrollWidth - clipWidth) * -1;
        var transform_x = (next_index * (clipWidth * -1));
        if(transform_x < maxScroll) transform_x = maxScroll;
        transform_x +=  "px";
        var transform = 'translate3d(' + transform_x + ', 0, 0)';

        iEl.style.transform = transform;

        var autoplay = !isNaN(parseInt(this.getAttribute('data-autoplay'))) ? parseInt(this.getAttribute('data-autoplay')) : 0;
        if($('.img', el).length > 1 && autoplay > 999){
          var id = window.setTimeout(function(){ $(el).carousel_next(1); }, autoplay);
          $(el).data('id', id);
        }

        this.setAttribute('data-index', next_index);

      })

    },

    carousel_prev:function(){

      var el = $(this).attr('data-group');
      if(typeof el == 'undefined') el = this;
      else el = '.' + el;

      $(el).each(function(){

        window.clearInterval($(this).data('id'));

        var iEl = this.firstElementChild;
        var column = parseInt($(this).attr('data-column'));
        if(isNaN(column)) column = 1;

        var index = parseInt(this.getAttribute('data-index'));
        if(isNaN(index)) index = 0;
        var count = Math.ceil(iEl.children.length / column);

        var next_index = index - 1 < 0 ? 0 : index - 1;
        var clipWidth = iEl.clientWidth;
        var scrollWidth = iEl.scrollWidth;
        var transform_x = (next_index * clipWidth * -1) + 'px';
        var transform = 'translate3d(' + transform_x + ', 0, 0)';

        iEl.style.transform = transform;

        var autoplay = !isNaN(parseInt(this.getAttribute('data-autoplay'))) ? parseInt(this.getAttribute('data-autoplay')) : 0;
        if($('.img', el).length > 1 && autoplay > 999){
          var id = window.setTimeout(function(){ $(el).carousel_next(1); }, autoplay);
          $(el).data('id', id);
        }

        this.setAttribute('data-index', next_index);

      })

    },

  });

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('carousel');

})();