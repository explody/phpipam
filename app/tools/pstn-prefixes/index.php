<?php

/**
 * Script to print locations
 ***************************/




# set admin
$admin = $User->is_admin(false);

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('pstnPrefixes');

# check that prefix support isenabled
if ($User->settings->enablePSTN!="1") {
    $Result->show("danger", _("PSTN prefixes module disabled."), false);
}
else {
    # all prefixes
    if(!isset($_GET['subnetId'])) {
        include(dirname(__FILE__) . "/all-prefixes.php");
    }
    # single prefixes
    else {
        # slaves ?
        $cnt = $Tools->count_database_objects("pstnPrefixes", "master",$_GET['subnetId']);
        if ($cnt == 0) {
            include(dirname(__FILE__) . "/single-prefix.php");
        }
        else {
            include(dirname(__FILE__) . "/single-prefix-slaves.php");
        }
    }
}
?>