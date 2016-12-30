<?php

/*  database connection details
 ******************************/

$db['host'] = 'host.domain.com';
$db['user'] = 'phpipam';
$db['pass'] = 'mypassword';
$db['name'] = 'phpipam';
$db['port'] = 3306;

/* SSL options for MySQL
 ******************************
 See http://php.net/manual/en/ref.pdo-mysql.php
     https://dev.mysql.com/doc/refman/5.7/en/ssl-options.html

     Please update these settings before setting 'ssl' to true.
     All settings can be commented out or set to NULL if not needed

     php 5.3.7 required
*/
$db['ssl']        = false;                           # true/false, enable or disable SSL as a whole
$db['ssl_key']    = "/path/to/cert.key";             # path to an SSL key file. Only makes sense combined with ssl_cert
$db['ssl_cert']   = "/path/to/cert.crt";             # path to an SSL certificate file. Only makes sense combined with ssl_key
$db['ssl_ca']     = "/path/to/ca.crt";               # path to a file containing SSL CA certs
$db['ssl_capath'] = "/path/to/ca_certs";             # path to a directory containing CA certs
$db['ssl_cipher'] = "DHE-RSA-AES256-SHA:AES128-SHA"; # one or more SSL Ciphers

// Cloud Foundry Support
if (array_key_exists('VCAP_SERVICES', $_SERVER)) {

  $svcj = $_SERVER["VCAP_SERVICES"];

  $services = json_decode($svcj, true);

  // Specify the array path to your service creds 
  $mysql_svc = $services["user-provided"][0]["credentials"];

  $db['host'] = $mysql_svc['hostname'];
  $db['user'] = $mysql_svc['username'];
  $db['pass'] = $mysql_svc['password'];
  $db['name'] = $mysql_svc['dbname'];
  $db['port'] = $mysql_svc['port'];

  set_include_path(get_include_path() . PATH_SEPARATOR . '/app/php/lib/php');

} 

?>