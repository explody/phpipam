<?php

/* Edit favourite subnets */

# checks
is_numeric($_POST['subnetId']) ? : $Result->show("danger", _('Invalid ID'),false, true);

# execute action
if(!$User->edit_favourite($_POST['action'], $_POST['subnetId'])) 	{ $Result->show("danger", _('Error editing favourite'),false, true); }
else 																{ print "success"; }
?>
