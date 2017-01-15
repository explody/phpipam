<?php 

/* 
 * Ensure dependencies are loaded.
 * paths.php must be required first (and should be normally)
 */
require_once(VENDOR   . '/autoload.php');
require_once(APP_ROOT . '/functions/classes/class.Config.php');

/* 
 * If a config instance exists, simply return it
 */
if (IpamConfig::alive()) {
    require_once(CONFIG_DIR . '/proxy.php');
    return IpamConfig::config();
}

// Otherwise, read our config files and return the IpamConfig
use Symfony\Component\Yaml\Yaml;

// Load primary and environment configs
$defaults = Yaml::parse(file_get_contents(CONFIG_DIR . '/config.yml'));
$env      = Yaml::parse(file_get_contents(ENV_DIR . '/' . $defaults['environment'] . '.yml'));

// Merge environment over primary
$config = array_merge($defaults, $env);

// Initalize the config pseudo-singleton with the config array
IpamConfig::init($config);

return IpamConfig::config();

?>