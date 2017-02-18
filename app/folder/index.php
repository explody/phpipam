<?php




# must be numeric
if(!is_numeric($_GET['subnetId']))	{ $Result->show("danger", _("Invalid ID"), true); }
if(!is_numeric($_GET['section']))	{ $Result->show("danger", _("Invalid ID"), true); }

# save folder ID
$folderId = $_GET['subnetId'];

# get custom subnet fields
$cfs = $Tools->fetch_custom_fields ('subnets');

# fetch subnet details!
$folder	= $Subnets->fetch_subnet ("id", $folderId);
if($folder==false) 				{ header("Location: ".create_link("subnets", $_GET['section'])); die(); }	//redirect if false

// to array
$folder = (array) $folder;

# permissions
$folder_permission  = $Subnets->check_permission($User->user, $folder['id']);						//subnet permission
$folder_permission_section = $Sections->check_permission($User->user, $folder['sectionId']);				//section permission
if($folder_permission == 0)			{ $Result->show("danger", _('You do not have permission to access this network'), true); }

# get all slaves and addresses
$slaves = $Subnets->fetch_subnet_slaves ($folderId);
$addresses = $Addresses->fetch_subnet_addresses ($folder['id'], $sort['field'], $sort['direction']);

# print Folder details
print "<div class='subnetDetails'>";
include_once("folder-details.php");
print "</div>";

# Subnets in Folder
if (!empty($slaves)) {
    print '<div class="ipaddresses_overlay">';
    include_once('folder-subnets.php');
    print '</div>';
}

# search for IP addresses in Folder
if (!empty($addresses)) {
    // set subnet
    $subnet = $folder;
    $subnet_permission = $folder_permission;
    $location = "folder";
    $User->user->hideFreeRange=1;
    $slaves = false;
    // print
    print '<div class="ipaddresses_overlay">';
    include_once(APP . '/subnets/addresses/print-address-table.php');
    print '</div>';
}

# empty
if (empty($addresses) && empty($slaves)) {
    print "<hr>";
    $Result->show("info alert-absolute", _("Folder is empty"), false);
}

?>