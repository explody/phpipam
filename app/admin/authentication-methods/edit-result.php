<?php

/**
 *	Edit authentication method
 *
 */

# validate csrf cookie
$User->csrf_cookie ("validate", "authmethods", $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";

# get action
$action = $_POST['action'];

//for adding remove id
if($action=="add") {
	unset($_POST['id']);
}
else {
	//check id
	if(!is_numeric($_POST['id']))	{ $Result->show("danger", _("Invalid ID"), true); }
}

# set update query
$values = array("id"=>@$_POST['id'],
				"type"=>$_POST['type'],
				"description"=>@$_POST['description'],
				);
# add params
unset($_POST['id'], $_POST['type'], $_POST['description'], $_POST['action'], $_POST['csrf_cookie']);

# Unset empty values to keep from cluttering authmethod settings
foreach ($_POST as $k => $v) {
    if (empty($v)) {
        unset($_POST[$k]);
    }
}

$values["params"]=json_encode($_POST);

# add - set protected
if($action=="add") {
	$values['protected'] = "No";
}

# update
if(!$Admin->object_modify("usersAuthMethod", $action, "id", $values))	{ $Result->show("danger",  _("Failed to edit authentication method"), false); }
else																	{ $Result->show("success", _("Authentication method updated"), false); }

# if delete also reset all users that have thos auth method
if($action=="delete") {
	$Database->runQuery("update `users` set `authMethod`=1 where `authMethod`= ?;", array($values['id']));
}
?>
