<?php

/**
 * print subnet masks
 */

# verify that user is logged in
$User->check_user_session();

// set popup
$popup = false;
// table
include(dirname(__FILE__) . '/print-table.php');
?>