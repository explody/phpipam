<?php

/**
 *	Generate XLS file for VRFs
 *********************************/
$Tools->csrf_validate($csrf, $Result);

# fetch all vrfs
$all_vrfs = $Admin->fetch_all_objects("vrf", "vrfId");
if (!$all_vrfs) { $all_vrfs = array(); }

# Create a workbook
$today = date("Ymd");
$filename = $today."_phpipam_VRF_export.xls";
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
$worksheet_name = "VRFs";
$worksheet =& $workbook->addWorksheet($worksheet_name);
$worksheet->setInputEncoding("utf-8");

$lineCount = 0;
$rowCount = 0;

//write headers
if( (isset($_GET['name'])) && ($_GET['name'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Name') ,$format_header);
	$rowCount++;
}
if( (isset($_GET['rd'])) && ($_GET['rd'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('RD') ,$format_header);
	$rowCount++;
}
if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('Description') ,$format_header);
	$rowCount++;
}

$lineCount++;

//write all VRF entries
foreach ($all_vrfs as $vrf) {
	//cast
	$vrf = (array) $vrf;

	//reset row count
	$rowCount = 0;

	if( (isset($_GET['name'])) && ($_GET['name'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $vrf['name'], $format_text);
		$rowCount++;
	}
	if( (isset($_GET['rd'])) && ($_GET['rd'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $vrf['rd'], $format_text);
		$rowCount++;
	}
	if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $vrf['description'], $format_text);
		$rowCount++;
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
