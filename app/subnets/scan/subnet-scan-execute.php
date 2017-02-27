<?php

/*
 * Discover new hosts with ping
 *******************************/

$Scan	 	= new Scan ($Database, $User->settings);
$DNS	 	= new DNS ($Database, $User->settings);

# subnet Id must be a integer
if(!is_numeric($_POST['subnetId']))	{ $Result->show("danger", _("Invalid ID"), true); }

# verify that user has write permissionss for subnet
if($Subnets->check_permission ($User->user, $_POST['subnetId']) != 3) 	{ $Result->show("danger", _('You do not have permissions to modify hosts in this subnet')."!", true, true); }

# fetch subnet details
$subnet = $Subnets->fetch_subnet (null, $_POST['subnetId']);
$subnet!==false ? : $Result->show("danger", _("Invalid ID"), true, true);

# fake sectionId for snmp-route-all scan
$_POST['sectionId'] = $subnet->sectionId;

# full
if ($_POST['type']!="update-icmp" && $subnet->isFull==1)                { $Result->show("warning", _("Cannot scan as subnet is market as used"), true, true); }

# verify php path
if(!file_exists($Scan->php_exec))	{ $Result->show("danger", _("Invalid php path"), true, true); }

# scna
if($_POST['type']=="scan-icmp")			   { include(dirname(__FILE__) . "/subnet-scan-icmp.php"); }
elseif($_POST['type']=="scan-telnet")	   { include(dirname(__FILE__) . "/subnet-scan-telnet.php"); }
elseif($_POST['type']=="snmp-route-all")   { include(dirname(__FILE__) . "/subnet-scan-snmp-route-all.php"); }
elseif($_POST['type']=="snmp-arp")	       { include(dirname(__FILE__) . "/subnet-scan-snmp-arp.php"); }
elseif($_POST['type']=="snmp-mac")	       { include(dirname(__FILE__) . "/subnet-scan-snmp-mac.php"); }
# discovery
elseif($_POST['type']=="update-icmp")	   { include(dirname(__FILE__) . "/subnet-update-icmp.php"); }
elseif($_POST['type']=="update-snmp-arp")  { include(dirname(__FILE__) . "/subnet-update-snmp-arp.php"); }
else									   { $Result->show("danger", _("Invalid scan type"), true); }

?>

