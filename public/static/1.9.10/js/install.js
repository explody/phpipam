/*
 phpipam 1.9.10 2017-03-24-11:15:32 
*/
$(document).ready(function() {
    $("div.jqueryError").hide(), $("div.loading").hide(), $("#toggle-advanced").on("click", function() {
        return $("div.loading").fadeIn("fast"), $("#advanced").slideToggle("fast"), $("div.loading").fadeOut("fast"), 
        !1;
    }), $(document).on("click", "a.install", function() {
        $("div.loading").fadeIn("fast");
        var postData = $("#install").serialize();
        $.post("app/install/install-execute.php", postData, function(data) {
            $("div.upgradeResult").html(data).slideDown("fast"), $("div.loading").fadeOut("fast");
        });
    }), $(document).on("click", "div.error", function() {
        $(this).stop(!0, !0).show();
    }), $("#postinstall").submit(function() {
        $("div.loading").fadeIn("fast");
        var postData = $(this).serialize();
        return $.post("app/install/postinstall_submit.php", postData, function(data) {
            $("div.postinstallresult").html(data).slideDown("fast"), $("div.loading").fadeOut("fast");
        }), !1;
    }), $("#manualUpgrade").click(function() {
        return $("#manualShow").slideToggle("fast"), !1;
    }), $(document).on("click", "input.upgrade", function() {
        $(this).removeClass("upgrade"), $("div.loading").fadeIn("fast");
        var version = $(this).attr("version");
        $.post("app/upgrade/upgrade-execute.php", {
            version: version
        }, function(data) {
            $("div#upgradeResult").html(data).slideDown("fast"), $("div.loading").fadeOut("fast");
        });
    }), $(document).on("click", "div.error", function() {
        $(this).stop(!0, !0).show();
    });
});