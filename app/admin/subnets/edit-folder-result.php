<?php

/**
 * Function to add / edit / delete folder
 ********************************************/
 
$action = $_POST['action'];

# ID must be numeric
if($action == "add") {
	if(!is_numeric($_POST['sectionId'])) {
        $Result->show("danger", _("Invalid ID"), true);
    }
} else {
	if(!is_numeric($_POST['subnetId'])) {
        $Result->show("danger", _("Invalid ID"), true);
    } else {
        $fid = $_POST['subnetId'];
    }
}

# validate csrf cookie
$User->csrf_validate("folder_${fid}_${action}", $_POST['csrf_cookie'], $Result);

# verify that user has permissions to add subnet
if($action=="add") {
	if($Sections->check_permission ($User->user, $_POST['sectionId']) != 3) { $Result->show("danger", _('You do not have permissions to add new subnet in this section')."!", true); }
}
# otherwise check subnet permission
else {
	if($Subnets->check_permission ($User->user, $fid) != 3) 	{ $Result->show("danger", _('You do not have permissions to add edit/delete this folder')."!", true); }
}

# we need old values for mailing
if($action=="edit" || $action=="delete") {
	$subnet_old_details = (array) $Subnets->fetch_subnet(null, $fid);
}

# get section details
$section = (array) $Sections->fetch_section(null, @$_POST['sectionId']);
# fetch custom fields
$cfs = $Tools->fetch_custom_fields('subnets');

foreach($cfs as $cf) {
	if(isset($_POST[$cf->name])) { $_POST[$cf->name] = $_POST[$cf->name];}
}

//remove subnet-specific fields
unset ($_POST['subnet'],$_POST['allowRequests'],$_POST['showName'],$_POST['pingSubnet'],$_POST['discoverSubnet']);
unset ($subnet_old_details['subnet'],$subnet_old_details['allowRequests'],$subnet_old_details['showName'],$subnet_old_details['pingSubnet'],$subnet_old_details['discoverSubnet']);

# Set permissions if adding new subnet
if($action=="add") {
	# root
	if($_POST['masterSubnetId']==0) {
		$_POST['permissions'] = $section['permissions'];
	}
	# nested - inherit parent permissions
	else {
		# get parent
		$parent = $Subnets->fetch_subnet(null, $_POST['masterSubnetId']);
		$_POST['permissions'] = $parent->permissions;
	}
}
elseif ($action=="edit") {
    /* for nesting - MasterId cannot be the same as subnetId! */
    if ( $_POST['masterSubnetId']==$fid ) {
    	$Result->show("danger", _('Folder cannot nest behind itself!'), true);
    }
}

//check for name length - 2 is minimum!
if(strlen($_POST['description'])<2 && $action!="delete") { $Result->show("danger", _('Folder name must have at least 2 characters')."!", true); }
//custom fields
if($action != "delete") {
	foreach($cfs as $cf) {
		//booleans can be only 0 and 1!
		if($cf->type=="boolean") {
			if(@$_POST[$cf->name]>1) {
				$_POST[$cf->name] = "";
			}
		}
		//not empty
		if(!$cf->null && strlen($_POST[$cf->name])==0) {
			$errors[] = "Field \"$cf->name\" cannot be empty!";
		}
	}
}

# delete and not yet confirmed
if ($action=="delete" && !isset($_POST['deleteconfirm'])) {
	# for ajax to prevent reload
	print "<div style='display:none'>alert alert-danger</div>";
	# result
	print "<div class='alert alert-warning'>";

	# print what will be deleted
	//fetch all slave subnets
	$Subnets->fetch_subnet_slaves_recursive ($fid);
	$subcnt = sizeof($Subnets->slaves);
	foreach($Subnets->slaves as $s) {
		$slave_array[$s] = $s;
	}
	$ipcnt = $Addresses->count_addresses_in_multiple_subnets($slave_array);

	print "<strong>"._("Warning")."</strong>: "._("I will delete").":<ul>";
	print "	<li>$subcnt "._("subnets")."</li>";
	if($ipcnt>0) {
	print "	<li>$ipcnt "._("IP addresses")."</li>";
	}
	print "</ul>";

	print "<hr><div style='text-align:right'>";
	print _("Are you sure you want to delete above items?")." ";
	print "<div class='btn-group'>";
	print "	<a class='btn btn-sm btn-danger editFolderSubmitDelete' id='editFolderSubmitDelete' data-subnetId='".$fid."'>"._("Confirm")."</a>";
	print "</div>";
	print "</div>";
	print "</div>";
}
# execute
else {

	# create array of default update values
	$values = array(
					"id"             => @$fid,
					"isFolder"       => 1,
					"masterSubnetId" => $_POST['masterSubnetId'],
					"description"    => @$_POST['description'],
					"isFull"         => @$_POST['isFull']
					);
	# for new subnets we add permissions
	if($action=="add") {
		$values['permissions'] = $_POST['permissions'];
		$values['sectionId']   = $_POST['sectionId'];
	}
	else {
		# if section change
		if(@$_POST['sectionId'] != @$_POST['sectionIdNew']) {
			$values['sectionId'] = $_POST['sectionIdNew'];
		}
	}
    
	# append custom fields
	foreach($cfs as $cf) {

		if(isset($_POST[$cf->name])) { $_POST[$cf->name] = $_POST[$cf->name];}

		//booleans can be only 0 and 1!
		if($cf->type=="boolean") {
			if($_POST[$cf->name]>1) {
				$_POST[$cf->name] = 0;
			}
		}
		//not null!
		if ($action!="delete") {
			if(!$cf->null && strlen($_POST[$cf->name])==0) { $Result->show("danger", $cf->name.'" can not be empty!', true); }
        }

		# save to update array
		$values[$cf->name] = $_POST[$cf->name];
	}

	# execute
	if(!$Subnets->modify_subnet ($action, $values))	{ $Result->show("danger", _('Error editing folder'), true); }
	else {
		# update also all slave subnets!
		if(isset($values['sectionId']) && $action=="edit") {
			$Subnets->reset_subnet_slaves_recursive();
			$Subnets->fetch_subnet_slaves_recursive($fid);
			$Subnets->remove_subnet_slaves_master($fid);

			if(sizeof($Subnets->slaves)>0) {
				foreach($Subnets->slaves as $slaveId) {
					$Admin->object_modify ("subnets", "edit", "id", array("id"=>$slaveId, "sectionId"=>$values['sectionId']));
				}
			}
		}
		# delete
		elseif ($action=="delete") {
			$Subnets->reset_subnet_slaves_recursive();
			$Subnets->fetch_subnet_slaves_recursive($fid);
			$Subnets->remove_subnet_slaves_master($fid);

			if(sizeof($Subnets->slaves)>0) {
				foreach($Subnets->slaves as $slaveId) {
					$Admin->object_modify ("subnets", "delete", "id", array("id"=>$slaveId));
				}
			}
		}

		# edit success
		if($action=="delete")	{ $Result->show("success", _('Folder, IP addresses and all belonging subnets deleted successfully').'!', false); }
		else							{ $Result->show("success", _("Folder $_POST[action] successfull").'!', true); }
	}
}

?>
