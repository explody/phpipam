<?php

/**
 * Script to edit / add / delete groups
 *************************************************/

# verify that user is logged in
$User->check_user_session();

# print leases
include(APP . "/tools/dhcp/leases.php");
?>