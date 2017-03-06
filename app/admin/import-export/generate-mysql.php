<?php

/**
 *	Generate XLS file
 *********************************/
$Tools->csrf_validate($csrf, $Result);

//set filename
$filename = "phpipam_MySQL_dump_". date("Y-m-d") .".sql";

//set content
/* $command = "mysqldump --opt -h $db['host'] -u $db['user'] -p $db['pass'] $db['name'] | gzip > $backupFile"; */
$command = "mysqldump --opt -h '". $c->db->host ."' -u '". $c->db->user ."' -p'". $c->db->pass ."' '". $c->db->name ."'";

$content  = "# phpipam Database dump \n";
$content .= "#    command executed: $command \n";
$content .= "# --------------------- \n\n";
$content .= shell_exec($command);

/* headers */
header("Cache-Control: no-cache, no-store, max-age=0, must-revalidate");
header("Content-Description: File Transfer");
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="'. $filename .'"');

print($content);
?>
