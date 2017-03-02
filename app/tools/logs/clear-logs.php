<?php

/**
 *	clear log files
 **********************************/

# truncate logs table
if(!$Admin->truncate_table("logs")) 	{ $Result->show("danger",  _('Error clearing logs')."!", true); }
else 									{ $Result->show("success", _('Logs cleared successfully')."!", true); }
?>
