(function ( $ ) {
    
    function splitText (rawData,offset) {
        /* if we can split the data, then there are delimited fields inside the 
           <option>.  e.g. address-modify.php, line 395ish */
        if (!offset) {
            offset = 1;
        }
        var data = rawData.split(' | ');
        var base = '';
        var extra = '';
        
        for (i=0; i< data.length;++i) {
            base += (i<offset ? ' ' + data[i] : '');
            extra += (i>=offset ? ' ' + data[i] : '');
        }
        return [base,extra];
    }
    
    $.fn.s2boldDescTwoLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = splitText(optionElement.text,1);

        var $state = $(
            '<div class="s2cust-container">' +
            '<div class="s2cust-2lineB-cont">' + d[0] + '</div>' + 
            '<div class="s2cust-2line-extra"> ' + d[1] + '</div>' + 
            '</div>'
        );
        return $state;
    }
    
    $.fn.s2boldDescOneLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = splitText(optionElement.text,1);

        var $state = $(
            '<div class="s2cust-container">' +
            '<span class="s2cust-1lineB-cont">' + d[0] + '</span>' + 
            '<span class="s2cust-1line-extra"> ' + d[1] + '</span>' +
            '</div>'
        );
        return $state;
    }
    
    $.fn.s2oneLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = splitText(optionElement.text,1);

        var $state = $(
            '<div class="s2cust-container">' +
            '<span class="s2cust-1line-cont">' + d[0] + '</span>' + 
            '<span class="s2cust-1line-extra"> ' + d[1] + '</span>' +
            '</div>'
        );
        return $state;
    }

}( jQuery ));