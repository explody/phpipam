<?php

/**
 * Script to draw rack
 ***************************/

# init racks object
$Racks = new phpipam_rack ($Database);

# deviceId not set or empty - set to 0
if (empty($_GET['deviceId']))      { $_GET['deviceId'] = 0; }

# validate rackId
if (!is_numeric($_GET['rackId']))     { die(); }
if (!is_numeric($_GET['deviceId']))   { die(); }

# draw
$Racks->draw_rack ($_GET['rackId'],$_GET['deviceId']);