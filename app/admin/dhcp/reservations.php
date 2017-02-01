<?php


# verify that user is logged in
$User->check_user_session();


# print reservations
include(APP . "/tools/dhcp/reservations.php");
?>