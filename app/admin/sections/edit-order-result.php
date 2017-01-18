<?php

/**
 * Function to add / edit / delete section
 *************************************************/

# create array of ordering
$otmp = explode(";", $_POST['position']);
foreach($otmp as $ot) {
	$ptmp = explode(":", $ot);
	$order[$ptmp[0]] = $ptmp[1];
}

#update
if(!$Sections->modify_section ("reorder", $order))	{ $Result->show("danger",  _("Section reordering failed"), true); }
else												{ $Result->show("success", _("Section reordering successful"), true); }
?>
