<?php

/**
 *	Mail settings
 **************************/

# validate csrf cookie
$User->csrf_validate("mail", $_POST['csrf_cookie'], $Result);

# set update query
$values = array("id"=>1,
				"mtype"=>$_POST['mtype'],
				"msecure"=>@$_POST['msecure'],
				"mauth"=>@$_POST['mauth'],
				"mserver"=>@$_POST['mserver'],
				"mport"=>@$_POST['mport'],
				"muser"=>@$_POST['muser'],
				"mpass"=>@$_POST['mpass'],
				"mAdminName"=>@$_POST['mAdminName'],
				"mAdminMail"=>@$_POST['mAdminMail']
				);

# update
if(!$Admin->object_modify("settingsMail", "edit", "id", $values))	{ $Result->show("danger",  _('Cannot update settings').'!', true); }
else																{ $Result->show("success", _('Settings updated successfully')."!", true); }
?>
