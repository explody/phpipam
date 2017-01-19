<?php

/*
 * Print edit subnet
 *********************/

# validate csrf cookie
$User->csrf_validate("linkedsubnet", $_POST['csrf_cookie'], $Result);

# check subnet permissions
if($Subnets->check_permission ($User->user, $_POST['subnetId']) != 3) 	{ $Result->show("danger", _('You do not have permissions to add edit/delete this subnet')."!", true); }

# ID must be numeric
if(!is_numeric($_POST['subnetId']))	{ $Result->show("danger", _("Invalid ID"), true); }
if(!is_numeric($_POST['linked_subnet']))	{ $Result->show("danger", _("Invalid ID"), true); }

# submit
$values = array(
    "id" => $_POST['subnetId'],
    "linked_subnet" => $_POST['linked_subnet']
);

# verify that user has write permissions for subnet
if($Subnets->modify_subnet ("edit", $values)!==false) {
    $Result->show("success", _("Subnet linked"), false);
}
?>
