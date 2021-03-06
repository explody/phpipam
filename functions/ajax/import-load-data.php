<?php
/*
 * Data import load
 *************************************************/

$expfields = explode("|",$_POST['expfields']);
$reqfields = explode("|",$_POST['reqfields']);
if (isset($_POST['filetype'])) {
	$filetype = $_POST['filetype'];
} else {
	$Result->show('danger', _("Error: could not read the uploaded file type!"), true, true);
}

# Load colors and icons
include 'import-constants.php';

$hrow = "<td></td>";
$hiddenfields="";

# read field mapping from previous window
foreach ($expfields as $expfield) {
	if (isset($_POST['importFields__'.str_replace(" ", "_",$expfield)])) {
		$impfield = $_POST['importFields__'.str_replace(" ", "_",$expfield)];
		if (in_array($expfield,$reqfields) && ($impfield == "-")) {
			$Result->show('danger', _("Error: missing required field mapping for expected field")." <b>".$expfield."</b>."._("Please check field matching in previous window."), true, true);
		} else {
			if ($impfield != "-") { $impfields[$impfield] = $expfield; }
		}
	} else {
		$Result->show('danger', _("Internal error: missing import field mapping."), true, true);
	}
	# prepare header row for preview table
	$hrow.="<th>".$expfield."</th>";
	# prepare select field to transfer to actual import file
	$hiddenfields.="<input name='importFields__".str_replace(" ", "_",$expfield)."' type='hidden' value='".$impfield."' style='display:none;'>";
}

$data = array();

# read first row from CSV
if (strtolower($filetype) == "csv") {
	# open CSV file
	$filehdl = fopen(IPAM_ROOT . '/upload/data_import.csv', 'r');

	# set delimiter
	$Tools->set_csv_delimiter ($filehdl);

	# read header row
	$row = 0;$col = 0;
	$line = fgets($filehdl);
	$row++;
	$line = str_replace( array("\r\n","\r","\n") , "" , $line);	//remove line break
	$cols = str_getcsv ($line, $Tools->csv_delimiter);
	foreach ($cols as $val) {
		$col++;
		# map import columns to expected fields as per previous window
		$fieldmap[$col] = $impfields[$val];
		$hcol = $col;
	}

	# read each remaining row into a dictionary with expected fields as keys
	while (($line = fgets($filehdl)) !== false) {
		$row++;$col = 0;
		$line = str_replace( array("\r\n","\r","\n") , "" , $line);	//remove line break
		$cols = str_getcsv ($line, $Tools->csv_delimiter);
		$record = array();
		foreach ($cols as $val) {
			$col++;
			if ($col > $hcol) {
				$Result->show('danger', _("Extra column found on line ").$row._(" in CSV file. CSV delimiter used in value field?"), true);
			} else {
				# read each row into a dictionary with expected fields as keys
				$record[$fieldmap[$col]] = trim($val);
			}
		}
		$data[] = $record;
	}
	fclose($filehdl);
}
# read first row from XLS
elseif(strtolower($filetype) == "xls") {
    // now autoloaded 
	$xls = new PHPExcelReader\SpreadsheetReader(IPAM_ROOT . '/upload/data_import.xls', false);
	$sheet = 0; $row = 1;

	# map import columns to expected fields as per previous window
	for($col=1;$col<=$xls->colcount($sheet);$col++) {
		$fieldmap[$col] = $impfields[$xls->val($row,$col,$sheet)];
		$hcol = $col;
	}

	# read each remaining row into a dictionary with expected fields as keys
	for($row=2;$row<=$xls->rowcount($sheet);$row++) {
		$record = array();
		for($col=1;$col<=$xls->colcount($sheet);$col++) {
			$record++;
			if ($col > $hcol) {
					$Result->show('danger', _("Extra column found on line ").$row._(" in XLS file. Please check input file."), true);
			} else {
				$record[$fieldmap[$col]] = trim($xls->val($row,$col,$sheet));
			}
		}
		$data[] = $record;
	}
}

?>
