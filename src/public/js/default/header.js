(function(){

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;

  $(function(){

    $('.menu-drawer-overlay').click(function(){
      $('body').toggleClass('menu-drawer-active');
    })

    $('.header-bars-btn').click(function(){

      $(document.body).addClassThen('header-menu-on', 'header-menu-active');

    });

    $('.header-search-btn').click(function(e){

      e.preventDefault();
      e.stopPropagation();

      if($(document.body).hasClass('header-search-active'))
        $(document.body).removeClassThen('header-search-active', 'header-search-on');
      else
        $(document.body).addClassThen('header-search-on', 'header-search-active');

    })

    $('.header-overlay').click(function(){

      if($(document.body).hasClass('header-menu-active'))
        $(document.body).removeClassThen('header-menu-active', 'header-menu-on', true);

      if($(document.body).hasClass('header-search-active'))
        $(document.body).removeClassThen('header-search-active', 'header-search-on');

    });

  })

})();