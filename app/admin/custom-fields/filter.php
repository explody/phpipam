<?php

/**
 * set which custom field to display
 ************************/

$csrf = $User->csrf_cookie ("create", "filter_fields");
/*
	provided values are:
		table		= name of the table
 */

# get hidden custom fields from settings
$filters = json_decode($User->settings->hiddenCustomFields, true);
isset($filters[$_POST['table']]) ? : $filters[$_POST['table']] = array();

# fetch custom fields
$custom = $Tools->fetch_custom_fields($_POST['table']);
?>

<script type="text/javascript">
$(document).ready(function() {
/* bootstrap switch */
var switch_options = {
	onText: "Hidden",
	offText: "Visible",
    onColor: 'default',
    offColor: 'default',
    size: "mini",
    inverse: true
};

$(".input-switch").bootstrapSwitch(switch_options);
});
</script>

<!-- header -->
<div class="pHeader"><?php print _('Filter custom field display'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="editCustomFieldsFilter">
	<table id="editCustomFields" class="table table-noborder table-condensed">

	<input type="hidden" name="table" value="<?php print $_POST['table']; ?>">

	<?php
	foreach($custom as $k=>$c) {
		print "<tr>";
		# select
		print "	<td style='width:20px;'>";
		if(in_array($k, $filters[$_POST['table']]))	{ print "<input type='checkbox' class='input-switch' name='$k' checked>"; }
		else										{ print "<input type='checkbox' class='input-switch' name='$k'>"; }
		print "	</td>";
		# name and comment
		print "	<td>".$k." (".$c['Comment'].")</td>";
		print "</tr>";
	}

	?>
	</table>
    <input type="hidden" name="csrf_cookie" value="<?php print $csrf; ?>">
	</form>

	<hr>
	<div class="text-muted">
	<?php print _("Selected fields will not be visible in table view, only in detail view"); ?>
	</div>
	<hr>
</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Close'); ?></button>
		<button class="btn btn-sm btn-default " id="editcustomFilterSubmit"><i class="fa fa-check"></i> <?php print ucwords(_("Save filter")); ?></button>
	</div>
	<!-- result -->
	<div class="customEditFilterResult"></div>
</div>
