<?php

/*  database connection details
 ******************************/
$db['host'] = 'data1-sc.vchs.pivotal.io';
$db['user'] = 'phpipam_dev';
$db['pass'] = 'dA9xQDh0CFaRue2rFBWyCilPDNRRE4jdwwW';
$db['name'] = 'phpipam_dev';
$db['port'] = 3306;
$db['ssl']  = true;
$db['ssl_ca'] = "/home/vcap/app/.bp-config/pivotal-combined.pem";
$db['ssl_cipher'] = "DHE-RSA-AES256-SHA:AES128-SHA";


/**
 * php debugging on/off
 *
 * true  = SHOW all php errors
 * false = HIDE all php errors
 ******************************/
$debugging = true;

/**
 *  manual set session name for auth
 *  increases security
 *  optional
 */
$phpsessname = "phpipam";

/**
 *  BASE definition if phpipam
 *  is not in root directory (e.g. /phpipam/)
 *
 *  Also change
 *  RewriteBase / in .htaccess
 ******************************/
if(!defined('BASE'))
define('BASE', "/");

?>
