<?php 

/* 
 * Ensure dependencies are loaded.
 * paths.php must be required first (and should be normally)
 */
require_once(VENDOR   . '/autoload.php');
require_once(FUNCTIONS . '/classes/class.Config.php');

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
$primary_config = CONFIG_DIR . '/config.yml';
if (!file_exists($primary_config)) { 
    throw new IpamConfigNotFound("Config file not found! - " . $primary_config); 
}
$defaults = Yaml::parse(file_get_contents($primary_config));

// cherry pick the environment setting out of config.yml before building the config object, 
// so we know which file to load.
$env_config = ENV_DIR . '/' . $defaults['environment'] . '.yml';
if (!file_exists($env_config)) {
    throw new IpamEnvironmentNotFound("Environment config file not found! - " . $env_config);
}
$env      = Yaml::parse(file_get_contents($env_config));

$config = [];

// First copy the db defaults into place
$defaults['db'] = $defaults['db_defaults'];

// Recursively merge environment config over primary config
$config = IpamConfig::config_merge_recursive($defaults, $env);

// Initalize the config pseudo-singleton with the config array
IpamConfig::init($config);

// Repetitive but necessary
require_once(CONFIG_DIR . '/proxy.php');

return IpamConfig::config();

?>