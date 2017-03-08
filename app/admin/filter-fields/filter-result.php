<?php

/**
 * Script to get all active IP requests
 ****************************************/

$Tools->csrf_validate($csrf, $Result);

# set fields to update
$values = array("id"=>1,
				"IPfilter"=>implode(';', $_POST));

# update
if(!$Admin->object_modify("settings", "edit", "id", $values))   { $Result->show("danger alert-absolute",  _("Update failed"), true); }
else															{ $Result->show("success alert-absolute", _('Update successfull'), true); }

?>
