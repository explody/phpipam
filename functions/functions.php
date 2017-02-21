<?php

// Critical, do not change
require_once dirname(__FILE__) . "/../paths.php";

/* @config file ------------------ */
$c = require CONFIG;

/* @composer autoload ------------------ */
require_once VENDOR . '/autoload.php';

/* @http only cookies ------------------- */
ini_set('session.cookie_httponly', 1);

/* @debugging functions ------------------- */
ini_set('display_errors', 1);
if (!$c->debugging) { 
    error_reporting(E_ERROR ^ E_WARNING); 
} else { 
    error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT); 
}

/**
 * detect missing gettext and fake function
 */
if(!function_exists('gettext')) {
	function gettext ($text) 	{ return $text; }
	function _($text) 			{ return $text; }
}

// BASE is defined in config.php now. Remove shortly.
// auto-set base if not already defined
// if(!defined('BASE')) {
// 	$root = substr($_SERVER['DOCUMENT_ROOT'],-1)=="/" ? substr($_SERVER['DOCUMENT_ROOT'],0,-1) : $_SERVER['DOCUMENT_ROOT'];	// fix for missing / in some environments
// 	define('BASE', substr(str_replace($root, "", dirname(__FILE__)),0,-9));
// }

/* @classes ---------------------- */
require_once( FUNCTIONS . '/classes/class.Common.php' );		//Class common - common functions
require_once( FUNCTIONS . '/classes/class.PDO.php' );		//Class PDO - wrapper for database
require_once( FUNCTIONS . '/classes/class.User.php' );		//Class for active user management
require_once( FUNCTIONS . '/classes/class.Log.php' );		//Class for log saving
require_once( FUNCTIONS . '/classes/class.Result.php' );		//Class for result printing
require_once( FUNCTIONS . '/classes/class.Install.php' );	//Class for Install
require_once( FUNCTIONS . '/classes/class.Sections.php' );	//Class for sections
require_once( FUNCTIONS . '/classes/class.Subnets.php' );	//Class for subnets
require_once( FUNCTIONS . '/classes/class.Tools.php' );		//Class for tools
require_once( FUNCTIONS . '/classes/class.Addresses.php' );	//Class for addresses
require_once( FUNCTIONS . '/classes/class.Scan.php' );		//Class for Scanning and pinging
require_once( FUNCTIONS . '/classes/class.DNS.php' );		//Class for DNS management
require_once( FUNCTIONS . '/classes/class.PowerDNS.php' );	//Class for PowerDNS management
require_once( FUNCTIONS . '/classes/class.FirewallZones.php' );	//Class for firewall zone management
require_once( FUNCTIONS . '/classes/class.Admin.php' );		//Class for Administration
require_once( FUNCTIONS . '/classes/class.Mail.php' );		//Class for Mailing
require_once( FUNCTIONS . '/classes/class.Rackspace.php' );	//Class for Racks
require_once( FUNCTIONS . '/classes/class.SNMP.php' );	    //Class for SNMP queries
require_once( FUNCTIONS . '/classes/class.DHCP.php' );	    //Class for DHCP
require_once( FUNCTIONS . '/classes/class.PaginationLinks.php' );	    //Class for generating pagination links
require_once( FUNCTIONS . '/classes/class.PagedSearch.php' );
require_once( FUNCTIONS . '/classes/class.Components.php' );
require_once( FUNCTIONS . '/classes/class.Devices.php' );

if(!isset($Database)) {
    try {
        # database object
        $Database     = new Database_PDO;
    } catch (Exception $e) {
        header("Location: /broken/");
    }
}

# save settings to constant
if (!$Database->bootstrap_required()) {
	# try to fetch settings
	try { $settings = $Database->getObject("settings", 1); }
	catch (Exception $e) { $settings = false; }
	if ($settings!==false) {
		if (phpversion() < "5.4") {
			define(SETTINGS, json_encode($settings));
		}else{
			define(SETTINGS, json_encode($settings, JSON_UNESCAPED_UNICODE));
		}
	}
    
    if ($settings->dbSessions == 'Yes') {
        $session = new Zebra_Session($Database->get_connection(), 'al1Q7SDTXhtICkvjIMHohMM');
    }
    
}

/**
 * create links function
 *
 *	if rewrite is enabled in settings use rewrite, otherwise ugly links
 *
 *	levels: $el
 */
function create_link ($l0 = null, $l1 = null, $l2 = null, $l3 = null, $l4 = null, $l5 = null, $l6 = null ) {
	# get settings
	global $User;

	# set normal link array
	$el = array("page", "section", "subnetId", "sPage", "ipaddrid", "tab");
	// override for search
	if ($l0=="tools" && $l1=="search")
    $el = array("page", "section", "ip", "addresses", "subnets", "vlans", "ip");

	# set rewrite
	if($User->settings->prettyLinks=="Yes") {
		if(!is_null($l6))		{ $link = "$l0/$l1/$l2/$l3/$l4/$l5/$l6"; }
		elseif(!is_null($l5))	{ $link = "$l0/$l1/$l2/$l3/$l4/$l5/"; }
		elseif(!is_null($l4))	{ $link = "$l0/$l1/$l2/$l3/$l4/"; }
		elseif(!is_null($l3))	{ $link = "$l0/$l1/$l2/$l3/"; }
		elseif(!is_null($l2))	{ $link = "$l0/$l1/$l2/"; }
		elseif(!is_null($l1))	{ $link = "$l0/$l1/"; }
		elseif(!is_null($l0))	{ $link = "$l0/"; }
		else					{ $link = ""; }

		# IP search fix
		if ($l0=="tools" && $l1=="search" && isset($l2) && substr($link,-1)=="/") {
    		$link = substr($link, 0, -1);
		}
	}
	# normal
	else {
		if(!is_null($l6))		{ $link = "?$el[0]=$l0&$el[1]=$l1&$el[2]=$l2&$el[3]=$l3&$el[4]=$l4&$el[5]=$l5&$el[6]=$l6"; }
		elseif(!is_null($l5))	{ $link = "?$el[0]=$l0&$el[1]=$l1&$el[2]=$l2&$el[3]=$l3&$el[4]=$l4&$el[5]=$l5"; }
		elseif(!is_null($l4))	{ $link = "?$el[0]=$l0&$el[1]=$l1&$el[2]=$l2&$el[3]=$l3&$el[4]=$l4"; }
		elseif(!is_null($l3))	{ $link = "?$el[0]=$l0&$el[1]=$l1&$el[2]=$l2&$el[3]=$l3"; }
		elseif(!is_null($l2))	{ $link = "?$el[0]=$l0&$el[1]=$l1&$el[2]=$l2"; }
		elseif(!is_null($l1))	{ $link = "?$el[0]=$l0&$el[1]=$l1"; }
		elseif(!is_null($l0))	{ $link = "?$el[0]=$l0"; }
		else					{ $link = ""; }
	}
	# prepend base
	$link = BASE.$link;

	# result
	return $link;
}

// Moved to config.php so we can see the version data everywhere
/* get version */
// include(dirname(__FILE__) . '/version.php');

?>
