<?php
/*******************************
 * Version Constants
 * Don't change these unless you really know what you're doing
 ******************************/

/* App version */
define("VERSION", json_decode(file_get_contents(dirname(__FILE__) . '/../package.json'))->version );

/* set latest revision */
define("REVISION", "030");		   // deprecated

/* set last possible upgrade */
define("LAST_POSSIBLE", "1.29");	   // Minimum required version to be able to upgrade

/* media version */
define("MEDIA_VERSION", VERSION);  // Support media-specific versioning

?>
