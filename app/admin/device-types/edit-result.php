<?php

/**
 * Edit device result
 ***************************/

/* functions */
require( dirname(__FILE__) . '/../../../functions/functions.php');

# initialize user object
$Database 	= new Database_PDO;
$User 		= new User ($Database);
$Admin	 	= new Admin ($Database);
$Result 	= new Result ();

# verify that user is logged in
$User->check_user_session();

# strip input tags
$_POST = $Admin->strip_input_tags($_POST);

# validate csrf cookie
$User->csrf_cookie ("validate", "device_types", $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";

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