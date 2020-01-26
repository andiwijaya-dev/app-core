(function() {

  $.fn.extend({

    datepicker:function(options){

      if(!$(this).hasClass('datepicker')) return;

      $(this).each(function(){

        if($(this).data('init') === '1') return;

        if($('.popup', this).length === 0){

          $(this).append(`<span class='popup datepicker-popup'>
          <div class="row valign-middle">
          
            <div class="col-9">
              <h4 class="month"></h4>
            </div>
            <div class="col-1"><span class="icon icon-today fa fa-calendar"></span></div>
            <div class="col-1"><span class="icon icon-prev fa fa-caret-left"></span></div>
            <div class="col-1"><span class="icon icon-next fa fa-caret-right"></span></div>
            
            <div class="col-12 vmart-2">
              <table>
                <thead>
                <tr>
                  <th><label>Min</label></th>
                  <th><label>Sen</label></th>
                  <th><label>Sel</label></th>
                  <th><label>Rab</label></th>
                  <th><label>Kam</label></th>
                  <th><label>Jum</label></th>
                  <th><label>Sab</label></th>
                </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            
          </div>
          </span>`);

        }

        $('.icon', this).click(function(){

          var d = $.date('Y-m-d', $.strtotime($('input', $(this).parent()).val()));
          if(d == '1970-01-01') d = $.date('Y-m-d');

          $.datepicker_popup($('.popup', $(this).parent()), d);

          $('.popup', $(this).parent()).popup_open({ ref:$(this).parent() });

        })

        $('input', this).blur(function(){

          var d = $.date('Y-m-d', $.strtotime(this.value));
          if(d === '1970-01-01')
            this.value = '';
          else
            this.value = d;

        })

        $(this).data('init', 1);

      })

    },

    datepicker_val:function(value){

      if(typeof value == 'undefined'){

        return $('input', this).val();

      }
      else{

        var d = $.date('Y-m-d', $.strtotime(value));
        if(d != '1970-01-01')
          $('input', this).val(d);

        if($(this).hasClass('invalid')) $(this).datepicker_validate();

      }

    },

    datepicker_validate:function(){

      var errors = [];

      $(this).each(function(){

        var valid = true;

        var validation = this.getAttribute('data-validation');
        if(validation == null){
          var options = $(this).data('options');
          validation = $.val('validation', options);
        }

        var error_message = this.getAttribute('data-error-message');
        var value = this.firstElementChild.value;
        var name = this.firstElementChild.name;
        var error = $.validate(value, validation, { name:name, error_message:error_message });
        if(error.error > 0){
          valid = false;
          errors.push(error);
        }

        valid ? $(this).removeClass('invalid') : $(this).addClass('invalid');

      })

      return errors;

    }

  })

  $.extend({

    datepicker_popup:function(popup, d){

      var year = parseInt($.date('Y', $.strtotime(d)));
      var month = parseInt($.date('n', $.strtotime(d)));
      var d1 = parseInt($.start_day_of_month(year, month));
      var d2 = parseInt($.last_date_of_month(year, month));

      var p = $.date('Y-m-d', $.strtotime(d) - (60 * 60 * 24 * 31));
      var py = parseInt($.date('Y', $.strtotime(p)));
      var pm = parseInt($.date('n', $.strtotime(p)));
      var p2 = parseInt($.last_date_of_month(py, pm));

      var n = $.date('Y-m-', $.strtotime(d) + (60 * 60 * 24 * 31));

      var dates = [];
      for(var date = p2 - d1 + 1 ; date <= p2 ; date++)
        dates.push($.date('Y-m', $.strtotime(p)) + '-' + date);
      for(var date = 1 ; date <= d2 ; date++)
        dates.push($.date('Y-m', $.strtotime(d)) + '-' + $.str_pad(date, 2, '0', 'STR_PAD_LEFT'));

      var html = [];
      var index = 0;
      var less = true;
      for(var i = 0 ; i < 6 ; i++){
        html.push("<tr>");
        for(var j = 0 ; j < 7 ; j++){

          if(index > dates.length - 1){
            var date = n + $.str_pad((index - dates.length + 1), 2, '0', 'STR_PAD_LEFT');
            html.push("<td class='less' data-value='" + date + "'>" + $.date('j', $.strtotime(date))  + "</td>");
          }
          else{
            if(dates[index].indexOf('-01') >= 0) less = false;

            if(less)
              html.push("<td class='less' data-value='" + dates[index] + "'>" + $.date('j', $.strtotime(dates[index]))  + "</td>");
            else
              html.push("<td data-value='" + dates[index] + "'>" + $.date('j', $.strtotime(dates[index]))  + "</td>");
          }

          index++;
        }
        html.push("</tr>");
      }
      $('tbody', popup).html(html.join(''));

      $('tbody td', popup).click(function(){
        var popup = $(this).closest('.popup');
        var ref = popup.data('ref');
        $(ref).datepicker_val(this.getAttribute('data-value'));
        popup.popup_close();
      });

      $('.month', popup).html($.date('M Y', $.strtotime(d)));

      $('.icon-prev', popup).off('click').on('click', function(){
        $.datepicker_popup($(this).closest('.popup'), p);
      })

      $('.icon-next', popup).off('click').on('click', function(){
        $.datepicker_popup($(this).closest('.popup'), n + '01');
      })

      $('.icon-today', popup).off('click').on('click', function(){
        var popup = $(this).closest('.popup');
        var ref = popup.data('ref');
        $('input', ref).val($.date('Y-m-d'));
        popup.popup_close();
      })

    }

  })

  $(function(){

  })

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;
  __ux_types.push('datepicker');

})();