<?php

/***
 *	Generate XLS file for VLANs
 *********************************/
 
$User->csrf_validate("export-vlans", $_GET['csrf_cookie'], $Result);

# fetch all l2 domains
$vlan_domains = $Admin->fetch_all_objects("vlanDomains", "id");

# get all custom fields
$cfs = $Tools->fetch_custom_fields('vlans');

# Create a workbook
$today = date("Ymd");
$filename = $today."_phpipam_VLAN_export.xls";
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8);

//formatting headers
$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setColor('black');
$format_header->setSize(12);
$format_header->setAlign('left');

//formatting content
$format_text =& $workbook->addFormat();

// Create a worksheet
$worksheet_name = "VLANs";
$worksheet =& $workbook->addWorksheet($worksheet_name);
$worksheet->setInputEncoding("utf-8");

$lineCount = 0;
$rowCount = 0;

//write headers
if( (isset($_GET['name'])) && ($_GET['name'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Name') ,$format_header);
	$rowCount++;
}
if( (isset($_GET['number'])) && ($_GET['number'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Number') ,$format_header);
	$rowCount++;
}
if( (isset($_GET['domain'])) && ($_GET['domain'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Domain') ,$format_header);
	$rowCount++;
}
if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Description') ,$format_header);
	$rowCount++;
}

//custom fields
foreach($cfs as $cf) {
	if( (isset($_GET[$cf->name])) && ($_GET[$cf->name] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $cf->name ,$format_header);
		$rowCount++;
	}
}


$lineCount++;

//write Subnet entries for the selected sections
foreach ($vlan_domains as $vlan_domain) {
	//cast
	$vlan_domain = (array) $vlan_domain;

	if( (isset($_GET['exportDomain__'.str_replace(" ", "_",$vlan_domain['name'])])) && ($_GET['exportDomain__'.str_replace(" ", "_",$vlan_domain['name'])] == "on") ) {
		// get all VLANs in VLAN domain
		$all_vlans = $Admin->fetch_multiple_objects("vlans", "domainId", $vlan_domain['id'], "number");
		$all_vlans = (array) $all_vlans;
		// skip empty domains
		if (sizeof($all_vlans)==0) { continue; }
		//write all VLAN entries
		foreach ($all_vlans as $vlan) {
			//cast
			$vlan = (array) $vlan;

			//reset row count
			$rowCount = 0;

			if( (isset($_GET['name'])) && ($_GET['name'] == "on") ) {
				$worksheet->write($lineCount, $rowCount, $vlan['name'], $format_text);
				$rowCount++;
			}
			if( (isset($_GET['number'])) && ($_GET['number'] == "on") ) {
				$worksheet->write($lineCount, $rowCount, $vlan['number'], $format_text);
				$rowCount++;
			}
			if( (isset($_GET['domain'])) && ($_GET['domain'] == "on") ) {
				$worksheet->write($lineCount, $rowCount, $vlan_domain['name'], $format_text);
				$rowCount++;
			}
			if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
				$worksheet->write($lineCount, $rowCount, $vlan['description'], $format_text);
				$rowCount++;
			}

			//custom fields, per VLAN

			foreach($cfs as $cf) {
				if( (isset($_GET[$cf->name])) && ($_GET[$cf->name] == "on") ) {
					$worksheet->write($lineCount, $rowCount, $vlan[$cf->name], $format_text);
					$rowCount++;
				}
			}


			$lineCount++;
		}
	}
}

//new line
$lineCount++;

//write domain sheet
if( (isset($_GET['exportVLANDomains'])) && ($_GET['exportVLANDomains'] == "on") ) {
	// Create a worksheet
	$worksheet_domains =& $workbook->addWorksheet('Domains');

	$lineCount = 0;
	$rowCount = 0;

	//write headers
	$worksheet_domains->write($lineCount, $rowCount, _('Name') ,$format_header);
	$rowCount++;
	$worksheet_domains->write($lineCount, $rowCount, _('Description') ,$format_header);
	$rowCount++;

	$lineCount++;
	$rowCount = 0;

	foreach ($vlan_domains as $vlan_domain) {
		//cast
		$vlan_domain = (array) $vlan_domain;

		if( (isset($_GET['exportDomain__'.str_replace(" ", "_",$vlan_domain['name'])])) && ($_GET['exportDomain__'.str_replace(" ", "_",$vlan_domain['name'])] == "on") ) {
			$worksheet_domains->write($lineCount, $rowCount, $vlan_domain['name'], $format_text);
			$rowCount++;
			$worksheet_domains->write($lineCount, $rowCount, $vlan_domain['description'], $format_text);
			$rowCount++;
		}

		$lineCount++;
		$rowCount = 0;
	}
}

// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>
