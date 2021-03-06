<?php

/**
 *	Script to replace fields in IP address list
 ***********************************************/

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);

//verify post
if(empty($_POST['search'])) { $Result->show("danger", _('Please enter something in search field').'!', true); }
//if device verify that it exists
if($_POST['field'] == "device") {
	if(!$device1 = $Admin->fetch_object("devices", "hostname", $_POST['search']))	{ $Result->show("danger  alert-absolute", _('Device').' "<i>'. $_POST['search']  .'</i>" '._('does not exist, first create switch under admin menu').'!', true); }
	if(!$device2 = $Admin->fetch_object("devices", "hostname", $_POST['replace']))	{ $Result->show("danger  alert-absolute", _('Device').' "<i>'. $_POST['search']  .'</i>" '._('does not exist, first create switch under admin menu').'!', true); }

	//replace posts
	$_POST['search']  = $device1->id;
	$_POST['replace'] = $device2->id;
}

# update
$Admin->replace_fields ($_POST['field'], $_POST['search'], $_POST['replace']);
?>
