<?php

/**
 * Edit rack result
 ***************************/

$Racks      = new phpipam_rack ($Database);

# validate csrf cookie
$User->csrf_validate("rack", $_POST['csrf_cookie'], $Result);

# get modified details
$rack = $_POST;

# ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['rackid']))			{ $Result->show("danger", _("Invalid ID"), true); }

# Hostname must be present
if($rack['name'] == "") 											    { $Result->show("danger", _('Name is mandatory').'!', true); }

# rack checks
# validate position and size
if (!is_numeric($rack['size']))                                         { $Result->show("danger", _('Invalid rack size').'!', true); }
# validate rack
if ($rack['action']=="edit") {
    if (!is_numeric($rack['rackid']))                                       { $Result->show("danger", _('Invalid rack identifier').'!', true); }
    $rack_details = $Racks->fetch_rack_details ($rack['rackid']);
    if ($rack_details===false)                                          { $Result->show("danger", _('Rack does not exist').'!', true); }
}
elseif($rack['action']=="delete") {
    if (!is_numeric($rack['rackid']))                                       { $Result->show("danger", _('Invalid rack identifier').'!', true); }
}

foreach($Tools->fetch_custom_fields('racks') as $cf) {
	//booleans can be only 0 and 1!
	if($cf->type=="tinyint(1)") {
		if($rack[$cf->name]>1) {
			$rack[$cf->name] = 0;
		}
	}
	//not null!
	if(!$cf->null && strlen($rack[$cf->name])==0) {
         $Result->show("danger", $cf->name.'" can not be empty!', true);
	}
	# save to update array
	$update[$cf->name] = $rack[$cf->name];
}


# set update values
$values = array("id"=>@$rack['rackid'],
				"name"=>@$rack['name'],
				"size"=>@$rack['size'],
				"location"=>@$rack['location'],
				"description"=>@$rack['description']
				);
# custom fields
if(isset($update)) {
	$values = array_merge($values, $update);
}

# update rack
if(!$Admin->object_modify("racks", $_POST['action'], "id", $values))	{}
else																	{ $Result->show("success", _("Rack $rack[action] successfull").'!', false); }

if($_POST['action']=="delete"){
	# remove all references from subnets and ip addresses
	$Admin->remove_object_references ("devices", "rack", $values["id"]);
}

?>
