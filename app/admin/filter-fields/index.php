<?php
# TODO:  This shouldn't be a function only for ipaddresses
/**
 * Script to get all active IP requests
 ****************************************/
$standard_fields = $Tools->fetch_standard_field_names("ipaddresses");

# get all selected fields and put them to array
$selected_fields = explode(";", $User->settings->IPfilter);

# These are not user selectable
$ignore_fields = ['id','state','subnetId',
                  'ip_addr','description',
                  'dns_name','lastSeen',
                  'excludePing','editDate',
                  'is_gateway','PTR','PTRignore',
                  'firewallAddressObject']

?>


<h4><?php print _('Filter which fields to display in IP list'); ?></h4>
<hr>

<div class="alert alert-info alert-absolute"><?php print _('You can select which fields are actually being used for IP management, so you dont show any overhead if not used. IP, hostname and description are mandatory'); ?>.</div>


<form id="filterIP" style="margin-top:50px;clear:both;">
<?php $csrf->insertToken('/ajx/admin/filter-fields/filter-result'); ?>
<table class="filterIP table table-auto table-striped table-top">

<!-- headers -->
<tr>
	<th colspan="2"><?php print _('Check which fields to use for IP addresses'); ?>:</th>
</tr>

<!-- fields -->
<?php
foreach($standard_fields as $field_name) {
    
    if (in_array($field_name, $ignore_fields)) {
        continue;
    }
    
	# set active
	$checked = in_array($field_name, $selected_fields) ? "checked" : "";

	print '<tr>'. "\n";
	print '	<td style="width:10px;padding-left:10px;"><input type="checkbox" class="input-switch" name="'. $field_name .'" value="'. $field_name .'" '. $checked .'></td>';
	print '	<td>'. ucfirst($field_name) .'</td>';
	print '</tr>';
}
?>

<!-- submit -->
<tr>
	<td></td>
	<td>
		<button class="btn btn-sm btn-default" id="filterIPSave"><i class="fa fa-check"></i> <?php print _('Save'); ?></button>
	</td>
</tr>


</table>

</form>


<div class="filterIPResult" style="display:none"></div>