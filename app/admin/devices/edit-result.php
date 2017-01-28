<?php

/**
 * Edit switch result
 ***************************/

/* functions */
require( dirname(__FILE__) . '/../../../functions/functions.php');

# validate csrf cookie
$User->csrf_validate("device", $_POST['csrf_cookie'], $Result);

# get modified details
$device = $Admin->strip_input_tags($_POST);

# ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['switchId']))			{ $Result->show("danger", _("Invalid ID"), true); }

# Multiselect transitional support 
if (array_key_exists('sections', $_POST)) {
    $device['sections'] = sizeof($_POST['sections'])>0 ? implode(";", $_POST['sections']) : null;
} else {

    foreach($device as $key=>$line) {
    	if (strlen(strstr($key,"section-"))>0) {
    		$key2 = str_replace("section-", "", $key);
    		$temp[] = $key2;
    		unset($device[$key]);
    	}
    }
    # glue sections together
    $device['sections'] = sizeof($temp)>0 ? implode(";", $temp) : null;

}

# Hostname must be present
if($device['hostname'] == "") 											{ $Result->show("danger", _('Hostname is mandatory').'!', true); }

# rack checks
if (strlen(@$device['rack']>0)) {
    if ($User->settings->enableRACK!="1") {
        unset($device['rack']);
    }
    else {
        # validate position and size
        if (!is_numeric($device['rack']))                               { $Result->show("danger", _('Invalid rack identifier').'!', true); }
        if (!is_numeric($device['rack_start']))                         { $Result->show("danger", _('Invalid rack start position').'!', true); }
        if (!is_numeric($device['rack_size']))                          { $Result->show("danger", _('Invalid rack size').'!', true); }
        # validate rack
        $rack = $Racks->fetch_rack_details ($device['rack']);
        if ($rack===false)                                              { $Result->show("danger", _('Rack does not exist').'!', true); }
    }
}

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('devices');

foreach($cfs as $cf) {
	//not null!
	if(!$cf->null && strlen($device[$cf->name])==0) { 
        $Result->show("danger", $cf->name.'" can not be empty!', true);
    }

	# save to update array
	$update[$cf->name] = $device[$cf->name];
}


# set update values
$values = array("id"=>@$device['switchId'],
				"hostname"=>@$device['hostname'],
				"ip_addr"=>@$device['ip_addr'],
				"type"=>@$device['type'],
				"vendor"=>@$device['vendor'],
				"model"=>@$device['model'],
				"version"=>@$device['version'],
				"description"=>@$device['description'],
				"sections"=>@$device['sections'],
				"location"=>@$device['location_item']
				);
# custom fields
if(isset($update)) {
	$values = array_merge($values, $update);
}
# rack
if (strlen(@$device['rack']>0)) {
    $values['rack'] = $device['rack'];
    $values['rack_start'] = $device['rack_start'];
    $values['rack_size']  = $device['rack_size'];

}

# update device
if(!$Admin->object_modify("devices", $_POST['action'], "id", $values)) {    
} else { 
    $Result->show("success", _("Device $device[action] successfull").'!', false); 
}

if($_POST['action']=="delete"){
	# remove all references from subnets and ip addresses
	$Admin->remove_object_references ("subnets", "device", $values["id"]);
	$Admin->remove_object_references ("ipaddresses", "switch", $values["id"]);
	$Admin->remove_object_references ("pstnPrefixes", "deviceId", $values["id"]);
	$Admin->remove_object_references ("pstnNumbers", "deviceId", $values["id"]);
}

?>
