(function(){

  $.fn.extend({

    tabs:function(){

      $(this).each(function(){

        $('.item', this).click(function(){
          $(this).closest('.tabs').tabs_index($(this).index());
        })
        
      })

    },

    tabs_index:function(index){

      $(this).each(function(){

        var cont = this.getAttribute('data-cont');
        if(cont != null){

          if(parseInt(this.getAttribute('data-no-resize')) != 1)
            $(cont).css({ 'min-height':$(cont).outerHeight() });

          $('.item', this).removeClass('active');
          $('.item:eq(' + index + ')', this).addClass('active');
          $(cont).each(function(){
            $(this).children().addClass('hidden');
            $(this).children().eq(index).removeClass('hidden');
          })

        }

      })

    }

  })

  $(function(){

    $('.tabs').each(function(){ $(this).tabs(); })

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('tabs');

})();

