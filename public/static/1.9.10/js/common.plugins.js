/*
 phpipam 1.9.10 2017-03-24-11:15:32 
*/
!function($) {
    function s2custContainer(contents) {
        return '<div class="s2cust-container">' + contents + "</div>";
    }
    $.fn.s2boldIPMaskDescOneLine = function(optionElement) {
        if (!optionElement.id) return optionElement.text;
        var d = optionElement.text.split(" | "), extra = d.slice(2), $state = $(s2custContainer('<span class="s2cust-1lineB-cont">' + d[0] + "/" + d[1] + '</span><span class="s2cust-1line-extra"> ' + extra + "</span>"));
        return $state;
    }, $.fn.s2boldDescTwoLine = function(optionElement) {
        if (!optionElement.id) return optionElement.text;
        var d = optionElement.text.split(" | "), extra = d.slice(1), $state = $(s2custContainer('<div class="s2cust-2lineB-cont">' + d[0] + '</div><div class="s2cust-2line-extra"> ' + extra + "</div>"));
        return $state;
    }, $.fn.s2boldDescOneLine = function(optionElement) {
        if (!optionElement.id) return optionElement.text;
        var d = optionElement.text.split(" | "), extra = d.slice(1), $state = $(s2custContainer('<span class="s2cust-1lineB-cont">' + d[0] + '</span><span class="s2cust-1line-extra"> ' + extra + "</span>"));
        return $state;
    }, $.fn.s2oneLine = function(optionElement) {
        if (!optionElement.id) return optionElement.text;
        var d = optionElement.text.split(" | "), extra = d.slice(1), $state = $(s2custContainer('<span class="s2cust-1line-cont">' + d[0] + '</span><span class="s2cust-1line-extra"> ' + extra + "</span>"));
        return $state;
    };
}(jQuery);