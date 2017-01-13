<?php

use Phinx\Migration\AbstractMigration;

class DeviceGroupSettings extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('settings');
        $table->addColumn('devicegrouping', 'boolean', array('default'=>0,'null'=>true))->update();
        $table->addColumn('devicegroupfield', 'string', array('length' => 128))->update();
    }
}
