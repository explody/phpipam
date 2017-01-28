<?php

/**
 * Script to print racks
 ***************************/

# verify that user is logged in
$User->check_user_session();

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('racks');

# all racks or one ?
if (isset($_GET['subnetId'])) {
    include(APP . "/tools/racks/print-single-rack.php");
} else {
    include(APP . "/tools/racks/print-racks.php");
}
