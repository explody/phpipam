<?php

/*
 * Data import load
 *************************************************/

# fetch all l2 domains
$vlan_domains = $Admin->fetch_all_objects("vlanDomains", "id");

# read again the custom fields, if any
if (!isset($cfs)) { $cfs = $Tools->fetch_custom_fields("vlans"); }

# Load existing data
$edata = array(); $vdom = array(); $vdomid = array();
# process for easier later check
foreach ($vlan_domains as $vlan_domain) {
	//cast
	$vlan_domain = (array) $vlan_domain;
	$vdom[] = $vlan_domain['name'];
	$vdomid[$vlan_domain['name']] = $vlan_domain['id'];
	// read vlans
	$all_vlans = $Admin->fetch_multiple_objects("vlans", "domainId", $vlan_domain['id'], "number");
	// skip empty domains
	if (empty($all_vlans)) { continue; }
	//write all VLAN entries
	foreach ($all_vlans as $vlan) {
		//cast
		$vlan = (array) $vlan;
		$edata[$vlan_domain['name']][$vlan['number']] = $vlan;
	}
}

$rows = "";
$counters = array();
$unique = array();

# check the fields
foreach ($data as &$cdata) {
	$msg = ""; $action = ""; $cfieldtds = "";

	# set a default domain if none specified
	if ($cdata['domain'] != "") { $cdom = $cdata['domain']; } else { $cdom = "default"; }

	# check if domain exists and link ID, otherwise issue error
	if (!in_array($cdom,$vdom)) {
		$msg.= "Missing VLAN domain. Please add/import VLAN domain first."; $action = "error";
	} else {
		$cdata['domainId'] = $vdomid[$cdom];
	}

	# check if required fields are present and not empty
	foreach($reqfields as $creq) {
		if ((!isset($cdata[$creq])) or ($cdata[$creq] == "")) { $msg.= "Required field ".$creq." missing or empty."; $action = "error"; }
	}

	# check data format
	if ($action != "error") {
		if (!preg_match("/^[a-zA-Z0-9-_]+$/", $cdata['name'])) { $msg.="Invalid name format."; $action = "error"; }
		if (!preg_match("/^[0-9]+$/", $cdata['number'])) { $msg.="Invalid number format."; $action = "error"; }
		if (preg_match("/[;'\"]/", $cdata['description'])) { $msg.="Invalid characters in description."; $action = "error"; }
		if (!preg_match("/^[a-zA-Z0-9-_ ]+$/", $cdom)) { $msg.="Invalid domain format."; $action = "error"; }
		if ($action != "error") { if ($cdata['number']>$User->settings->vlanMax) { $msg.= _('Highest possible VLAN number is ').$User->settings->vlanMax.'!'; $action = "error"; } }
	}

	# Generate the custom fields columns
	if(sizeof($cfs) > 0) { foreach($cfs as $cf) { $cfieldtds.= "<td>".$cdata[$cf->name]."</td>"; } }

	# check if duplicate VLAN
	if (isset($unique[$cdom][$cdata['number']])) { $msg.= "Duplicate VLAN domain and number not supported. Please check import file."; $action = "error"; }

	# check if existing
	if ($action != "error") {
		if (isset($edata[$cdom][$cdata['number']])) {
			$cdata['vlanId'] = $edata[$cdom][$cdata['number']]['vlanId'];
			$action = "skip"; # skip duplicate fields if identical, update if different
			if ($cdata['name'] != $edata[$cdom][$cdata['number']]['name']) { $msg.= "VLAN name will be updated."; $action = "edit"; }
			if ($cdata['description'] != $edata[$cdom][$cdata['number']]['description']) { $msg.= "VLAN description will be updated."; $action = "edit"; }
			# Check if the values of the custom fields have changed

			foreach($cfs as $cf) {
				if ($cdata[$cf->name] != $edata[$cdom][$cdata['number']][$cf->name]) {
					$msg.= "VLAN ".$cf->name." will be updated."; $action = "edit";
				}
			}

			if ($action == "skip") {
				$msg.= "Duplicate, will skip.";
			}
		} else {
			$msg.="New entry, will be added."; $action = "add";
		}
	}

	$cdata['msg'].= $msg;
	$cdata['action'] = $action;
	$counters[$action]++;
	if (!isset($unique[$cdom][$cdata['number']])) { $unique[$cdom][$cdata['number']] = $cdata['name']; }

	$rows.="<tr class='".$colors[$action]."'><td><i class='fa ".$icons[$action]."' rel='tooltip' data-placement='bottom' title='"._($msg)."'></i></td>
		<td>".$cdata['name']."</td>
		<td>".$cdata['number']."</td>
		<td>".$cdata['description']."</td>
		<td>".$cdata['domain']."</td>
		".$cfieldtds."
		<td>"._($cdata['msg'])."</td></tr>";
}

?>

