<?php

/**
 *	select which fields to export
 */

# set and check permissions
$subnet_permission = $Subnets->check_permission($User->user, $_POST['subnetId']);
$subnet_permission > 0 ? :		$Result->show("danger", _('You do not have access to this network'), true);

?>

<!-- header -->
<div class="pHeader"><?php print _("Select fields to export"); ?></div>


<!-- content -->
<div class="pContent">

<?php

# print
print '<form id="selectExportFields">';

# table
print "	<table class='table table-striped table-condensed'>";

print "	<tr>";
print "	<td colspan='2'><h4>"._('Standard fields')."</h4></td>";
print "	</tr>";

# IP addr - mandatory
print "	<tr>";
print "	<td>"._('IP address')."</td>";
print "	<td><input type='checkbox' name='ip_addr' checked> </td>";
print "	</tr>";

# state
print "	<tr>";
print "	<td>"._('IP state')."</td>";
print "	<td><input type='checkbox' name='state' checked> </td>";
print "	</tr>";

# description - mandatory
print "	<tr>";
print "	<td>"._('Description')."</td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print "	</tr>";

# hostname - mandatory
print "	<tr>";
print "	<td>"._('Hostname')."</td>";
print "	<td><input type='checkbox' name='dns_name' checked> </td>";
print "	</tr>";

# firewallAddressObject - mandatory
print "	<tr>";
print "	<td>"._('FW Object')."</td>";
print "	<td><input type='checkbox' name='firewallAddressObject' checked> </td>";
print "	</tr>";

# mac
print "	<tr>";
print "	<td>"._('MAC address')."</td>";
print "	<td><input type='checkbox' name='mac' checked> </td>";
print "	</tr>";

# owner
print "	<tr>";
print "	<td>"._('Owner')."</td>";
print "	<td><input type='checkbox' name='owner' checked> </td>";
print "	</tr>";

# switch
print "	<tr>";
print "	<td>"._('Device')."</td>";
print "	<td><input type='checkbox' name='device' checked> </td>";
print "	</tr>";

# port
print "	<tr>";
print "	<td>"._('Port')."</td>";
print "	<td><input type='checkbox' name='port' checked> </td>";
print "	</tr>";

# note
print "	<tr>";
print "	<td>"._('Note')."</td>";
print "	<td><input type='checkbox' name='note' checked> </td>";
print "	</tr>";

# note
print "	<tr>";
print "	<td>"._('Location')."</td>";
print "	<td><input type='checkbox' name='location' checked> </td>";
print "	</tr>";

# get all custom fields
$cfs = $Tools->fetch_custom_fields ('ipaddresses');
foreach($cfs as $cf) {
	print "	<tr>";
	print "	<td>$cf->name</td>";
	print "	<td><input type='checkbox' name='$cf->name' checked> </td>";
	print "	</tr>";
}


# set file name
print "	<tr>";
print "	<td style='width:140px;'>"._('Filename')."</td>";
print "	<td><input type='text' class='form-control' name='filename' value='phpipam_subnet_export.xls' style='height:auto;'></td>";
print "	</tr>";

print '</table>';
print '</form>';

?>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-success" id="exportSubnet"><i class="fa fa-download"></i> <?php print _('Export'); ?></button>
	</div>
</div>
