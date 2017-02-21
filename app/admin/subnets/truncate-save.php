<?php

/*
 * truncate subnet result
 *********************/

# validate csrf cookie
$User->csrf_validate("truncate", $_POST['csrf_cookie'], $Result);

# id must be numeric
if(!is_numeric($_POST['subnetId']))			{ $Result->show("danger", _("Invalid ID"), true); }

# get subnet details
$subnet = $Subnets->fetch_subnet (null, $_POST['subnetId']);

# verify that user has write permissions for subnet
$subnetPerm = $Subnets->check_permission ($User->user, $subnet->id);
if($subnetPerm < 3) 						{ $Result->show("danger", _('You do not have permissions to resize subnet').'!', true); }

# truncate network
if(!$Subnets->subnet_truncate($subnet->id))	{ $Result->show("danger",  _("Failed to truncate subnet"), false); }
else										{ $Result->show("success", _("Subnet truncated succesfully")."!", false); }

# check for DNS PTR records
if ($User->settings->enablePowerDNS=="1" && $subnet->DNSrecursive=="1") {
	# powerDNS class
	$PowerDNS = new PowerDNS ($Database);
	if($PowerDNS->db_check()!==false) {
		// set name
		$zone = $PowerDNS->get_ptr_zone_name ($subnet->ip, $subnet->mask);
		// fetch domain
		$domain = $PowerDNS->fetch_domain_by_name ($zone);
		// if it exist remove all PTR records
		if ($domain!==false) {
    		// get all PTRs
    		$ptr_indexes = $Addresses->ptr_get_subnet_indexes ($subnet->id);
			// remove existing records and links
			$PowerDNS->remove_all_ptr_records ($domain->id, $ptr_indexes);
			// ok
			$Result->show("success", "PTR records removed", false);
		}
	}
	# error
	else {
		$Result->show("danger", "Cannot connect to powerDNS database", false);
	}
}
?>
