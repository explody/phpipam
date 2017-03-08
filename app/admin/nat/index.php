<?php

# Make sure user is admin
$User->is_admin(true);

# show all nat objects
include(APP . "/tools/nat/index.php");
?>