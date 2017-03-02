<?php

/**
 *	Site settings
 **************************/

/* functions */
require( dirname(__FILE__) . '/../../../functions/functions.php');

# initialize user object
$Database 	= new Database_PDO;
$User 		= new User ($Database);
$Admin	 	= new Admin ($Database);
$Result 	= new Result ();




# set update values
$values = array("id"=>1,
				"maintenanceMode" => 0
				);
if(!$Admin->object_modify("settings", "edit", "id", $values))	{ $Result->show("danger",  _("Cannot update settings"), true, true, false, false, true); }
else															{ $Result->show("success", _("Maintenance mode removed"), true, true, false, false, true); }