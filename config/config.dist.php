<?php

// Do not change these
require_once dirname(__FILE__) . '/version.php';
require_once dirname(__FILE__) . '/constants.php';

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
 * Permit private subpages - private apps under /app/tools/custom/<custom_app_name>/index.php
 *
 * (default value: false)
 *
 * @var bool
 * @access public
 */
$private_subpages = array();

/**
 * Google MAPs API key for locations to display map
 *
 *  Obtain key: Go to your Google Console (https://console.developers.google.com) and enable "Google Maps JavaScript API"
 *  from overview tab, so go to Credentials tab and make an API key for your project.
 */
$gmaps_api_key = "";


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

if ($proxy_enabled == true && $proxy_use_auth == false) {
    stream_context_set_default(array('http' => array('proxy'=>'tcp://'.$proxy_server.':'.$proxy_port)));
}
elseif ($proxy_enabled == true && $proxy_use_auth == true) {
    stream_context_set_default(
        array('http' => array(
              'proxy' => "tcp://$proxy_server:$proxy_port",
              'request_fulluri' => true,
              'header' => "Proxy-Authorization: Basic $proxy_auth"
        )));
}

/* for debugging proxy config uncomment next line */
#var_dump(stream_context_get_options(stream_context_get_default()));

// Include database settings
require dirname(__FILE__) . '/database.php';

?>
