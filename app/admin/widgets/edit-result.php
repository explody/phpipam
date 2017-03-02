<?php

/**
 * Script to display widget edit
 *************************************/

# validate csrf cookie
$User->csrf_validate("widget", $_POST['csrf_cookie'], $Result);


# ID must be numeric */
if($_POST['action']!="add") {
	if(!is_numeric($_POST['wid']))								{ $Result->show("danger", _("Invalid ID"), true); }
}
# Title and path must be present!
if($_POST['action']!="delete") {
if(strlen($_POST['wtitle'])==0 || strlen($_POST['wfile'])==0) 	{ $Result->show("danger", _("Filename and title are mandatory")."!", true); }
}

# Remove .php form wfile if it is present
$_POST['wfile'] = str_replace(".php","",trim(@$_POST['wfile']));

# set update values
$values = array("wid"=>@$_POST['wid'],
				"wtitle"=>$_POST['wtitle'],
				"wdescription"=>@$_POST['wdescription'],
				"wfile"=>$_POST['wfile'],
				"wadminonly"=>$_POST['wadminonly'],
				"wactive"=>$_POST['wactive'],
				"wparams"=>$_POST['wparams'],
				"whref"=>$_POST['whref'],
				"wsize"=>$_POST['wsize']
				);
# update
if(!$Admin->object_modify("widgets", $_POST['action'], "wid", $values))	{ $Result->show("danger",  _("Widget $_POST[action] error")."!", true); }
else																	{ $Result->show("success", _("Widget $_POST[action] success")."!", true); }
?>
