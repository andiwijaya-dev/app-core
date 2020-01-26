(function(){

  __ux_types = typeof __ux_types == 'undefined' ? [] : __ux_types;

  $.fn.ux_init = function(options){

    $(this).each(function(){

      if(this.classList.contains('carousel'))
        $(this).carousel(options);
      else if(this.classList.contains('carousel'))
        $(this).carousel(options);
      else if(this.classList.contains('numberfield'))
        $(this).numberfield(options);

    })

  }

  $.fn.ux_type = function(){

    var classNames = $(this).attr('class').split(' ');
    var result = '';
    $(__ux_types).each(function(){
      if($.in_array(this, classNames)){
        result = this;
      }
    })
    return result;

  }

  var oldVal = $.fn.val;
  $.fn.val = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_val'] != 'undefined')
      return $(this)[type + '_val'].apply(this, arguments);
    else if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN', 'TR', 'TD', 'TABLE' ])){
      var obj = {};

      $("label[data-name]", this).each(function(){
        obj[this.getAttribute('data-name')] = $(this).html();
      });

      $("*[name]", this).each(function(){
        if(typeof this.value != 'undefined')
          obj[this.getAttribute('name')] = this.value;
      });

      return obj;
    }
    else if(typeof oldVal == 'function')
      return oldVal.apply(this, arguments);

  }

  var oldOpen = $.fn.open;
  $.fn.open = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_open'] != 'undefined')
      return $(this)[type + '_open'].apply(this, arguments);
    else if(typeof oldOpen == 'function')
      return oldOpen.apply(this, arguments);

  }

  var oldUpdate = $.fn.update;
  $.fn.update = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_update'] != 'undefined')
      return $(this)[type + '_update'].apply(this, arguments);
    else if(typeof oldUpdate == 'function')
      return oldUpdate.apply(this, arguments);

  }

  var oldIndex = $.fn.index;
  $.fn.index = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_index'] != 'undefined')
      return $(this)[type + '_index'].apply(this, arguments);
    else if(typeof oldIndex == 'function')
      return oldIndex.apply(this, arguments);

  }

  var oldClose = $.fn.close;
  $.fn.close = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_close'] != 'undefined')
      return $(this)[type + '_close'].apply(this, arguments);
    else if(typeof oldClose == 'function')
      return oldClose.apply(this, arguments);

  }

  var oldReset = $.fn.reset;
  $.fn.reset = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_reset'] != 'undefined')
      return $(this)[type + '_reset'].apply(this, arguments);
    else if(typeof oldReset == 'function')
      return oldReset.apply(this, arguments);

  }

  var oldSelect = $.fn.select;
  $.fn.select = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_select'] != 'undefined')
      return $(this)[type + '_select'].apply(this, arguments);
    else if(typeof oldSelect == 'function')
      return oldSelect.apply(this, arguments);

  }

  var oldToggle = $.fn.toggle;
  $.fn.toggle = function(value){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ]) &&
      (type = $(this).ux_type()) != '' &&
      typeof $(this)[type + '_toggle'] != 'undefined')
      return $(this)[type + '_toggle'].apply(this, arguments);
    else if(typeof oldToggle == 'function')
      return oldToggle.apply(this, arguments);

  }

  var oldFilter = $.fn.filter;
  $.fn.filter = function(key){

    if($.in_array($(this).prop('tagName'), [ 'DIV', 'SPAN' ])){

      key = key.toLowerCase();

      $(this).children().each(function(){

        if(this.classList.contains('radio-item')){
          $('label', this).html().toLowerCase().indexOf(key) >= 0 ? this.classList.remove('hidden') : this.classList.add('hidden');
        }

      })

    }
    else if(typeof oldFilter == 'function')
      return oldFilter.apply(this, arguments);

  }

  var oldParam = $.param;
  $.param = function(arr){

    if(arr instanceof FormData){
      var params = [];
      for(var pair of arr.entries())
        params.push(encodeURIComponent(pair[0])+ '='+ encodeURIComponent(pair[1]));
      return params.join('&');
    }
    return oldParam.apply(this, arguments);

  }

  $.fn.addClassThen = function(className, then, transitionend){

    $(this).addClass(className);
    var instance = this;
    if(typeof transitionend != 'undefined' && transitionend){
      $(instance).on('transitionend', function(){
        $(instance).addClass(then);
        $(instance).off('transitionend');
      })
    }
    else{
      window.setTimeout(function(){
        $(instance).addClass(then);
        $(instance).off('transitionend');
      }, 100);
    }

  }

  $.fn.removeClassThen = function(className, then, callback){

    var instance = this;
    if(window.getComputedStyle($(this)[0]).transition == 'all 0s ease 0s'){
      window.setTimeout(function(){
        $(instance).removeClass(then);

        $(this).off('transitionend');

        $.fire_event(callback);
      }, 100);
    }
    else{
      $(instance).on('transitionend', function(){
        $(instance).removeClass(then);

        $(this).off('transitionend');

        $.fire_event(callback);
      })
    }
    $(this).removeClass(className);

  }

  $.fn.backgroundFromFile = function(file){

    if (FileReader) {
      var instance = this;
      var fr = new FileReader();
      fr.onload = function (e) {
        $(instance).css({ 'background-image':"url('" + fr.result + "')" });
      }
      fr.readAsDataURL(file);
    }

  }

  /* UTIL */
  $.extend({

    date:function(format, timestamp){

      var that = this;
      var jsdate, f;
      // Keep this here (works, but for code commented-out below for file size reasons)
      // var tal= [];
      var txt_words = [
        'Sun', 'Mon', 'Tues', 'Wednes', 'Thurs', 'Fri', 'Satur',
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      // trailing backslash -> (dropped)
      // a backslash followed by any character (including backslash) -> the character
      // empty string -> empty string
      var formatChr = /\\?(.?)/gi;
      var formatChrCb = function(t, s) {
        return f[t] ? f[t]() : s;
      };
      var _pad = function(n, c) {
        n = String(n);
        while (n.length < c) {
          n = '0' + n;
        }
        return n;
      };
      f = {
        // Day
        d: function() { // Day of month w/leading 0; 01..31
          return _pad(f.j(), 2);
        },
        D: function() { // Shorthand day name; Mon...Sun
          return f.l()
            .slice(0, 3);
        },
        j: function() { // Day of month; 1..31
          return jsdate.getDate();
        },
        l: function() { // Full day name; Monday...Sunday
          return txt_words[f.w()] + 'day';
        },
        N: function() { // ISO-8601 day of week; 1[Mon]..7[Sun]
          return f.w() || 7;
        },
        S: function() { // Ordinal suffix for day of month; st, nd, rd, th
          var j = f.j();
          var i = j % 10;
          if (i <= 3 && parseInt((j % 100) / 10, 10) == 1) {
            i = 0;
          }
          return ['st', 'nd', 'rd'][i - 1] || 'th';
        },
        w: function() { // Day of week; 0[Sun]..6[Sat]
          return jsdate.getDay();
        },
        z: function() { // Day of year; 0..365
          var a = new Date(f.Y(), f.n() - 1, f.j());
          var b = new Date(f.Y(), 0, 1);
          return Math.round((a - b) / 864e5);
        },

        // Week
        W: function() { // ISO-8601 week number
          var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
          var b = new Date(a.getFullYear(), 0, 4);
          return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
        },

        // Month
        F: function() { // Full month name; January...December
          return txt_words[6 + f.n()];
        },
        m: function() { // Month w/leading 0; 01...12
          return _pad(f.n(), 2);
        },
        M: function() { // Shorthand month name; Jan...Dec
          return f.F()
            .slice(0, 3);
        },
        n: function() { // Month; 1...12
          return jsdate.getMonth() + 1;
        },
        t: function() { // Days in month; 28...31
          return (new Date(f.Y(), f.n(), 0))
            .getDate();
        },

        // Year
        L: function() { // Is leap year?; 0 or 1
          var j = f.Y();
          return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
        },
        o: function() { // ISO-8601 year
          var n = f.n();
          var W = f.W();
          var Y = f.Y();
          return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
        },
        Y: function() { // Full year; e.g. 1980...2010
          return jsdate.getFullYear();
        },
        y: function() { // Last two digits of year; 00...99
          return f.Y()
            .toString()
            .slice(-2);
        },

        // Time
        a: function() { // am or pm
          return jsdate.getHours() > 11 ? 'pm' : 'am';
        },
        A: function() { // AM or PM
          return f.a()
            .toUpperCase();
        },
        B: function() { // Swatch Internet time; 000..999
          var H = jsdate.getUTCHours() * 36e2;
          // Hours
          var i = jsdate.getUTCMinutes() * 60;
          // Minutes
          var s = jsdate.getUTCSeconds(); // Seconds
          return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
        },
        g: function() { // 12-Hours; 1..12
          return f.G() % 12 || 12;
        },
        G: function() { // 24-Hours; 0..23
          return jsdate.getHours();
        },
        h: function() { // 12-Hours w/leading 0; 01..12
          return _pad(f.g(), 2);
        },
        H: function() { // 24-Hours w/leading 0; 00..23
          return _pad(f.G(), 2);
        },
        i: function() { // Minutes w/leading 0; 00..59
          return _pad(jsdate.getMinutes(), 2);
        },
        s: function() { // Seconds w/leading 0; 00..59
          return _pad(jsdate.getSeconds(), 2);
        },
        u: function() { // Microseconds; 000000-999000
          return _pad(jsdate.getMilliseconds() * 1000, 6);
        },

        // Timezone
        e: function() { // Timezone identifier; e.g. Atlantic/Azores, ...
          // The following works, but requires inclusion of the very large
          // timezone_abbreviations_list() function.
          /*              return that.date_default_timezone_get();
           */
          throw 'Not supported (see source code of date() for timezone on how to add support)';
        },
        I: function() { // DST observed?; 0 or 1
          // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
          // If they are not equal, then DST is observed.
          var a = new Date(f.Y(), 0);
          // Jan 1
          var c = Date.UTC(f.Y(), 0);
          // Jan 1 UTC
          var b = new Date(f.Y(), 6);
          // Jul 1
          var d = Date.UTC(f.Y(), 6); // Jul 1 UTC
          return ((a - c) !== (b - d)) ? 1 : 0;
        },
        O: function() { // Difference to GMT in hour format; e.g. +0200
          var tzo = jsdate.getTimezoneOffset();
          var a = Math.abs(tzo);
          return (tzo > 0 ? '-' : '+') + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
        },
        P: function() { // Difference to GMT w/colon; e.g. +02:00
          var O = f.O();
          return (O.substr(0, 3) + ':' + O.substr(3, 2));
        },
        T: function() { // Timezone abbreviation; e.g. EST, MDT, ...
          // The following works, but requires inclusion of the very
          // large timezone_abbreviations_list() function.
          /*              var abbr, i, os, _default;
           if (!tal.length) {
           tal = that.timezone_abbreviations_list();
           }
           if (that.php_js && that.php_js.default_timezone) {
           _default = that.php_js.default_timezone;
           for (abbr in tal) {
           for (i = 0; i < tal[abbr].length; i++) {
           if (tal[abbr][i].timezone_id === _default) {
           return abbr.toUpperCase();
           }
           }
           }
           }
           for (abbr in tal) {
           for (i = 0; i < tal[abbr].length; i++) {
           os = -jsdate.getTimezoneOffset() * 60;
           if (tal[abbr][i].offset === os) {
           return abbr.toUpperCase();
           }
           }
           }
           */
          return 'UTC';
        },
        Z: function() { // Timezone offset in seconds (-43200...50400)
          return -jsdate.getTimezoneOffset() * 60;
        },

        // Full Date/Time
        c: function() { // ISO-8601 date.
          return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
        },
        r: function() { // RFC 2822
          return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
        },
        U: function() { // Seconds since UNIX epoch
          return jsdate / 1000 | 0;
        }
      };
      this.date = function(format, timestamp) {
        that = this;
        jsdate = (timestamp === undefined ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
              new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
        );
        return format.replace(formatChr, formatChrCb);
      };
      return this.date(format, timestamp);

    },
    mktime:function() {
      //  discuss at: http://phpjs.org/functions/mktime/
      // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // improved by: baris ozdil
      // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // improved by: FGFEmperor
      // improved by: Brett Zamir (http://brett-zamir.me)
      //    input by: gabriel paderni
      //    input by: Yannoo
      //    input by: jakes
      //    input by: 3D-GRAF
      //    input by: Chris
      // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // bugfixed by: Marc Palau
      // bugfixed by: Brett Zamir (http://brett-zamir.me)
      //  revised by: Theriault
      //        note: The return values of the following examples are
      //        note: received only if your system's timezone is UTC.
      //   example 1: mktime(14, 10, 2, 2, 1, 2008);
      //   returns 1: 1201875002
      //   example 2: mktime(0, 0, 0, 0, 1, 2008);
      //   returns 2: 1196467200
      //   example 3: make = mktime();
      //   example 3: td = new Date();
      //   example 3: real = Math.floor(td.getTime() / 1000);
      //   example 3: diff = (real - make);
      //   example 3: diff < 5
      //   returns 3: true
      //   example 4: mktime(0, 0, 0, 13, 1, 1997)
      //   returns 4: 883612800
      //   example 5: mktime(0, 0, 0, 1, 1, 1998)
      //   returns 5: 883612800
      //   example 6: mktime(0, 0, 0, 1, 1, 98)
      //   returns 6: 883612800
      //   example 7: mktime(23, 59, 59, 13, 0, 2010)
      //   returns 7: 1293839999
      //   example 8: mktime(0, 0, -1, 1, 1, 1970)
      //   returns 8: -1

      var d = new Date(),
        r = arguments,
        i = 0,
        e = ['Hours', 'Minutes', 'Seconds', 'Month', 'Date', 'FullYear'];

      for (i = 0; i < e.length; i++) {
        if (typeof r[i] === 'undefined') {
          r[i] = d['get' + e[i]]();
          r[i] += (i === 3); // +1 to fix JS months.
        } else {
          r[i] = parseInt(r[i], 10);
          if (isNaN(r[i])) {
            return false;
          }
        }
      }

      // Map years 0-69 to 2000-2069 and years 70-100 to 1970-2000.
      r[5] += (r[5] >= 0 ? (r[5] <= 69 ? 2e3 : (r[5] <= 100 ? 1900 : 0)) : 0);

      // Set year, month (-1 to fix JS months), and date.
      // !This must come before the call to setHours!
      d.setFullYear(r[5], r[3] - 1, r[4]);

      // Set hours, minutes, and seconds.
      d.setHours(r[0], r[1], r[2]);

      // Divide milliseconds by 1000 to return seconds and drop decimal.
      // Add 1 second if negative or it'll be off from PHP by 1 second.
      return (d.getTime() / 1e3 >> 0) - (d.getTime() < 0);
    },
    strtotime:function(text, now) {
      //  discuss at: http://phpjs.org/functions/strtotime/
      //     version: 1109.2016
      // original by: Caio Ariede (http://caioariede.com)
      // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // improved by: Caio Ariede (http://caioariede.com)
      // improved by: A. Matías Quezada (http://amatiasq.com)
      // improved by: preuter
      // improved by: Brett Zamir (http://brett-zamir.me)
      // improved by: Mirko Faber
      //    input by: David
      // bugfixed by: Wagner B. Soares
      // bugfixed by: Artur Tchernychev
      //        note: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
      //   example 1: strtotime('+1 day', 1129633200);
      //   returns 1: 1129719600
      //   example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
      //   returns 2: 1130425202
      //   example 3: strtotime('last month', 1129633200);
      //   returns 3: 1127041200
      //   example 4: strtotime('2009-05-04 08:30:00 GMT');
      //   returns 4: 1241425800

      var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;

      if (!text) {
        return fail;
      }


      // Accept YYYYMMDD format
      if(/^\d{8}$/.test(text)){
        text = text.substr(0, 4) + "-" + text.substr(4, 2) + "-" + text.substr(6, 2);
      }

      // Unecessary spaces
      text = text.replace(/^\s+|\s+$/g, '')
        .replace(/\s{2,}/g, ' ')
        .replace(/[\t\r\n]/g, '')
        .toLowerCase();

      // in contrast to php, js Date.parse function interprets:
      // dates given as yyyy-mm-dd as in timezone: UTC,
      // dates with "." or "-" as MDY instead of DMY
      // dates with two-digit years differently
      // etc...etc...
      // ...therefore we manually parse lots of common date formats
      match = text.match(
        /^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);

      if (match && match[2] === match[4]) {
        if (match[1] > 1901) {
          switch (match[2]) {
            case '-':
            { // YYYY-M-D
              if (match[3] > 12 || match[5] > 31) {
                return fail;
              }

              return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
            case '.':
            { // YYYY.M.D is not parsed by strtotime()
              return fail;
            }
            case '/':
            { // YYYY/M/D
              if (match[3] > 12 || match[5] > 31) {
                return fail;
              }

              return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
          }
        } else if (match[5] > 1901) {
          switch (match[2]) {
            case '-':
            { // D-M-YYYY
              if (match[3] > 12 || match[1] > 31) {
                return fail;
              }

              return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
            case '.':
            { // D.M.YYYY
              if (match[3] > 12 || match[1] > 31) {
                return fail;
              }

              return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
            case '/':
            { // M/D/YYYY
              if (match[1] > 12 || match[3] > 31) {
                return fail;
              }

              return new Date(match[5], parseInt(match[1], 10) - 1, match[3],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
          }
        } else {
          switch (match[2]) {
            case '-':
            { // YY-M-D
              if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
                return fail;
              }

              year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
              return new Date(year, parseInt(match[3], 10) - 1, match[5],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
            case '.':
            { // D.M.YY or H.MM.SS
              if (match[5] >= 70) { // D.M.YY
                if (match[3] > 12 || match[1] > 31) {
                  return fail;
                }

                return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                  match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
              }
              if (match[5] < 60 && !match[6]) { // H.MM.SS
                if (match[1] > 23 || match[3] > 59) {
                  return fail;
                }

                today = new Date();
                return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
                  match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000;
              }

              return fail; // invalid format, cannot be parsed
            }
            case '/':
            { // M/D/YY
              if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
                return fail;
              }

              year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
              return new Date(year, parseInt(match[1], 10) - 1, match[3],
                match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
            }
            case ':':
            { // HH:MM:SS
              if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
                return fail;
              }

              today = new Date();
              return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
                match[1] || 0, match[3] || 0, match[5] || 0) / 1000;
            }
          }
        }
      }

      // other formats and "now" should be parsed by Date.parse()
      if (text === 'now') {
        return now === null || isNaN(now) ? new Date()
          .getTime() / 1000 | 0 : now | 0;
      }
      if (!isNaN(parsed = Date.parse(text))) {
        return parsed / 1000 | 0;
      }

      date = now ? new Date(now * 1000) : new Date();
      days = {
        'sun': 0,
        'mon': 1,
        'tue': 2,
        'wed': 3,
        'thu': 4,
        'fri': 5,
        'sat': 6
      };
      ranges = {
        'yea': 'FullYear',
        'mon': 'Month',
        'day': 'Date',
        'hou': 'Hours',
        'min': 'Minutes',
        'sec': 'Seconds'
      };

      function lastNext(type, range, modifier) {
        var diff, day = days[range];

        if (typeof day !== 'undefined') {
          diff = day - date.getDay();

          if (diff === 0) {
            diff = 7 * modifier;
          } else if (diff > 0 && type === 'last') {
            diff -= 7;
          } else if (diff < 0 && type === 'next') {
            diff += 7;
          }

          date.setDate(date.getDate() + diff);
        }
      }

      function process(val) {
        var splt = val.split(' '), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
          type = splt[0],
          range = splt[1].substring(0, 3),
          typeIsNumber = /\d+/.test(type),
          ago = splt[2] === 'ago',
          num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

        if (typeIsNumber) {
          num *= parseInt(type, 10);
        }

        if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
          return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
        }

        if (range === 'wee') {
          return date.setDate(date.getDate() + (num * 7));
        }

        if (type === 'next' || type === 'last') {
          lastNext(type, range, num);
        } else if (!typeIsNumber) {
          return false;
        }

        return true;
      }

      times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
        '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
        '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
      regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

      match = text.match(new RegExp(regex, 'gi'));
      if (!match) {
        return fail;
      }

      for (i = 0, len = match.length; i < len; i++) {
        if (!process(match[i])) {
          return fail;
        }
      }

      // ECMAScript 5 only
      // if (!match.every(process))
      //    return false;

      return (date.getTime() / 1000);
    },
    start_day_of_month:function(year, month){

      var N = $.date('N', $.mktime(0, 0, 0, month, 1, year));
      return N;
    },
    last_date_of_month:function(year, month){

      var d = 27;
      do{
        d++;
        var j = parseInt($.date('j', $.mktime(0, 0, 0, month, d, year)));

        if(j != d){
          d--;
          break;
        }
      }
      while(true);
      return d;
    },

    copyToClipboard:function(el){

      var oldContentEditable = el.contentEditable,
        oldReadOnly = el.readOnly,
        range = document.createRange();

      el.contentEditable = true;
      el.readOnly = false;
      range.selectNodeContents(el);

      var s = window.getSelection();
      s.removeAllRanges();
      s.addRange(range);

      el.setSelectionRange(0, 999999);

      el.contentEditable = oldContentEditable;
      el.readOnly = oldReadOnly;

      el.select();

      document.execCommand('copy');

      s.removeAllRanges();

    },

    in_array:function(needle, haystack, argStrict){

      var key = ''
      var strict = !!argStrict
      // we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] === ndl)
      // in just one for, in order to improve the performance
      // deciding wich type of comparation will do before walk array
      if (strict) {
        for (key in haystack) {
          if (haystack[key] === needle) {
            return true;
          }
        }
      } else {
        for (key in haystack) {
          if (haystack[key] == needle) { // eslint-disable-line eqeqeq
            return true;
          }
        }
      }
      return false;

    },
    str_pad:function(input, padLength, padString, padType){

      var half = ''
      var padToGo
      var _strPadRepeater = function (s, len) {
        var collect = ''
        while (collect.length < len) {
          collect += s
        }
        collect = collect.substr(0, len)
        return collect
      }
      input += ''
      padString = padString !== undefined ? padString : ' '
      if (padType !== 'STR_PAD_LEFT' && padType !== 'STR_PAD_RIGHT' && padType !== 'STR_PAD_BOTH') {
        padType = 'STR_PAD_RIGHT'
      }
      if ((padToGo = padLength - input.length) > 0) {
        if (padType === 'STR_PAD_LEFT') {
          input = _strPadRepeater(padString, padToGo) + input
        } else if (padType === 'STR_PAD_RIGHT') {
          input = input + _strPadRepeater(padString, padToGo)
        } else if (padType === 'STR_PAD_BOTH') {
          half = _strPadRepeater(padString, Math.ceil(padToGo / 2))
          input = half + input + half
          input = input.substr(0, padLength)
        }
      }
      return input;

    },
    urlencode:function urlencode (str) {
      str = (str + '')

      return encodeURIComponent(str)
        .replace(/!/g, '%21')
        .replace(/'/g, '%27')
        .replace(/\(/g, '%28')
        .replace(/\)/g, '%29')
        .replace(/\*/g, '%2A')
        .replace(/~/g, '%7E')
        .replace(/%20/g, '+')
    },

    array_merge:function(){

      var args = Array.prototype.slice.call(arguments)
      var argl = args.length
      var arg
      var retObj = {}
      var k = ''
      var argil = 0
      var j = 0
      var i = 0
      var ct = 0
      var toStr = Object.prototype.toString
      var retArr = true

      for (i = 0; i < argl; i++) {
        if (toStr.call(args[i]) !== '[object Array]') {
          retArr = false
          break
        }
      }

      if (retArr) {
        retArr = []
        for (i = 0; i < argl; i++) {
          retArr = retArr.concat(args[i])
        }
        return retArr
      }

      for (i = 0, ct = 0; i < argl; i++) {
        arg = args[i]
        if (toStr.call(arg) === '[object Array]') {
          for (j = 0, argil = arg.length; j < argil; j++) {
            retObj[ct++] = arg[j]
          }
        } else {
          for (k in arg) {
            if (arg.hasOwnProperty(k)) {
              if (parseInt(k, 10) + '' === k) {
                retObj[ct++] = arg[k]
              } else {
                retObj[k] = arg[k]
              }
            }
          }
        }
      }

      return retObj;

    },

    uniqid:function(){

      if(typeof $.__UNIQID == 'undefined') $.__UNIQID = 0;
      return ++$.__UNIQID;

    },
    
    error:function(message){
      console.error(message);
    },

    val:function(key, obj, options){

      if(typeof options == 'undefined' || $.type(options) != 'object') options = {};
      if(typeof key == 'undefined' && typeof obj == 'undefined') return null;

      var value = null;
      var default_value = typeof options['default_value'] != 'undefined' ? options['default_value'] : (typeof options['d'] != 'undefined' ? options['d'] : null)
      var datatype = typeof options['datatype'] != 'undefined' ? options['datatype'] : '';

      if($.type(obj) == 'array' && obj.length > 0 && $.type(obj[0]) == 'object'){

        for(var i = 0 ; i < obj.length ; i++){
          var o = obj[i];
          var oval = $.val(key, o, { d:null });
          if(oval != null){ value = oval; break; }
        }
        value = value == null && default_value != null ? default_value : value;

      }
      else if($.type(obj) == 'object' || $.type(obj) == 'array'){

        var datatype = typeof options['t'] != 'undefined' ? options['t'] : (typeof options['datatype'] != 'undefined' ? options['datatype'] : 'string');
        var required = typeof options['required'] != 'undefined' && required == 1 ? true : false;

        if($.type(obj) == 'object'){

          if($.type(key) == 'array'){

            value = null;
            for(var i = 0 ; i < key.length ; i++){

              var k = key[i];
              v = $.val(k, obj, { default_value:null });
              if(v != null){
                value = v;
                break;
              }

            }

          }
          else if($.type(key) == 'string' || $.type(key) == 'number'){

            if(typeof obj[key] != 'undefined')
              value = obj[key];

          }

        }
        else if($.type(obj) == 'array'){

          if(typeof obj[key] != 'undefined')
            value = obj[key];

        }

        if(required){

          if(value == null){

          }

        }
        else{

          value = value == null && default_value != null ? default_value : value;

        }

        switch(datatype){

          case 'number':
          case 'integer':
          case 'float':
          case 'double':
            value = parseFloat(value);
            break;

        }

      }
      else{
        value = default_value;
      }

      switch(datatype){
        case 'bool':
        case 'boolean':
          value = value == 1 || value == true ? 1 : 0;
          break;
      }

      return value;

    },

    fire_event:function(callback, params, thisArg){

      if($.type(callback) == 'object'){

        var result = null;
        for(var key in callback)
          result = $.fire_event(callback[key], params, thisArg);
        return result;

      }

      else if($.type(callback) == 'array'){

        if(callback.length == 2 && typeof($(callback[0])[callback[1]]) == 'function'){

          $(callback[0])[callback[1]](params);

        }
        else{

          var result = null;
          for(var i = 0 ; i < callback.length ; i++)
            result = $.fire_event(callback[i], params, thisArg);
          return result;

        }

      }

      else{

        if(typeof thisArg == 'undefined') thisArg = null; // Parameter 3 is optional, default: null

        if($.type(callback) == 'string'){

          if(callback.match(/^([\.]*[\w\-]+)\.(\w+)$/)){
            return $.fire_event_string(callback);
          }
          else {
            callback = new Function('e', 'obj', callback);
          }

        }

        if($.type(callback) == 'function')
          return callback.apply(thisArg, params);

      }

    },

    is_in_viewport:function(el){

      var is_in_viewport = false;
      $(el).each(function(){

        var rect = this.getBoundingClientRect();

        var rel_top = rect.top < 0 ? 0 : rect.top;
        var rel_bottom = rect.bottom > window.innerHeight ? window.innerHeight : rect.bottom;
        var rel_left = rect.left < 0 ? 0 : rect.left;
        var rel_right = rect.right > window.innerWidth ? window.innerWidth : rect.right;

        is_in_viewport = (
          rect.width > 0
          && rect.height > 0
          && (rel_right - rel_left > 5)
          && (rel_bottom - rel_top > 5)
        );

        if($(this).hasClass('debug'))
          console.log({
            is_in_viewport:is_in_viewport,
            width:rect.width,
            height:rect.height,
            width_01:window.innerWidth * .1,
            height_01:window.innerHeight * .1,
            rel_top:rel_top,
            rel_bottom:rel_bottom,
            rel_left:rel_left,
            rel_right:rel_right,
            v1:rect.width > 0,
            v2:rect.height > 0,
            v3:(rel_right - rel_left > window.innerWidth * .1),
            v4:(rel_bottom - rel_top > window.innerHeight * .1),
          })

        //console.log([ this, rel_top, rel_bottom, window.innerHeight, is_in_viewport ]);

      });

      return is_in_viewport;

    },

    round:function(value, precision, mode) {

      var m, f, isHalf, sgn
      precision |= 0
      m = Math.pow(10, precision)
      value *= m
      sgn = (value > 0) | -(value < 0)
      isHalf = value % 1 === 0.5 * sgn
      f = Math.floor(value)

      if (isHalf) {
        switch (mode) {
          case 'PHP_ROUND_HALF_DOWN':
            value = f + (sgn < 0)
            break
          case 'PHP_ROUND_HALF_EVEN':
            value = f + (f % 2 * sgn)
            break
          case 'PHP_ROUND_HALF_ODD':
            value = f + !(f % 2)
            break
          default:
            value = f + (sgn > 0)
        }
      }

      return (isHalf ? value : Math.round(value)) / m

    },
    number_format:function number_format (number, decimals, decPoint, thousandsSep) {

      number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
      var n = !isFinite(+number) ? 0 : +number
      var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
      var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
      var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
      var s = '';

      var toFixedFix = function (n, prec) {
        if (('' + n).indexOf('e') === -1) {
          return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
        } else {
          var arr = ('' + n).split('e')
          var sig = ''
          if (+arr[1] + prec > 0) {
            sig = '+'
          }
          return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
        }
      }

      // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
      }

      return s.join(dec)

    },
    floatval:function(mixedVar){

      return (parseFloat(mixedVar) || 0);

    },

    lazy_load:function(event){

      $('.img.unloaded').each(function(){

        var el = this;

        if($.is_in_viewport(el)){

          var src = el.getAttribute("data-src");
          var img = new Image();
          img.onload = function(){
            el.style.background = "url('" + this.src + "') no-repeat center";
            el.classList.remove('loading');
            el.classList.add('loaded');
            el.innerHTML = $(el).data('html');
          }
          img.onerror = function(){
            el.style.background = "#eee";
            el.classList.remove('loading');
            el.classList.add('loaded');
            el.innerHTML =  $(el).data('html');
          }
          img.src = src;
          $(el).data('html', el.innerHTML);
          $(el).removeClass('unloaded').addClass('loading');
          $(el).html("<span class='loader'><span></span></span>");

        }

      })

    },

    calc_size:function(size){

      if(size != null){
        var div = document.createElement('div');
        document.body.appendChild(div);
        div.style.height = 1;
        div.style.width = size;
        return div.getBoundingClientRect().width;
      }
      return 0;

    },

    ux_init:function(cont){

      cont = typeof cont == 'undefined' || $(cont).length <= 0 ? document.body : cont;

      $('form.async', cont).on('submit', function(e){

        e.preventDefault();
        e.returnValue = false;

        var instance = this;
        var skip_validation = this.getAttribute('data-skip-validation-on-action');
        skip_validation = (skip_validation == null ? '' : skip_validation).split(',');

        var data = new FormData(this);
        var action = data.get('action');

        var action_skipped = false;
        for(var i in skip_validation){
          var skip_action = skip_validation[i];
          if(skip_action == action) action_skipped = true;
          else if(skip_action.indexOf('*') >= 0){
            if(action.indexOf(skip_action.split('*')[0]) >= 0)
              action_skipped = true;
          }
        }

        var error_cont = this.getAttribute('data-error-cont');

        if(!action_skipped){
          var errors = $(this).validate();
          var on_error = this.getAttribute('data-onerror');
          if(errors.length > 0){
            if(error_cont != null){
              var html = [];
              $(errors).each(function(){ html.push(this.message); });
              $(error_cont).html("<p>" + html.join("<br />") + "</p>").addClass('error').on('click.dismiss', function(){
                $(this).removeClass('error').html('');
              }).each(function(){
                //this.scrollIntoView();
              });
            }

            $.fire_event(on_error, [ errors ], this);

            return;
          }
        }

        var method = this.method;
        var action = this.getAttribute('action') == null ? '' : this.getAttribute('action');
        var onerror = this.getAttribute('data-onerror');
        var onsuccess = this.getAttribute('data-onsuccess');
        var url = method == 'get' ? action + '?' + $.param(data) : action;
        var upload_max_percentage = parseInt(this.getAttribute('data-progress-max-percentage'));
        if(isNaN(upload_max_percentage)) upload_max_percentage = 100;
        var upload_cont = this.getAttribute('data-progress-cont');

        $('button', instance).attr('disabled', true);

        $.ajax({
          headers:{
            Accept:'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
          },
          method:method,
          url:url,
          data:data,
          cache:false,
          processData:false,
          contentType:false,
          beforeSend: function (jqXHR, settings) {
            var self = this;
            var xhr = settings.xhr;
            settings.xhr = function () {
              var output = xhr();
              output.onreadystatechange = function () {
                if (typeof(self.readyStateChanged) == "function") {
                  self.readyStateChanged(this);
                }
              };
              this.responseIndex = 0;
              this.responsePrevIndex = -1;
              return output;
            };
          },
          readyStateChanged: function (xhr) {

            if(this.responseIndex > this.responsePrevIndex){
              var text = xhr.responseText.substr(this.responseIndex);
              if(text.length > 2){
                try{
                  data = JSON.parse(text);
                  this.responsePrevIndex = this.responseIndex;
                  this.responseIndex += text.length;

                  $.process_xhr_response(data);
                  $.lazy_load();
                }
                catch(e){}
              }
            }

          },
          success:function(data, status, xhr){

            $('button', instance).attr('disabled', false);

            if(this.responseIndex > 0) data = {};

            if(this.responseIndex > this.responsePrevIndex) {
              var text = xhr.responseText.substr(this.responseIndex);
              if(text.length > 2){
                try{
                  data = JSON.parse(text);
                }
                catch(e){
                }
              }
            }

            if(typeof data['error'] != 'undefined' && parseInt(data['error']) == 1){
              onerror == null ? $.alert(data) : $.fire_event(onerror, [ data ], instance);
              error_cont != null ? $(error_cont).html($.val('message', data, { d:'Terjadi kesalahan, silakan mencoba kembali' })).addClass('error') : '';
            }
            else{
              $.process_xhr_response(data);
              $.lazy_load();
              $.fire_event(onsuccess, [ data ], instance);
            }

          },
          error:function(xhr, status){

            $('button', instance).attr('disabled', false);

            try{
              response = eval("(" + xhr.responseText + ")");
            }
            catch(e){
              response = { error:1, text:"General error" };
            }

            if(typeof xhr.responseText == 'undefined' && status == 'error') response = { error:1, text:"Tidak dapat terhubung ke server." };

            if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){
              onerror == null ? $.alert(response) : $.fire_event(onerror, [ response ], instance);
              error_cont != null ? $(error_cont).html($.val('message', response, { d:'Terjadi kesalahan, silakan mencoba kembali' })).addClass('error') : '';
            }
            else if(typeof response['message'] != 'undefined'){
              if(error_cont != null){
                $(error_cont).html("<p>" + $.val('message', response, { d:'Terjadi kesalahan, silakan mencoba kembali' }) + "</p>").addClass('error');
                $(error_cont).on('click.dismiss', function(){
                  $(this).html('').removeClass('error');
                })
              }
            }

          },
          xhr:function(){

            var xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener( "progress", function ( evt){
              if(evt.lengthComputable){
                var percentComplete = Math.round((evt.loaded * 100) / evt.total);
                $(upload_cont).val(percentComplete / 100 * upload_max_percentage);
              }
            }, false);

            return xhr;

          }
        });

      })

      $("form:not(.async)", cont).submit(function(e){
        $('button', this).attr('disabled', true);
      });

      $(".no-submit").on('keydown', function(e){
        if(e.keyCode == 13){
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
      })

      $('.scrollpane').off('scroll').on('scroll', function(){
        $.lazy_load();
      })

      $(__ux_types).each(function(){
        if(typeof $('.' + this, cont)[this] == 'function'){
          $('.' + this, cont)[this]();
          $('.' + this, cont).attr('data-type', this);
        }
        /*else
          console.warn("Unknown ux type: " + this);*/
      });

      $("button[type=button][data-action]").off('click').on('click', function(){

        switch(this.getAttribute('data-action')){

          case 'close-modal': $(this).closest('.modal').close(); break;

        }

      })

    },

    process_xhr_response:function(response){

      if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){

        $.alert(response);

      }

      else{

        for(var key in response){

          var redirect = null;
          var rewrite = null;
          var scripts = [];
          var html = {};

          if(key == 'script'){
            scripts.push("<script class='async-exec'>" + response[key] + "<\/script>");
          }
          else if(key == 'redirect'){
            redirect = response[key];
          }
          else if(key == 'rewrite'){
            rewrite = response[key];
          }
          else if(key == '_'){

            var el = $(response[key]);

            if(typeof $(el).attr('id') != 'undefined'){
              var id = $(el).attr('id');
              if($("#" + id).length > 0){
                $("#" + id).html($(el).html());
                $.ux_init($("#" + id));
              }
              else{
                $('.screen').append(el);
                $.ux_init(el);
              }
            }
            else{
              $('.screen').append(el);
              $.ux_init(el);
            }

          }
          else{
            if($(key).length > 0)
              html[key] = response[key];
          }

          if(redirect != null)
            window.location = redirect;

          for(var key in html){

            var content = html[key];
            if($.type(content) == 'string'){
              if(content.substr(0, 2) == '>>'){
                $(key).append(content.substr(2));
              }
              else if(content.substr(0, 2) == '<<'){
                $(key).prepend(content.substr(2));
              }
              else if(content.substr(0, 2) == '><'){

                $(key).replaceWith(content.substr(2));

              }
              else{
                $(key).html(content);
              }
            }

            $.ux_init(key);

          }

          $('.async-exec').remove();
          for(var key in scripts)
            $(document.body).append(scripts[key]);

          if(rewrite != null){
            window.history.pushState(rewrite.data, rewrite.title, rewrite.url);
          }

        }
      }

    },

    fetch:function(url, params){

      var method = $.val('method', params, { d:'get' }).toUpperCase();
      var data = $.val('data', params, { d:null });
      var onerror = $.val('onerror', params, { d:null });
      var onsuccess = $.val('onsuccess', params, { d:null });

      $.ajax({
        headers:{
          Accept:"text/html; charset=utf-8"
        },
        method:method,
        url:url,
        data:data,
        cache:false,
        //processData:false, // GET failed if this is enabled
        contentType:false,
        dataType:'json',
        success:function(response){

          if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){
            onerror == null ? $.alert(response) : $.fire_event(onerror, [ response ], instance);
          }
          else{
            $.process_xhr_response(response);
            $.lazy_load();
            $.fire_event(onsuccess, [ response ], this);
          }

        },
        error:function(xhr){

          if(xhr.responseText.length > 0){
            var response = eval("(" + xhr.responseText + ")");

            if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){
              onerror == null ? $.alert(response) : $.fire_event(onerror, [ response ], instance);
            }
            else if(typeof response['message'] != 'undefined'){
            }
          }

        }
      })

    },

    json:function(url, params){

      var method = $.val('method', params, { d:'get' }).toUpperCase();
      var data = $.val('data', params, { d:null });
      var onerror = $.val('onerror', params, { d:null });
      var onsuccess = $.val('onsuccess', params, { d:null });

      $.ajax({
        headers:{
          Accept:"application/json",
        },
        method:method,
        url:url,
        data:data,
        //processData:false, // GET failed if this is enabled
        contentType:false,
        dataType:'json',
        success:function(response){

          if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){
            onerror == null ? $.alert(response) : $.fire_event(onerror, [ response ], instance);
          }
          else{
            $.process_xhr_response(response);
            $.lazy_load();
            $.fire_event(onsuccess, [ response ], this);
          }

        },
        error:function(xhr){

          if(xhr.responseText.length > 0){
            var response = eval("(" + xhr.responseText + ")");

            if(typeof response['error'] != 'undefined' && parseInt(response['error']) == 1){
              onerror == null ? $.alert(response) : $.fire_event(onerror, [ response ], instance);
            }
            else if(typeof response['message'] != 'undefined'){
            }
          }

        }
      })

    },

    filter_radio:function(el, key){

      key = key.toLowerCase();
      if(key.length > 1){
        var items = document.getElementsByClassName('filterable');
        for(var i = 0 ; i < items.length ; i++){
          items[i].getAttribute('data-tag').toLowerCase().indexOf(key) >= 0 ?
            items[i].classList.remove('hidden') :
            items[i].classList.add('hidden');
        }
      }

    },

    tagIndex:function(cont){

      if(typeof $__TAGS == 'undefined') $__TAGS = {};
      $__TAGS[cont] = { _:$(cont).html(), tagged:{} };

      $("*[data-tag]", cont).each(function(){
        $__TAGS[cont]['tagged'][this.getAttribute('data-tag').toLowerCase()] = this.outerHTML;
      })

    },

    searchTag:function(key, cont){

      if(typeof $__TAGS == 'undefined' || typeof $__TAGS[cont] == 'undefined') return;

      key = key.toLowerCase();

      if(key == ''){
        $(cont).html($__TAGS[cont]['_'])
      }
      else{
        var html = [];
        for(var tagKey in $__TAGS[cont]['tagged']){
          if(tagKey.indexOf(key) >= 0)
            html.push($__TAGS[cont]['tagged'][tagKey]);
        }
        $(cont).html(html.join(''));
      }

    },

    clearTag:function(cont, commit){

      if(typeof commit == 'undefined' || commit != true){
        window.setTimeout(function(){
          $.clearTag(cont, true);
        }, 777);
      }
      else{
        if(typeof $__TAGS == 'undefined' || typeof $__TAGS[cont] == 'undefined') return;
        $(cont).html($__TAGS[cont]['_']);
      }

    },

    map_to_object:function(str){

      var obj = {};
      if($.type(str) == 'string'){


        var maps = str.split(',');
        $(maps).each(function(){

          var m = this.split(':');
          if(m.length == 2)
            obj[m[0]] = m[1];

        })


      }
      return obj;

    },

    mediaType:function(){

      return window.matchMedia('screen and (min-width:320px) and (max-width:800px)').matches ? 'sm' :
        'lg';

    },

  })
  $.fn.extend({

    serializeObject:function(){

      var arr = $(this).serializeArray();
      var obj = {};
      $(arr).each(function(){
        obj[this.name] = this.value;
      })
      return obj;

    }

  })

  /* VALIDATION */
  $.fn.extend({

    validate:function(){

      var errors = [];

      $('.textbox, .textarea, .dropdown', this).each(function(){

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

      });

      $('.checkbox', this).each(function() {

        var valid = true;
        var validation = this.getAttribute('data-validation');
        var error_message = this.getAttribute('data-error-message');
        var value = this.firstElementChild.checked ? 1 : '';
        var name = this.firstElementChild.name;
        var error = $.validate(value, validation, { name:name, error_message:error_message });
        if(error.error > 0){
          valid = false;
          errors.push(error);
        }

        valid ? $(this).removeClass('invalid') : $(this).addClass('invalid');

      });

      $('.fileupload', this).each(function(){

        var valid = true;
        var validation = this.getAttribute('data-validation');
        var file = $('input[type=file]', this);
        if(file.length < 1 || file[0].files.length < 1){
          valid = false;
          errors.push('File upload harus diisi');
        }

        valid ? $(this).removeClass('invalid') : $(this).addClass('invalid');

      })

      $("*[data-type]", this).each(function(){

        var type = this.getAttribute('data-type');
        if(typeof $(this)[type + '_validate'] == 'function')
          errors = $.array_merge(errors, $(this)[type + '_validate']());

      })

      return errors;

    }

  })
  $.extend({

    validate:function(value, validation, options){

      validation = typeof validation == 'undefined' || validation == null ? '' : validation;

      var error = 0;
      var message = [];

      var name = options.name;
      var error_message = options['error_message'] != null ? options['error_message'] : false;

      var rules = validation.split('|');
      for(var i in rules){
        var rule = rules[i];

        switch(rule){

          case 'required':
            if(value == ''){
              error = 1;
              message[error_message ? error_message : name + " belum diisi"] = 1;
            }
            break;

          case 'email':
            if(!/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)){
              error = 1;
              message[error_message ? error_message : name + " harus diisi dengan benar"] = 1;
            };
            break;

          default:
            if(rule.indexOf('regex:') >= 0){
              var regex = new RegExp(rule.substr(6));
              if(!regex.test(value)){
                error = 1;
                message[error_message ? error_message : name + " harus diisi dengan data yang benar"] = 1;
              }
            }
            break;

        }

      }

      var messages = [];
      for(var key in message)
        messages.push(key);

      return {
        error:error,
        message:messages.length > 1 ? messages : messages.join("\n")
      }

    }

  })

  // Attach lazy load to document load & scroll
  $(window).on('DOMContentLoaded scroll', function(e){ $.lazy_load(); });

  // Patch for jQuery serialize and serializeArray function (to include button submit value)
  $(document).on('click', '[name][value]:button', function(evt){
    var $button = $(evt.currentTarget),
      $input = $button.closest('form').find('input[name="'+$button.attr('name')+'"]');
    if(!$input.length){
      $input = $('<input>', {
        type:'hidden',
        name:$button.attr('name')
      });
      $input.insertAfter($button);
    }
    $input.val($button.val());
  });

  $(function(){

    // Initialize available ux components in the page
    $.ux_init();

    $('.modal-cont').on('scroll', function(){ $.lazy_load(); });

    console.warn('Script loaded');

  })

})();
