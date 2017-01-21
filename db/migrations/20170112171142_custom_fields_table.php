<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class CustomFieldsTable extends Ipam\Migration\RepeatableMigration
{
    protected $customFieldsTable = [
        'table' => [
            'options' => [],
            'columns' => [
                ['table', 'string', ['limit'=>64,'null'=>false]],
                ['field', 'string', ['limit'=>64,'null'=>false]],
                ['type', 'string', ['limit'=>32,'null'=>false]],
                ['description', 'string', ['limit'=>64,'null'=>false]],
                ['order', 'integer', ['limit'=>2, 'default'=>0,'null'=>false]],
                ['params', 'blob', ['null'=>false]]
            ],
            'indexes' => [],
            'fkeys' => []
        ]
    ];
    
    public function change()
    {
        $this->migrationFromSchemaArray($this->customFieldsTable,true);
    }
}

?>