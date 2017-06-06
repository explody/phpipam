<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/functions.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class RenameSwitchToDevice extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        $table = $this->table('ipaddresses');
        $table->renameColumn('switch','device');
        $table->save();
        
        $d = new Database_PDO;
        $d->runQuery('update settings set IPfilter="mac;owner;state;device;note;firewallAddressObject"');
    }
}
