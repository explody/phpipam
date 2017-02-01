<?php

/**
 * Script to edit / add / delete groups
 *************************************************/

# verify that user is logged in
$User->check_user_session();

# print subnets
include(APP . "/tools/dhcp/subnets.php");
?>