<?php

/**
 *	Script that resolved hostname from IP address
 */

$DNS		= new DNS ($Database);

# fetch subnet
$subnet = $Subnets->fetch_subnet ("id", $_POST['subnetId']);
$nsid = $subnet===false ? false : $subnet->nameserverId;

# resolve
$hostname = $DNS->resolve_address ($_POST['ipaddress'], false, true, $nsid);

# print result
print $hostname['name'];
?>
