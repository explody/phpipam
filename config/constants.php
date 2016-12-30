<?php 

/*******************************
 * Globally available settings
 * 
 * 
 ******************************/

/**
 * Misc Config
 *
 ******************************/

/**
 * Multicast unique mac requirement - section or vlan
 **/
define('MCUNIQUE', "section");

/**
 * Define server paths for building URLs
 * See also paths.php
 * Don't change these unless you really know what you're doing
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