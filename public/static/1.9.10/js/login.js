/*
 phpipam 1.9.10 2017-03-02-10:56:28 
*/
$(document).ready(function() {
    function showSpinner() {
        $("div.loading").show();
    }
    function hideSpinner() {
        $("div.loading").fadeOut("fast");
    }
    function loginRedirect() {
        var base = $(".iebase").html();
        window.location = base;
    }
    $("div.jqueryError").hide(), $("div.loading").hide(), $("form#login").submit(function() {
        showSpinner(), $("div#loginCheck").stop(!0, !0);
        var logindata = $(this).serialize();
        return $("div#loginCheck").hide(), $.post("/ajx/login/login_check", logindata, function(data) {
            $("div#loginCheck").html(data).fadeIn("fast"), data.search("alert alert-success") != -1 ? (showSpinner(), 
            $("form#login input#phpipamredirect").length > 0 ? setTimeout(function() {
                window.location = $("form#login input#phpipamredirect").val();
            }, 1e3) : setTimeout(loginRedirect, 1e3)) : hideSpinner();
        }), !1;
    }), $(document).on("submit", "#requestIP", function() {
        var subnet = $("#requestIPsubnet").serialize(), IPdata = $(this).serialize(), postData = subnet + "&" + IPdata;
        return showSpinner(), $.post("ajx/login/request_ip_result", postData, function(data) {
            $("div#requestIPresult").html(data).slideDown("fast"), hideSpinner(), data.search("alert alert-success") != -1 && ($('form#requestIP input[type="text"]').val(""), 
            $("form#requestIP textarea").val(""));
        }), !1;
    }), $(".clearIPrequest").click(function() {
        $('form#requestIP input[type="text"]').val(""), $("form#requestIP textarea").val("");
    });
});