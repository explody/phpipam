<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class DeviceGroupSettings extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        $table = $this->table('settings')
                    ->addColumn('devicegrouping', 'boolean', array('default'=>0,'null'=>true))
                    ->addColumn('devicegroupfield', 'string', array('limit' => 128))
                    ->save();
    }
}

?>