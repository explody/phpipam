<?php

namespace Ipam\Migration;

require(dirname(__FILE__) . '/class.RepeatableTable.php');

use Phinx\Migration\AbstractMigration;

class RepeatableMigration extends AbstractMigration
{

    /**
    * {@inheritdoc}
    */
    public function table($tableName, $options = array()) {
       return new RepeatableTable($tableName, $options, $this->getAdapter());
    }
    
    /**
     * Utility method.  Takes a structured array of schema information and adds/updates 
     * tables, columns, indexes and foreign keys.
     *
     * @param array $schema
     * @param bool $debug
     * @return void
     */
    public function migrationFromSchemaArray($schema, $debug = false) {
        
        foreach ($schema as $tn => $td) {
            
            $debug ? print "  Checking table $tn\n" : null;
            
            $table = $this->table($tn, $td['options']);
            
            $table->setDebug($debug);
            
            foreach ($td['columns'] as $newcol) {
                $table = $table->addColumn($newcol[0], $newcol[1], $newcol[2]);
            }

            foreach ($td['indexes'] as $idx) {
                $table = $table->addIndex($idx[0], $idx[1]);
            }
            
            foreach ($td['fkeys'] as $fk) {
                $table = $table->addForeignKey($fk[0], $fk[1], $fk[2], $fk[3]);
            }
            
            $table->save();
            
        }
        
    }
    
}