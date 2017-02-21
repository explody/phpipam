<?php

/*
 * Print resize subnet
 *********************/

# validate csrf cookie
$User->csrf_validate("split", $_POST['csrf_cookie'], $Result);

# id must be numeric
if(!is_numeric($_POST['subnetId']))			{ $Result->show("danger", _("Invalid ID"), true); }

# get subnet details
$subnet_old = $Subnets->fetch_subnet (null, $_POST['subnetId']);

# verify that user has write permissions for subnet
$subnetPerm = $Subnets->check_permission ($User->user, $subnet_old->id);
if($subnetPerm < 3) 						{ $Result->show("danger", _('You do not have permissions to resize subnet').'!', true); }

# verify
$Subnets->subnet_split ($subnet_old, $_POST['number'], $_POST['prefix'], @$_POST['group'], @$_POST['strict'], @$_POST['custom_fields']);

# all good
$Result->show("success", _("Subnet splitted ok")."!", true);

?>
