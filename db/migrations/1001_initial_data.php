<?php

use Phinx\Migration\AbstractMigration;

class InitialData extends AbstractMigration
{
    
    public $initialData = [
        'instructions' => [
            [
                'instructions' => 'You can write instructions under admin menu!'
            ]
        ]
    ];

    public function change() {
        
        foreach ($this->initialData as $table=>$rows) {
            
        }
        
    }

}