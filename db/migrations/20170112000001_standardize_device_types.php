<?php

use Phinx\Migration\AbstractMigration;

class StandardizeDeviceTypes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('deviceTypes');
        foreach (['id','name','description'] as $col) {
            if ($table->hasColumn('t' . $col) && !$table->hasColumn($col)) {
                $table->renameColumn('t' . $col, $col);
            }
        }
        $table->save();
    }
}
