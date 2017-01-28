<?php

/**
 * Script to manage sections
 *************************************************/
 
$csrf = $User->csrf_create('ripe-import');

# verify that user is logged in
$User->check_user_session();
// TODO: select2 on the ripe-import dropdowns
?>

<h4><?php print _('Import subnets from RIPE'); ?></h4>
<hr><br>


<?php $Result->show("info alert-absolute",  _('This script imports subnets from RIPE database for specific AS. Enter desired AS to search for subnets'), false); ?>

<form name="ripeImport" id="ripeImport" style="margin-top:50px;clear:both;" class="form-inline" role="form">
	<div class="form-group">
		<input class="search form-control input-sm" placeholder="<?php print _('AS number'); ?>" name="as" type="text">
	</div>
	<div class="form-group">
        <input type="hidden" name="csrf_cookie" value="<?php print $csrf; ?>">
		<button type="submit" class="btn btn-sm btn-default"><?php print _('Search'); ?></button>
	</div>
</form>

<!-- result -->
<div class="content" style="margin-top:10px;">
	<div class="ripeImportTelnet"></div>
</div>