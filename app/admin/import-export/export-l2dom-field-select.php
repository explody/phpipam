<?php

/**
 *	L2 Domain export
 */
 
 $csrf = $User->csrf_cookie ("create", "export-l2domains");

# prepare HTML variables
$custom_fields_names = "";
$custom_fields_boxes = "";

?>

<!-- header -->
<div class="pHeader"><?php print _("Select L2 Domain fields to export"); ?></div>

<!-- content -->
<div class="pContent" style="overflow:auto;">

<?php

# print
print '<form id="selectExportFields">';

# table
print "	<table class='table table-striped table-condensed'>";

print "	<tr>";
print "	<th>"._('Name')."</th>";
print "	<th>"._('Description')."</th>";
print "	</tr>";

print "	<tr>";
print "	<td><input type='checkbox' name='name' checked title='"._('Mandatory')."'></td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print "	</tr>";

print '</table>';
print '<input type="hidden" name="csrf_cookie" value="' . $csrf . '">';
print '<input type="hidden" name="action" value="export">';
print '</form>';

?>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-success" id="dataExportSubmit" data-type="l2dom"><i class="fa fa-upload"></i> <?php print _('Export'); ?></button>
	</div>
</div>
