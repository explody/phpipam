<?php




# Make sure user is admin
$User->is_admin(true);

# show all nat objects
include(dirname(__FILE__)."/../../tools/pstn-prefixes/index.php");
?>