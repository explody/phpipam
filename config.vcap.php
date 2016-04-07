<?php

/*  database connection details
 ******************************/
if (array_key_exists('VCAP_SERVICES', $_SERVER)) {

  $svcj = $_SERVER["VCAP_SERVICES"];

  $services = json_decode($svcj, true);

  // Specify the array path to your service creds 
  $mysql_svc = $services["user-provided"][0]["credentials"];

  $db['host'] = $mysql_svc['hostname'];
  $db['user'] = $mysql_svc['username'];
  $db['pass'] = $mysql_svc['password'];
  $db['name'] = $mysql_svc['dbname'];
  $db['port'] = 3306;

  /* SSL options
   ******************************/
  // See http://php.net/manual/en/ref.pdo-mysql.php
  //     https://dev.mysql.com/doc/refman/5.7/en/ssl-options.html

  // Please update these settings before setting 'ssl' to true.
  // All settings can be commented out or set to NULL if not needed

  $db['ssl']        = true;
  $db['ssl_key']    = NULL;
  $db['ssl_cert']   = NULL;
  $db['ssl_ca']     = "/app/.bp-config/pivotal-combined.pem";
  $db['ssl_capath'] = NULL;
  $db['ssl_cipher'] = "DHE-RSA-AES256-SHA:AES128-SHA";

  set_include_path(get_include_path() . PATH_SEPARATOR . '/app/php/lib/php');

} else {

  $db['host'] = 'data1-sc.vchs.pivotal.io';
  $db['user'] = 'phpipam_dev';
  $db['pass'] = 'dA9xQDh0CFaRue2rFBWyCilPDNRRE4jdwwW';
  $db['name'] = 'phpipam_dev';
  $db['port'] = 3306;
  $db['ssl']  = true;
  $db['ssl_ca'] = '/etc/pivotal-combined.pem';

}

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

/*  proxy connection details
 ******************************/
$proxy_enabled  = false;                                  # Enable/Disable usage of the Proxy server
$proxy_server   = "myproxy.something.com";                # Proxy server FQDN or IP
$proxy_port     = "8080";                                 # Proxy server port
$proxy_user     = "USERNAME";                             # Proxy Username
$proxy_pass     = "PASSWORD";                             # Proxy Password
$proxy_use_auth = false;                                  # Enable/Disable Proxy authentication

/**
 * proxy to use for every internet access like update check
 */
$proxy_auth     = base64_encode("$proxy_user:$proxy_pass");

if ($proxy_enabled == true && proxy_use_auth == false) {
    stream_context_set_default(['http'=>['proxy'=>'tcp://$proxy_server:$proxy_port']]);
}
elseif ($proxy_enabled == true && proxy_use_auth == true) {
    stream_context_set_default(
        array('http' => array(
              'proxy' => "tcp://$proxy_server:$proxy_port",
              'request_fulluri' => true,
              'header' => "Proxy-Authorization: Basic $proxy_auth"
        )));
}

?>
