//jQuery Counter v 0.1
//written by: Łukasz Traczewski (c) 2012
//05-05-2012

(function($){
    
    var settings = {
        'value' : '0',
        'digitsNumber' : 4,
        'backUrl' : 'styles/ui/images/digits2.png'
      };
    
    var methods = {
      init : function (options)
      {
            this.each(function()
            {
          
                settings['value'] = $(this).attr('name').toString();
          
                //jezeli sa opcje to zmieniamy domyslne
                if(options)
                {
                    $.extend(settings, options);
                }
          
                var wrapper_width = 19 * settings['digitsNumber'];

                $(this).css({
                    'overflow' : 'hidden', 
                    'height' : '29px', 
                    'width' : wrapper_width + 'px'
                    });

                for(var i = 0;i < settings['value'].length;i++)
                {
                    var margin = settings['value'].charAt(i) * 174.3 + 1;

                    $(this).prepend('<div class="number-' + settings['value'].charAt(i) + '" style="float:right;height:1743px;width:19px;background:url(\'' + settings['backUrl'] + '\');margin-top:-' + margin + 'px"></div>');
                }

                if(settings['value'].length < settings['digitsNumber'])
                {
                    var diff = settings['digitsNumber'] - settings['value'].length;

                    for(var i = 0;i < diff;i++)
                    {
                        $(this).append("<div class=\"number-0\" style=\"float:right;height:1743px;width:19px;background:url('" + settings['backUrl'] + "');margin-top:-1px\"></div>");
                    }
                }
            });
      },
      update : function (value)
      {
          var val = value.toString();
          var diff = settings['digitsNumber'] - val.length;
          var tmp = "";
          
          for(var j = 0;j < diff;j++)
          {
              tmp += '0';
          }
          val = tmp + val;
          //alert(val);
          
          for(var i = 0;i < val.length;i++)
          {
              //alert(val.charAt(i));
              if($(this).children('div:eq(' + ($(this).children().size() - i - 1)  + ')').attr('class').toString().charAt(7) != val.charAt(i))
              {
                //alert(val.charAt(i));
                //alert($(this).children().size());
                //alert($(this).children('div:eq(' + ($(this).children().size() - i - 1)  + ')').attr('class').charAt(7));
                $(this).children('div:eq(' + ($(this).children().size() - i - 1)  + ')').animate({'margin-top' : '-' + (val.charAt(i) * 174.3 + 1) + 'px'}, 1000);
                $(this).children('div:eq(' + ($(this).children().size() - i - 1)  + ')').attr('class', 'number-' + val.charAt(i));
              }
          }
      }  
    };
    
    $.fn.counter = function(options)
    {
        if (methods[options])
        {
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof options === 'object' || !options)
        {
            return methods.init.apply(this, arguments);
        }
        else
        {
            $.error('Method ' +  method + ' does not exist on jQuery.tooltip');
        } 
    }
})(jQuery);