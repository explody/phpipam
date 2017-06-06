<?php

/**
 * Script to display devices
 *
 */





if(isset($_GET['id'])) {
    # check
    is_numeric($_GET['id']) ? : $Result->show("danger", _("Invalid ID: " + $_GET['id']), true);

    # fetch device
    $device = $Tools->fetch_object ("devices", "id", $_GET['id']);

    # count subnets and addresses
    $cnt_subnets   = $Tools->count_database_objects ("subnets", "device", $device->id);
    $cnt_addresses = $Tools->count_database_objects ("ipaddresses", "device", $device->id);
    $cnt_nat       = $Tools->count_database_objects ("nat", "device", $device->id);
    $cnt_pstn      = $Tools->count_database_objects ("pstnPrefixes", "deviceId", $device->id);

    ?>
    <!-- tabs -->
    <ul class='nav nav-tabs' style='margin-bottom:20px;'>
        <li role='presentation' <?php if(!isset($_GET['sPage'])) print " class='active'"; ?>> 
            <a href='<?php print create_link("tools", "devices", $device->id); ?>'><?php print _("Device details"); ?></a>
        </li>
        <li role='presentation' <?php if(@$_GET['sPage']=="subnets") print "class='active'"; ?>>
            <a href='<?php print create_link("tools", "devices", $device->id, "subnets", $subnet['id']); ?>'><?php print _("Subnets"); ?> 
                <span class='badge' style="margin-left: 5px;"><?php print $cnt_subnets; ?></span>
            </a>
        </li>
        <li role='presentation' <?php if(@$_GET['sPage']=="addresses") print "class='active'"; ?>>
            <a href='<?php print create_link("tools", "devices", $device->id, "addresses", $subnet['id']); ?>'><?php print _("Addresses"); ?>
                <span class='badge' style="margin-left: 5px;"><?php print $cnt_addresses; ?></span>
            </a>
        </li>
        <?php if($User->settings->enableNAT==1) { ?>
        <li role='presentation' <?php if(@$_GET['sPage']=="nat") print "class='active'"; ?>>
            <a href='<?php print create_link("tools", "devices", $device->id, "nat", $subnet['id']); ?>'><?php print _("NAT"); ?>
                <span class='badge' style="margin-left: 5px;"><?php print $cnt_nat; ?></span>
            </a>
        </li>
        <?php } ?>
        <?php if($User->settings->enableLocations==1) { ?>
        <li role='presentation' <?php if(@$_GET['sPage']=="location") print "class='active'"; ?>>
            <a href='<?php print create_link("tools", "devices", $device->id, "location"); ?>'><?php print _("Location"); ?></a>
        </li>
        <?php } ?>
        <?php if($User->settings->enablePSTN==1) { ?>
        <li role='presentation' <?php if(@$_GET['sPage']=="pstn-prefixes") print "class='active'"; ?>>
            <a href='<?php print create_link("tools", "devices", $device->id, "pstn-prefixes"); ?>'><?php print _("PSTN prefixes"); ?>
                <span class='badge' style="margin-left: 5px;"><?php print $cnt_pstn; ?></span>
            </a>
        </li>
        <?php } ?>

    </ul>

    <!-- details -->
    <?php
    if(!isset($_GET['sPage'])) {
    	include(dirname(__FILE__) . "/device-details/device-details.php");
    }
    elseif(@$_GET['sPage']=="subnets") {
        include(dirname(__FILE__) . "/device-details/device-subnets.php");
    }
    elseif(@$_GET['sPage']=="addresses") {
        include(dirname(__FILE__) . "/device-details/device-addresses.php");
    }
    elseif($User->settings->enableNAT==1 && @$_GET['sPage']=="nat") {
        include(dirname(__FILE__) . "/device-details/device-nat.php");
    }
    elseif(@$_GET['sPage']=="location") {
        include(dirname(__FILE__) . "/device-details/device-location.php");
    }
    elseif(@$_GET['sPage']=="pstn-prefixes") {
        include(dirname(__FILE__) . "/device-details/device-pstn.php");
    }
} else {
	include(dirname(__FILE__) . '/all-devices.php');
}

?>