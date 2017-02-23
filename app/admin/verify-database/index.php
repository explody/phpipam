<?php

/**
 * Script to verify database structure
 ****************************************/

# admin user is required
$User->is_admin(true);

# title
print "<h4>"._('Database structure verification').'</h4><hr>';

# we will also check for invalid subnets and addresses
print "<h4>"._('Invalid subnets').'</h4><hr>';

$invalid_subnets = $Subnets->find_invalid_subnets();
if (sizeof($invalid_subnets) == 0 || $invalid_subnets===false) {
	$Result->show ("success", _("No invalid subnets detected"), false);
}
else {
	print "Found following invalid subnets (with unexisting parent subnet):<hr>";
	// loop
	foreach ($invalid_subnets as $subnet) {
		// print each subnet
		foreach ($subnet as $s) {
			print " - <a href='".create_link("subnets", $s->sectionId, $s->id)."'>$s->ip/$s->mask</a> ($s->description)"."<br>";
		}
	}
}


print "<h4>"._('Invalid addresses').'</h4><hr>';

$invalid_subnets = $Addresses->find_invalid_addresses();
if (sizeof($invalid_subnets) == 0 || $invalid_subnets===false) {
	$Result->show ("success", _("No invalid addresses detected"), false);
}
else {
	print "Found following invalid addresses (with unexisting subnet):<hr>";
	// loop
	foreach ($invalid_subnets as $subnet) {
		// print each subnet
		foreach ($subnet as $s) {
			print " <a class='btn btn-xs btn-danger modIPaddr' data-action='delete' data-id='$s->id' data-subnetId='$s->subnetId'><i class='fa fa-remove'></i></a> $s->ip $s->dns_name (database id: $s->id)"."<br>";
		}
	}
}


?>
