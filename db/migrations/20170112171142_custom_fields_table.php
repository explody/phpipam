<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class CustomFieldsTable extends Ipam\Migration\RepeatableMigration
{
    protected $customFieldsTable = [
        'customFields' => [
            'options' => [],
            'columns' => [
                ['table', 'string', ['limit'=>64,'null'=>false]],
                ['name', 'string', ['limit'=>64,'null'=>false]],
                ['display_name', 'string', ['limit'=>64,'null'=>false]],
                ['type', 'string', ['limit'=>32,'null'=>false]],
                ['description', 'string', ['limit'=>255,'null'=>true]],
                ['limit', 'integer', ['limit'=>8, 'default'=>128,'null'=>true]],
                ['order', 'integer', ['limit'=>2, 'default'=>0,'null'=>false]],
                ['required', 'boolean', ['default'=>0,'null'=>false]],
                ['null', 'boolean', ['default'=>1,'null'=>false]],
                ['visible', 'boolean', ['default'=>1,'null'=>false]],
                ['default', 'string', ['limit'=>64,'null'=>true]],
                ['params', 'text', ['null'=>false]]
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