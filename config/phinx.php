<?php  

require_once(__DIR__ . '/../paths.php');
require_once(VENDOR   . '/autoload.php');
require('loader.php');

$c = IpamConfig::config();

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

$finder = new Finder();
$finder->files()->in(ENV_DIR);

$pconf = [
    'paths' => [
        'migrations' => APP_ROOT . '/' . $c->migrations->paths->migrations,
        'seeds' => APP_ROOT . '/' . $c->migrations->paths->seeds,
    ],
    'environments' => [
        'default_database' => $c->environment,
        'default_migration_table' => $c->migrations->environments->default_migration_table
    ]
];

// It's a bit repetitive to load individual env configs here after 
// using IpamConfig. But, the frequency of running here is low and 
// the performance hit is trivial at most.
// This lets us build a phinx config array for all the environments.
foreach ($finder as $file) {
    $env = basename($file,'.yml');
    $env_conf = Yaml::parse(file_get_contents($file->getRealPath()));

    $pconf['environments'][$env] = [
        'adapter' => 'mysql',
        'name' => $env_conf['db']['name'],
        'host' => $env_conf['db']['host'],
        'user' => $env_conf['db']['user'],
        'pass' => $env_conf['db']['pass'],
    ];
    
    if (array_key_exists('migrations', $env_conf['db'])) {
        $pconf['environments'][$env] = array_merge($pconf['environments'][$env], $env_conf['db']['migrations']);
    }
    
    if (array_key_exists('mysql_attr', $env_conf['db'])) {
        foreach ($env_conf['db']['mysql_attr'] as $attr=>$val) {
            $xname = 'mysql_attr_' . $attr;
            $pconf['environments'][$env][$xname] = $val;
        }
    }
}

return $pconf;

?>