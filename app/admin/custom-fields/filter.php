<?php

/**
 * set which custom field to display
 ************************/

$csrf = $User->csrf_create('filter_fields');
/*
	provided values are:
		table		= name of the table
 */

# get hidden custom fields from settings
$filters = json_decode($User->settings->hiddenCustomFields, true);
isset($filters[$_POST['table']]) ? : $filters[$_POST['table']] = array();

# fetch custom fields
$cfs = $Tools->fetch_custom_fields($_POST['table']);
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

	<?php
	foreach($cfs as $cf) {
		print "<tr>";
		# select
		print "	<td style='width:20px;'>";
        // When there are multiple inputs with the same name, PHP will use the last one set.  With dual inputs for all of these,
        // the initial value is in the hidden input and any user changes will overwrite that with the input 
        // from the checkbox, which gives us visible/hidden state for all fields when the user hits submit
        print "<input type='hidden' name='visible[$cf->id]' value='off'>";
		if($cf->visible) { 
            print "<input type='checkbox' class='input-switch' name='visible[$cf->id]'>"; 
        } else { 
            print "<input type='checkbox' class='input-switch' name='visible[$cf->id]' checked>"; 
        }
		print "	</td>";
		# name and comment
		print "	<td>".$cf->name." (".$cf->display_name.")</td>";
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
