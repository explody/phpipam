<?php

use Phinx\Migration\AbstractMigration;

class CustomFieldsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('customFields')
                ->addColumn('table', 'string', ['length'=>64, 'null'=>false])
                ->addColumn('field', 'string', ['length'=>64, 'null'=>false])
                ->addColumn('description', 'string', ['length'=>64, 'null'=>false])
                ->addColumn('order', 'integer', ['default'=>0, 'null'=>false])
                ->addColumn('params', 'blob', ['null'=>false])
                ->create();
    }
}
