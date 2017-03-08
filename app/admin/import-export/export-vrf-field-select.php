<?php

/**
 *	VRF export
 */

?>

<!-- header -->
<div class="pHeader"><?php print _("Select VRF fields to export"); ?></div>

<!-- content -->
<div class="pContent">
    
<form id="selectExportTargets" method="post">
<input type="hidden" name="<?php print $csrf->getFormIndex(); ?>" />
<input type="hidden" name="<?php print $csrf->getFormToken(); ?>" />

<?php
print "	<table class='table table-striped table-condensed'>";
print "	<tr>";
print "	<th>"._('Name')."</th>";
print "	<th>"._('RD')."</th>";
print "	<th>"._('Description')."</th>";
print "	</tr>";
print "	<tr>";
print "	<td><input type='checkbox' name='name' checked title='"._('Mandatory')."'></td>";
print "	<td><input type='checkbox' name='rd' checked> </td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print "	</tr>";
print '</table>';
?>

</form>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-success" id="dataExportSubmit" data-form="selectExportTargets" data-type="vrf" data-action="export" data-csrf="<?php print htmlspecialchars(json_encode($csrf->getTokenArray('/ajx/admin/import-export/export-vrf'))); ?>"><i class="fa fa-upload"></i> <?php print _('Export'); ?></button>
	</div>
</div>
