/*
 phpipam 1.9.10 2017-03-02-10:56:28 
*/
$(document).ready(function() {
    $("div.jqueryError").hide(), $("div.loading").hide(), $("form#setup-basic").submit(function() {
        $("div.loading").fadeIn("fast");
        var postData = $(this).serialize();
        return $.post("ajx/setup/setup-basic-result", postData, function(data) {
            $("div.setup-basic-result").html(data).slideDown("fast"), $("div.loading").fadeOut("fast"), 
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        }), !1;
    }), $(document).on("click", "input.migrate", function() {
        $(this).removeClass("migrate"), $("div.loading").fadeIn("fast"), $.post("ajx/migrate/migration-execute", function(data) {
            $("div#migrationResult").html(data).slideDown("fast"), $("div.loading").fadeOut("fast");
        });
    }), $(document).on("click", "div.error", function() {
        $(this).stop(!0, !0).show();
    });
});