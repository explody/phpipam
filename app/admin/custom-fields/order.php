<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

# some verifications
if( (empty($_POST['current'])) || (empty($_POST['next'])) ) 							{ $Result->show("danger", _('Fileds cannot be empty')."!", true); }


/* reorder */
if(!$Admin->reorder_custom_fields($_POST['table'], $_POST['next'], $_POST['current'])) 	{ $Result->show("danger", _('Reordering failed')."!", true); }
else 																					{ $Result->show("success", _('Fields reordered successfully')."!");}

?>
