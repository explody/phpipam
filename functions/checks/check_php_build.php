<?php

/**
 *
 * Script to check if all required extensions are compiled and loaded in PHP
 *
 *
 * We need the following mudules:
 *      - session
 *      - gmp
 *		- json
 *		- gettext
 *		- PDO
 *		- pdo_mysql
 *
 ************************************/


# Required extensions
$requiredExt  = array("session", "sockets", "filter", "openssl", "gmp", "json", "gettext", "PDO", "pdo_mysql", "mbstring");

# Available extensions
$availableExt = get_loaded_extensions();

$missingExt = [];

# if not all are present create array of missing ones
foreach ($requiredExt as $extension) {
    if (!in_array($extension, $availableExt)) {
        $missingExt[] = $extension;
    }
}

# check if mod_rewrite is enabled in apache
if (function_exists("apache_get_modules")) {
    $modules = apache_get_modules();
    if(!in_array("mod_rewrite", $modules)) {
        $missingExt[] = "mod_rewrite (Apache module)";
    }
}

# if db ssl = true check version
if (@$db['ssl']==true) {
    if (phpversion() < "5.3.7") {
        $missingExt[] = "For SSL MySQL php version 5.3.7 is required!";
    }
}

# if any extension is missing print error and die!
if (sizeof($missingExt) != 0) {

    /* error */
    $error  = "<div class='alert alert-danger' style='margin:auto;margin-top:20px;width:500px;'><strong>"._('The following required PHP extensions are missing').":</strong><br><hr>";
    $error  .= '<ul>' . "\n";
    foreach ($missingExt as $missing) {
        $error .= '<li>'. $missing .'</li>' . "\n";
    }
    $error  .= '</ul><hr>' . "\n";
    $error  .= _('Please recompile PHP to include missing extensions and restart Apache.') . "\n";
    $error  .= "</div>";

    die($error);
    
}
?>