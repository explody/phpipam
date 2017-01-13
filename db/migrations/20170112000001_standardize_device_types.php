<?php

use Phinx\Migration\AbstractMigration;

class StandardizeDeviceTypes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('deviceTypes');
        $table->renameColumn('tid','id');
        $table->renameColumn('tname','name');
        $table->renameColumn('tdescription','description');
    }
}
