<?php

/**
 * Script to print locations
 ***************************/

# verify that user is logged in
$User->check_user_session();

# set admin
$admin = $User->is_admin(false);

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('locations');

# check that location support isenabled
if ($User->settings->enableLocations!="1") {
    $Result->show("danger", _("Locations module disabled."), false);
}
else {
    # all locations
    if(!isset($_GET['subnetId'])) {
        include(dirname(__FILE__) . "/all-locations-list.php");
    }
    # map
    elseif ($_GET['subnetId']=="map") {
        include(dirname(__FILE__) . "/all-locations-map.php");
    }
    # single location
    else {
        include(dirname(__FILE__) . "/single-location.php");

    }
}
?>