<?php
/*
 * Data import verify and load header row
 *************************************************/
 
 $User->csrf_cookie ("validate", "import-" . $_POST['type'], $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";

/* get extension */
$filename = $_FILES['file']['name'];
$expfields = explode("|",$_POST['expfields']);
$file_exp = explode(".", $filename);
$filetype = strtolower(end($file_exp));

/* list of permitted file extensions */
$allowed = array('xls','csv');
/* upload dir */
$upload_dir = IPAM_ROOT . "/upload";

$today = date("Ymd-His");

if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
	echo '{"status":"error", "error":"Upload directory is not writable, or does not exist."}';
	exit;
}

/* no errors */
if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
	//wrong extension
    if(!in_array(strtolower($filetype), $allowed)) {
		echo '{"status":"error", "error":"Invalid document type"}';
        exit;
    }
	//if cannot move
	elseif(!move_uploaded_file($_FILES["file"]["tmp_name"], $upload_dir . "/data_import." . $filetype )) {
		echo '{"status":"error", "error":"Cannot move file to upload dir"}';
		exit;
	}
	//other errors
	elseif($_FILES['file']['error'] != 0) {
		echo '{"status":"error", "error":"Error: '.$_FILES['file']['error'].'" }';
        exit;
	}
	else {
	//default - success

	// grab first row from CSV
	if (strtolower($filetype) == "csv") {
		/* get file to string */
		$filehdl = fopen($upload_dir . '/data_import.csv', 'r');
		$data = fgets($filehdl);
		fclose($filehdl);

		/* format file */
		$data = str_replace( array("\r\n","\r","\n") , "" , $data);	//remove line break
		$data = preg_split("/[;,]/", $data); //split by comma or semi-colon

		foreach ($data as $col) {
			$firstrow[] = $col;
		}
	}
	// grab first row from XLS
	elseif(strtolower($filetype) == "xls") {
        // now autoloaded 
		$data = new PHPExcelReader\SpreadsheetReader($upload_dir . '/data_import.xls', false);
		$sheet = 0; $row = 1;

		for($col=1;$col<=$data->colcount($sheet);$col++) {
			$firstrow[] = $data->val($row,$col,$sheet);
		}
	}

	echo '{"status":"success","expfields":'.json_encode($expfields,true).',"impfields":'.json_encode($firstrow,true).',"filetype":'.json_encode($filetype,true).'}';
	exit;
	}
}

/* default - error */
echo '{"status":"error","error":"Empty file"}';
exit;
?>

