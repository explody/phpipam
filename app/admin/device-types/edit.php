<?php

/**
 *	Edit device details
 ************************/

# ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['id'])) { $Result->show("danger", _("Invalid ID"), true, true); }
# set delete flag
$readonly = $_POST['action']=="delete" ? "readonly" : "";

# fetch device type details
if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
	$deviceType = $Admin->fetch_object("deviceTypes", "id", $_POST['id']);
	# fail if false
	$deviceType===false ? $Result->show("danger", _("Invalid ID"), true) : null;
}
?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('device type'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="devTypeEdit">
    <?php $csrf->insertToken('/ajx/admin/device-types/edit-result'); ?>
	<table class="table table-noborder table-condensed">

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" name="name" class="form-control input-sm" placeholder="<?php print _('Name'); ?>" value="<?php print @$deviceType->name; ?>" <?php print $readonly; ?>>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
				print '<input type="hidden" name="id" value="'. $_POST['id'] .'">'. "\n";
			}
			?>
		</td>
	</tr>

	<!-- IP address -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="description" class="form-control input-sm" placeholder="<?php print _('Description'); ?>" value="<?php print @$deviceType->description; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	</table>
	</form>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editDevTypeSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- result -->
	<div class="devTypeEditResult"></div>
</div>
