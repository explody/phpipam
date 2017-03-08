<?php

/**
 * Edit device result
 ***************************/

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);

# ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['id'])) 	{ $Result->show("danger", _("Invalid ID"), true); }

# name must be present! */
if($_POST['name'] == "") 									{ $Result->show("danger", _('Name is mandatory').'!', false); }

# create array of values for modification
$values = array("id"=>@$_POST['id'],
				"name"=>$_POST['name'],
				"description"=>@$_POST['description']);

# update
if(!$Admin->object_modify("deviceTypes", $_POST['action'], "id", $values)) 	{ $Result->show("danger",  _("Failed to $_POST[action] device type").'!', false); }
else 																			{ $Result->show("success", _("Device type $_POST[action] successfull").'!', false); }

?>
