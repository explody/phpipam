(function ( $ ) {
    
    // function splitText (rawData) {
    //     /* if we can split the data, then there are delimited fields inside the 
    //        <option>.  e.g. address-modify.php, line 395ish */
    //     if (!offset) {
    //         offset = 1;
    //     }
    //     var data = rawData.split(' | ');
    //     var base = '';
    //     var extra = [];
    //     
    //     for (i=0; i< data.length;++i) {
    //         base += (i<offset ? ' ' + data[i] : '');
    //         extra += (i>=offset ? ' ' + data[i] : '');
    //     }
    //     return [base,extra];
    // }
    
    function s2custContainer(contents) {
        return '<div class="s2cust-container">' +
               contents +
               '</div>';
    }
    
    $.fn.s2boldIPMaskDescOneLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = optionElement.text.split(' | ');
        var extra = d.slice(2);
        
        var $state = $(
            s2custContainer(
                '<span class="s2cust-1lineB-cont">' + d[0] + '/' + d[1] + '</span>' + 
                '<span class="s2cust-1line-extra"> ' + extra + '</span>'
            )
        );
        return $state;
    }
    
    $.fn.s2boldDescTwoLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = optionElement.text.split(' | ');
        var extra = d.slice(1);
        
        var $state = $(
            s2custContainer(
                '<div class="s2cust-2lineB-cont">' + d[0] + '</div>' + 
                '<div class="s2cust-2line-extra"> ' + extra + '</div>'
            )
        );
        return $state;
    }
    
    $.fn.s2boldDescOneLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = optionElement.text.split(' | ');
        var extra = d.slice(1);

        var $state = $(
            s2custContainer(
                '<span class="s2cust-1lineB-cont">' + d[0] + '</span>' + 
                '<span class="s2cust-1line-extra"> ' + extra + '</span>'
            )
        );
        return $state;
    }
    
    $.fn.s2oneLine = function (optionElement) {
        if (!optionElement.id) { 
            return optionElement.text; 
        }
        var d = optionElement.text.split(' | ');
        var extra = d.slice(1);

        var $state = $(
            s2custContainer(
                '<span class="s2cust-1line-cont">' + d[0] + '</span>' + 
                '<span class="s2cust-1line-extra"> ' + extra + '</span>'
            )
        );
        return $state;
    }

}( jQuery ));