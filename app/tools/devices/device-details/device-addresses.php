<?php

/**
 * Script to display devices
 */




# check
is_numeric($_GET['id']) ? : $Result->show("danger", _("Invalid ID"), true);

# fetch device
$device = (array) $Tools->fetch_object ("devices", "id", $_GET['id']);

# fetch all addresses on switch
$addresses     = $Tools->fetch_multiple_objects("ipaddresses", "device", $device['id']);

# title - hosts
print "<h4>"._("Belonging addresses")."</h4><hr>";

# Hosts table
print "<table id='switchMainTable' class='devices table table-striped table-top table-condensed'>";

# headers
print "<tr>";
print "	<th>"._('IP address')."</th>";
print "	<th>"._('Port')."</th>";
print "	<th>"._('Subnet')."</th>";
print "	<th colspan='2'>"._('Description')."</th>";
print "	<th class='hidden-xs'>"._('Hostname')."</th>";
print "	<th class='hidden-xs hidden-sm'>"._('Owner')."</th>";
print "</tr>";

# IP addresses
$ipcnt = 0;
if(sizeof($addresses) > 0) {
	foreach ($addresses as $ip) {
		# cast
		$ip = (array) $ip;

		# check permission
		$subnet_permission  = $Subnets->check_permission($User->user, $ip['id']);

		if($subnet_permission>0) {
			# get subnet and section details for belonging IP
			$subnet  = (array) $Subnets->fetch_subnet(null, $ip['id']);
			$section = (array) $Sections->fetch_section (null, $subnet['sectionId']);

			# print
			print "<tr>";
			print "	<td class='ip'><a href='".create_link("subnets",$section['id'],$subnet['id'],"address-details",$ip['id'])."'>".$Subnets->transform_to_dotted($ip['ip_addr'])."</a></td>";
			print "	<td class='port'>$ip[port]</td>";
			print "	<td class='subnet'><a href='".create_link("subnets",$section['id'],$subnet['id'])."'>$subnet[ip]/$subnet[mask]</a> <span class='text-muted'>($subnet[description])</span></td>";
			print "	<td class='description'>$ip[description]</td>";

			# print info button for hover
			print "<td class='note'>";
			if(!empty($ip['note'])) {
				$ip['note'] = str_replace("\n", "<br>",$ip['note']);
				print "	<i class='fa fa-comment-o' rel='tooltip' title='$ip[note]'></i>";
			}
			print "</td>";

			print "	<td class='dns hidden-xs'>$ip[dns_name]</td>";
			print "	<td class='owner hidden-xs hidden-sm'>$ip[owner]</td>";
			print "</tr>";

			$ipcnt++;
		}
	}
}

# empty
if($ipcnt == 0) {
print "<tr class='alert text-info'>";
print "	<td colspan='8'>"._('No hosts belonging to this device')."!</td>";
print "</tr>";
}

print "</table>";			# end table
print "</td>";

print "</tr>";
print "</table>";
?>