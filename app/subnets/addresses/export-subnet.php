<?php

/**
 *	Generate XLS file for subnet
 *********************************/

# we dont need any errors!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

# fetch subnet details
$subnet = (array) $Tools->fetch_object ("subnets", "id", $_GET['subnetId']);
# fetch all IP addresses in subnet
$addresses = $Addresses->fetch_subnet_addresses ($_GET['subnetId'], "ip_addr", "asc");
# get all custom fields
$cfs = $Tools->fetch_custom_fields ('ipaddresses');


# Create a workbook
$filename = isset($_GET['filename'])&&strlen(@$_GET['filename'])>0 ? $_GET['filename'] : "phpipam_subnet_export.xls";
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8);

//formatting headers
$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setColor('black');
$format_header->setSize(12);

//format vlan
$format_vlan =& $workbook->addFormat();
$format_vlan->setColor('black');
$format_vlan->setSize(11);


//formatting titles
$format_title =& $workbook->addFormat();
$format_title->setColor('black');
$format_title->setFgColor(22);			//light gray
$format_title->setBottom(1);
$format_title->setTop(1);
$format_title->setAlign('left');

//formatting content - borders around IP addresses
$format_right =& $workbook->addFormat();
$format_right->setRight(1);
$format_left =& $workbook->addFormat();
$format_left->setLeft(1);
$format_top =& $workbook->addFormat();
$format_top->setTop(1);


// Create a worksheet
$worksheet_name = strlen($subnet['description']) > 30 ? substr($subnet['description'],0,27).'...' : $subnet['description'];
$worksheet =& $workbook->addWorksheet($worksheet_name);
$worksheet->setInputEncoding("utf-8");

$lineCount = 0;
$rowCount  = 0;

# Write title - subnet details
$worksheet->write($lineCount, $rowCount, $subnet['description'], $format_header );
$lineCount++;
$worksheet->write($lineCount, $rowCount, $Subnets->transform_address($subnet['subnet'],"dotted") . "/" .$subnet['mask'], $format_header );
$lineCount++;

# write VLAN details
$vlan = $Tools->fetch_object("vlans", "vlanId", $subnet['vlanId']);
if($vlan!=false) {
	$vlan = (array) $vlan;
	$vlan_text = strlen($vlan['name'])>0 ? "vlan: $vlan[number] - $vlan[name]" : "vlan: $vlan[number]";

	$worksheet->write($lineCount, $rowCount, $vlan_text, $format_vlan );
	$lineCount++;
}
$lineCount++;

//set row count
$rowCount = 0;

//write headers
if( (isset($_GET['ip_addr'])) && ($_GET['ip_addr'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('ip address') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['state'])) && ($_GET['state'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('ip state') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('description') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['dns_name'])) && ($_GET['dns_name'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('hostname') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['firewallAddressObject'])) && ($_GET['firewallAddressObject'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('fw object') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['mac'])) && ($_GET['mac'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('mac') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['owner'])) && ($_GET['owner'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('owner') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['device'])) && ($_GET['device'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('device') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['port'])) && ($_GET['port'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('port') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['note'])) && ($_GET['note'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('note') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['location'])) && ($_GET['location'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('location') ,$format_title);
	$rowCount++;
}

//custom
foreach($cfs as $cf) {
	if( (isset($_GET[$cf->name])) && ($_GET[$cf->name] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $cf->name ,$format_title);
		$rowCount++;
	}
}

$lineCount++;

//we need to reformat state!
$ip_types = $Addresses->addresses_types_fetch();
//fetch devices and reorder
$devices = $Tools->fetch_all_objects("devices", "hostname");
$devices_indexed = array();
if ($devices!==false) {
	foreach($devices as $d) {
		$devices_indexed[$d->id] = (object) $d;
	}
}
//add blank
$devices_indexed[0] = new StdClass ();
$devices_indexed[0]->hostname = 0;

//fetch locations and reorder
$locations = $Tools->fetch_all_objects("locations", "id");
$locations_indexed = array();
if ($locations!==false) {
	foreach($locations as $d) {
		$locations_indexed[$d->id] = (object) $d;
	}
}
//add blank
$locations_indexed[0] = new StdClass ();
$locations_indexed[0]->name = 0;

//write all IP addresses
foreach ($addresses as $ip) {
	$ip = (array) $ip;

	//reset row count
	$rowCount = 0;

	//change switch ID to name
	$ip['device']   = is_null($ip['device'])||strlen($ip['device'])==0||$ip['device']==0||!isset($devices_indexed[$ip['device']]) ? "" : $devices_indexed[$ip['device']]->hostname;
	$ip['location'] = is_null($ip['location'])||strlen($ip['location'])==0||$ip['location']==0||!isset($locations_indexed[$ip['location']]) ? "" : $locations_indexed[$ip['location']]->name;

	if( (isset($_GET['ip_addr'])) && ($_GET['ip_addr'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $Subnets->transform_address($ip['ip_addr'],"dotted"), $format_left);
		$rowCount++;
	}
	if( (isset($_GET['state'])) && ($_GET['state'] == "on") ) {
		if(@$ip_types[$ip['state']]['showtag']==1) {
		$worksheet->write($lineCount, $rowCount, $ip_types[$ip['state']]['type']);
		}
		$rowCount++;
	}
	if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['description']);
		$rowCount++;
	}
	if( (isset($_GET['dns_name'])) && ($_GET['dns_name'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['dns_name']);
		$rowCount++;
	}
	if( (isset($_GET['firewallAddressObject'])) && ($_GET['firewallAddressObject'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['firewallAddressObject']);
		$rowCount++;
	}
	if( (isset($_GET['mac'])) && ($_GET['mac'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['mac']);
		$rowCount++;
	}
	if( (isset($_GET['owner'])) && ($_GET['owner'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['owner']);
		$rowCount++;
	}
	if( (isset($_GET['device'])) && ($_GET['device'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['device']);
		$rowCount++;
	}
	if( (isset($_GET['port'])) && ($_GET['port'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['port']);
		$rowCount++;
	}
	if( (isset($_GET['note'])) && ($_GET['note'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['note']);
		$rowCount++;
	}
	if( (isset($_GET['location'])) && ($_GET['location'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['location']);
		$rowCount++;
	}

	//custom
	foreach($cfs as $cf) {
		if( (isset($_GET[$cf->name])) && ($_GET[$cf->name] == "on") ) {
			$worksheet->write($lineCount, $rowCount, $ip[$cf->name]);
			$rowCount++;
		}
	}


	$lineCount++;
}


//new line
$lineCount++;

// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>
