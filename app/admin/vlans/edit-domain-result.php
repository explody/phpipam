<?php

/**
 * Edit switch result
 ***************************/

# validate csrf cookie
$User->csrf_cookie ("validate", "vlan_domain", $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";

# we cannot delete default domain
if(@$_POST['id']==1 && $_POST['action']=="delete")						{ $Result->show("danger", _("Default domain cannot be deleted"), true); }
// ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['id']))				{ $Result->show("danger", _("Invalid ID"), true); }
// Hostname must be present
if(@$_POST['name'] == "") 												{ $Result->show("danger", _('Name is mandatory').'!', true); }


# TODO: DRY - most forms that use multi-select.

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

# set update values
$values = array("id"=>@$_POST['id'],
				"name"=>@$_POST['name'],
				"description"=>@$_POST['description'],
				"permissions"=>$sections,
				);

# update domain
if(!$Admin->object_modify("vlanDomains", $_POST['action'], "id", $values))	{}
else																		{ $Result->show("success", _("Domain $_POST[action] successfull").'!', false); }

# if delete move all vlans to default domain!
if($_POST['action']=="delete") {
	$Admin->update_object_references ("vlans", "domainId", $_POST['id'], 1);
}

?>
