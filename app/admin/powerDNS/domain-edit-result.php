<?php

/**
 * Script to edit domain
 ***************************/

$PowerDNS 	= new PowerDNS ($Database);

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);


# checks / validation
if ($_POST['action']!="delete") {
	// fqdn
	if ($_POST['action']=="add")
	if($Result->validate_hostname($_POST['name'])===false)			{ $Result->show("danger", "Invalid domain name", true); }
	// master
	if (strlen($_POST['master'])>0) {
    	// if multilpe masters
    	if (strpos($_POST['master'], ",")!==false) {
        	// to array and trim, check each
        	$masters = array_filter(explode(",", $_POST['master']));
        	foreach ($masters as $m) {
                           if(!filter_var($m, FILTER_VALIDATE_IP))  { $Result->show("danger", "Master must be an IP address". " - ". $m, true); }
        	}
    	}
    	else {
            if(!filter_var($_POST['master'], FILTER_VALIDATE_IP))	{ $Result->show("danger", "Master must be an IP address". " - ". $_POST['master'], true); }
    	}
	}
	// type
	if(!in_array($_POST['type'], (array )$PowerDNS->domain_types))	{ $Result->show("danger", "Invalid domain type", true); }

	# new domain
	if ($_POST['action']=="add" && !isset($_POST['manual'])) {
		// admin
		if ($Result->validate_email($_POST['hostmaster'])===false)	{ $Result->show("danger", "Invalid domain hostmaster", true); }
	}

	// if slave master must be present
	if ($_POST['type']=="SLAVE") {
    	if (strlen($_POST['master'])==0)                            { $Result->show("danger", "Please set master server(s) if domain type is SLAVE", true); }
        else {
        	if (strpos($_POST['master'], ",")!==false) {
            	// to array and trim, check each
            	$masters = array_filter(explode(",", $_POST['master']));
            	foreach ($masters as $m) {
                               if(!filter_var($m, FILTER_VALIDATE_IP))  { $Result->show("danger", "Master must be an IP address". " - ". $m, true); }
            	}
        	}
        	else {
                if(!filter_var($_POST['master'], FILTER_VALIDATE_IP))	{ $Result->show("danger", "Master must be an IP address". " - ". $_POST['master'], true); }
        	}
    	}
	}


	# if update sve old domain !
	if ($_POST['action']=="edit") {
		$old_domain = $PowerDNS->fetch_domain ($_POST['id']);
	}
}

# set update array
$values = array("id"=>@$_POST['id'],
				"master"=>@$_POST['master'],
				"type"=>@$_POST['type']
				);
# name only on add
if ($_POST['action']=="add")
$values['name'] = $_POST['name'];


# remove all references if delete
if ($_POST['action']=="delete") 									{ $PowerDNS->remove_all_records ($values['id']); }

# for creation validate default records before creating them ! => true means check only
if ($_POST['action']=="add" && !isset($_POST['manual']))            { $PowerDNS->create_default_records ($_POST, true); }

# update
if(!$PowerDNS->domain_edit($_POST['action'], $values))				{ $Result->show("danger",  _("Failed to $_POST[action] domain").'!', true); }
else																{ $Result->show("success", _("Domain $_POST[action] successfull").'!', false); }

# create default records
if ($_POST['action']=="add" && !isset($_POST['manual']))			{ $PowerDNS->create_default_records ($_POST); }
?>
