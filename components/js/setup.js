/**
 * Javascript / jQuery install functions
 */


$(document).ready(function() {

/* hide error div if jquery loads ok
*********************************************/
$('div.jqueryError').hide();
$('div.loading').hide();

/* basic setup */
$('form#setup-basic').submit(function() {
	$("div.loading").fadeIn("fast");
	var postData = $(this).serialize();
	$.post("ajx/setup/setup-basic-result", postData, function(data) {
		$("div.setup-basic-result").html(data).slideDown("fast");
		$("div.loading").fadeOut("fast");
        setTimeout(function (){window.location.reload();}, 1500);
	});
	return false;
});

$(document).on("click", "input.migrate", function() {
	$(this).removeClass("migrate");
	$("div.loading").fadeIn("fast");
	$.post("ajx/migrate/migration-execute", function(data) {
		$("div#migrationResult").html(data).slideDown("fast");
		$("div.loading").fadeOut("fast");
	});
});

$(document).on("click", "div.error", function() {
	$(this).stop(true,true).show();
});


});


