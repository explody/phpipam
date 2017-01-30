<?php
ob_start();

/*
|--------------------------------------------------------------------------
| Include the paths config
|--------------------------------------------------------------------------
|
| If you need to move files and directories of the app around, this can be 
| done in paths.php.  But, this file must always be able to find paths.php
| so if you reorganize the structure, update the next line.
|
*/

require_once dirname(__FILE__) . "/../paths.php";

/* config */
try {
    $c = require(CONFIG);
} catch (IpamConfigNotFound $e) {
    die('<br /><hr />' .$e . '<br /> Make sure you have copied config.dist.yml to config.yml.' );
} catch (IpamEnvironmentNotFound $e) {
    die('<br /><hr />' . $e . '<br /> No environment config found. Ensure your environment is specified correctly in '.
        'config.yml and that environment config exists' );
} catch (Exception $e) {
    die('<br /><hr />Error loading config<br /> The config loader reports: ' . $e );
}

/* site functions */
require FUNCTIONS . '/functions.php';

/* composer */
require_once VENDOR . '/autoload.php';

# Initialize objects
$Database 	= new Database_PDO;
$Tools		= new Tools ($Database);

# validate action + method
$method = $_SERVER['REQUEST_METHOD'];
$Tools->validate_method($method, true);

if (!$Database->bootstrap_required()) {
    $User 		= new User ($Database);
    $Addresses  = new Addresses ($Database);
    $Admin	 	= new Admin ($Database, false);
    $Sections	= new Sections ($Database);
    $Subnets	= new Subnets ($Database);
    $Devices    = new Devices ($Database);
    $Components = new Components ($Tools);
}

$Result 	= new Result ();

$path = $_GET['a'];

// dispose of the path elements from _GET
unset($_GET['a']);

# verify that user is logged in, unless it's a login or captcha call, or if setup is incomplete
if ($path[1] != 'login_check' && !$Database->setup_required()) {
    $User->check_user_session();
}

// mod_rewrite sends to the destination as a GET. This is where the path info will be.
$path = APP . '/' . implode(DIRECTORY_SEPARATOR, $path) . '.php';

# strip tags - XSS
if(isset($_POST)) {
    $_POST = $Tools->strip_input_tags($_POST);
}

// $_GET will always be set
$_GET = $Tools->strip_input_tags($_GET);

// die if our target script does not exist
if (file_exists($path)) {
    include($path);
} else {
    header('Location: ' . create_link('error','404')); 
    die();
}

ob_end_flush(); 
?>
