<?php  

require_once(__DIR__ . '/../paths.php');
require_once(VENDOR   . '/autoload.php');
$c = require('loader.php');

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

# TODO: I don't think we need to use finder here. Try replacing with glob()
$finder = new Finder();
$finder->files()->in(ENV_DIR)->exclude('dist');

$pconf = [
    'paths' => [
        'migrations' => IPAM_ROOT . '/' . $c->migrations->paths->migrations,
        'seeds' => IPAM_ROOT . '/' . $c->migrations->paths->seeds,
    ],
    'environments' => [
        'default_database' => $c->environment,
        'default_migration_table' => $c->migrations->environments->default_migration_table
    ]
];

// It's a bit repetitive to load individual env configs here after 
// using IpamConfig. But, the frequency of running here is low and 
// the performance hit is trivial at worst.
// This lets us build a phinx config array for all the environments with config files.
foreach ($finder as $file) {

    if (substr($file, -3) != 'yml') {
        continue;
    }
    
    $env = basename($file, '.yml');
    $env_conf = Yaml::parse(file_get_contents($file->getRealPath()));

    $pconf['environments'][$env] = [];
    
    // Set defaults for all DBs from the main config.
    // These may or may not be relevant for phinx but they do no harm and we need some, like 'charset'
    $pconf['environments'][$env] = $c->db_defaults->as_array();
    
    // Merge specific phinx-required properties from the env into the phinx config
    $pconf['environments'][$env] = array_merge(
                                    $pconf['environments'][$env], 
                                    [
                                        'adapter' => 'mysql',
                                        'name' => $env_conf['db']['name'],
                                        'host' => $env_conf['db']['host'],
                                        'user' => $env_conf['db']['user'],
                                        'pass' => $env_conf['db']['pass'],
                                        'unix_socket' => (isset($env_conf['db']['socket']) ? 
                                                          $env_conf['db']['socket'] : 
                                                          null )
                                    ]
                                   );
    
    // Merge env-specific migration settings over the phinx env config.  This allows override of default settings 
    // from the main config
    if (array_key_exists('migrations', $env_conf['db'])) {
        $pconf['environments'][$env] = array_merge($pconf['environments'][$env], 
                                                   $env_conf['db']['migrations']);
    }
    
    // Set any given mysql_attrs
    if (array_key_exists('mysql_attr', $env_conf['db'])) {
        foreach ($env_conf['db']['mysql_attr'] as $attr=>$val) {
            $xname = 'mysql_attr_' . $attr;
            $pconf['environments'][$env][$xname] = $val;
        }
    }
}

return $pconf;

?>