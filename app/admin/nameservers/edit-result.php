<?php

/**
 * Script to edit nameserver sets
 ***************************/

# validate csrf cookie
$User->csrf_validate("ns", $_POST['csrf_cookie'], $Result);


# Name and primary nameserver must be present!
if ($_POST['action']!="delete") {

	$m=1;
	$nservers_reindexed = array ();
	# reindex
	foreach($_POST as $k=>$v) {
		if(strpos($k, "namesrv-")!==false) {
			$nservers_reindexed["namesrv-".$m] = $v;
			$m++;
			unset($_POST[$k]);
		}
	}
	# join
	$_POST = array_merge($_POST, $nservers_reindexed);

	if($_POST['name'] == "") 				{ $Result->show("danger", _("Name is mandatory"), true); }
	if(trim($_POST['namesrv-1']) == "") 	{ $Result->show("danger", _("Primary nameserver is mandatory"), true); }
}

// merge nameservers
foreach($_POST as $key=>$line) {
	if (strlen(strstr($key,"namesrv-"))>0) {
		if (strlen($line)>0) {
			$all_nameservers[] = trim($line);
		}
	}
}
$_POST['namesrv1'] = isset($all_nameservers) ? implode(";", $all_nameservers) : "";

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
$values = array("id"=>@$_POST['nameserverId'],
				"name"=>$_POST['name'],
				"permissions"=>$sections,
				"namesrv1"=>$_POST['namesrv1'],
				"description"=>$_POST['description']
				);
# update
if(!$Admin->object_modify("nameservers", $_POST['action'], "id", $values))	{ $Result->show("danger",  _("Failed to $_POST[action] nameserver set").'!', true); }
else																		{ $Result->show("success", _("Nameserver set $_POST[action] successfull").'!', false); }


# remove all references if delete
if($_POST['action']=="delete") { $Admin->remove_object_references ("nameservers", "id", $_POST['nameserverId']); }
?>
