<?php

/**
 * Script to display devices
 */




# check
is_numeric($_GET['id']) ? : $Result->show("danger", _("Invalid ID"), true);

# cast
$device = (array) $device;

# title - subnets
print "<h4>"._("Belonging subnets")."</h4><hr>";

//fetch
$subnets = $Tools->fetch_multiple_objects ("subnets", "device", $device['id']);

# Hosts table
print "<table id='switchMainTable' class='devices table table-striped table-top table-condensed'>";

# headers
print "<tr>";
print "	<th>"._('Section')."</th>";
print "	<th>"._('Subnet')."</th>";
print "	<th>"._('Description')."</th>";
print "	<th>"._('VLAN')."</th>";
print "</tr>";

$ipcnt = 0;

foreach ($subnets as $s) {

	$subnet_permission  = $Subnets->check_permission($User->user, $s->id);

	if($subnet_permission>0) {

		$section = (array) $Sections->fetch_section (null, $s->sectionId);
		$vlan	 = $Tools->fetch_object ("vlans", 'vlanId', $s->vlanId);

		print "<tr>";
		print "	<td class='ip'><a href='".create_link("subnets",$section['id'])."'>"; 
        print  (empty($section['name']) ? $section['description'] : $section['name']);
        print "</a></td>";
		print "	<td class='ip'><a href='".create_link("subnets",$section['id'],$s->id)."'>".$Subnets->transform_to_dotted($s->subnet)."/".$s->mask."</a></td>";
		print "	<td class='port'>".$s->description."</td>";
		print "	<td class='description'>".@$vlan->number ." ".@$vlan->description."</td>";

		$ipcnt++;
	}
}


# empty
if($ipcnt == 0) {
print "<tr class='alert text-info'>";
print "	<td colspan='8'>"._('No subnets belong to this device')."!</td>";
print "</tr>";
}

print "</table>";
?>