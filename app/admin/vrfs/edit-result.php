<?php

/**
 * Script to edit VRF
 ***************************/

# make sue user can edit
if ($User->is_admin(false)==false && $User->user->editVlan!="Yes") {
    $Result->show("danger", _("Not allowed to change VRFs"), true, true);
}

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);

# Hostname must be present!
if($_POST['name'] == "") { $Result->show("danger", _("Name is mandatory"), true); }

$sections = '';

# Multiselect transitional support 
if (array_key_exists('sections', $_POST)) {
    $sections = sizeof($_POST['sections'])>0 ? implode(";", $_POST['sections']) : null;
} else {

    foreach($_POST as $key=>$line) {
    	if (strlen(strstr($key,"section-"))>0) {
    		$key2 = str_replace("section-", "", $key);
    		$temp[] = $key2;
    		unset($_POST[$key]);
    	}
    }
    # glue sections together
    $sections = sizeof($temp)>0 ? implode(";", $temp) : null;

}

# set update array
$values = array("vrfId"=>@$_POST['vrfId'],
				"name"=>$_POST['name'],
				"rd"=>$_POST['rd'],
				"sections"=>$sections,
				"description"=>$_POST['description']
				);

# append custom
foreach($Tools->fetch_custom_fields('vrf') as $cf) {
	if(isset($_POST[$cf->name])) { $values[$cf->name] = @$_POST[$cf->name];}
}

# update
if(!$Admin->object_modify("vrf", $_POST['action'], "vrfId", $values))	{ $Result->show("danger",  _("Failed to $_POST[action] VRF").'!', true); }
else																	{ $Result->show("success", _("VRF $_POST[action] successfull").'!', false); }


# remove all references if delete
if($_POST['action']=="delete") { $Admin->remove_object_references ("subnets", "vrfId", $_POST['vrfId']); }
?>
