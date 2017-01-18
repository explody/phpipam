<?php
/*
 * CSV import verify + parse data
 *************************************************/

// define file
$file = dirname(__FILE__). '/../../../../' . MEDIA . '/images/logo/logo.png';

# try to remove logo
try {
    if(!is_writable($file)) {
        throw new Exception("File $file not writable");
    }
    // remove
    unlink($file);
    // ok
    $Result->show("success", "Logo removed");
}
catch(Exception $e) {
    $Result->show("danger", "Cannot remove logo file ".$file." - error ".$e->getMessage());
}

?>
