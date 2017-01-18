<?php

/*	return first free IP address in provided subnet
***************************************************/

//get first free IP address
$firstIP = $Subnets->transform_to_dotted($Addresses->get_first_available_address ($_POST['subnetId'], $Subnets));

print $firstIP;
?>
