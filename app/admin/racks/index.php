<?php

/**
 * Script to print racks
 ***************************/

# verify that user is logged in
$User->check_user_session();

# fetch custom fields
$custom = $Tools->fetch_custom_fields('racks');

# get hidden fields
$hidden_custom_fields = json_decode($User->settings->hiddenCustomFields, true);
$hidden_custom_fields = is_array(@$hidden_custom_fields['racks']) ? $hidden_custom_fields['racks'] : array();


# all racks or one ?
if (isset($_GET['subnetId'])) {
    include(APP . "/tools/racks/print-single-rack.php");
} else {
    include(APP . "/tools/racks/print-racks.php");
}
