<?php 

/******************************
 * Define filesystem paths
 *
 * Change these settings to customize the filesystem layout
 * DO NOT MOVE THIS FILE FROM THE APP ROOT
 * 
 ******************************/

/**
 *  Full path to the application root, or where the index.php lives
 **/
define('APP_ROOT', realpath(dirname(__FILE__)));

/**
 *  Full path to web server document root. Generally the same as $_SERVER['DOCUMENT_ROOT'].
 *  Default: APP_ROOT
 **/
define('SERVER_ROOT', APP_ROOT);

/**
 *  Full path to the main config file
 *  Default: APP_ROOT/config
 **/
define('CONFIG', APP_ROOT . '/config/config.php');

?>