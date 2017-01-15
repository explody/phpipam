<?php 

/******************************
 * Define filesystem paths
 *
 * Change these settings to customize the filesystem layout
 * DO NOT MOVE THIS FILE FROM THE APP ROOT
 * 
 ******************************/

/**
 *  Full path to the application root, or where this file lives
 **/
define('APP_ROOT', realpath(dirname(__FILE__)));

/**
 *  Full path to web server document root. Generally the same as $_SERVER['DOCUMENT_ROOT'].
 *  Default: APP_ROOT/app
 **/
define('SERVER_ROOT', APP_ROOT . "/app");

/**
 *  Path to our composer vendor directory
 *  Default: APP_ROOT/vendor
 **/
define('VENDOR', APP_ROOT . '/vendor');

/**
 *  Full path to the config directory
 *  Default: APP_ROOT/config
 **/
 define('CONFIG_DIR', APP_ROOT . '/config');

/**
 *  Full path to the main config file
 *  Default: APP_ROOT/config
 **/
 define('CONFIG', CONFIG_DIR . '/loader.php');
 
 /**
  *  Full path to the environments config directory
  *  Default: CONFIG_DIR/environments
  **/
  define('ENV_DIR', CONFIG_DIR . '/environments');

# With basic paths set, we can require our version info 
require_once CONFIG_DIR . '/version.php';

/******************************
 *
 * Define web server paths
 * 
 ******************************/
 
/**
 *  BASE definition if phpipam is not at the root of your web server
 *  (e.g. domain.com/phpipam/). Include trailing slash.
 *
 *  Also change
 *  RewriteBase / in .htaccess
 **/
define('BASE', "/");

/**
 *  Static media server path, relative to BASE
 **/
define('STATIC_PATH', BASE . "static/");

/**
 * Define the active media path
 * e.g. /base/static/1.0.0/
 **/
define('MEDIA', STATIC_PATH . MEDIA_VERSION);

?>