<?php

namespace Ipam\Migration;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;

class RepeatableTable extends Table
{
    
    protected $columnsByName = [];
    protected $debug = false;
    
    public function __construct($name, $options = array(), AdapterInterface $adapter = null) {
        
        parent::__construct($name, $options, $adapter);
        
        if ($this->exists()) {
            $tcs = $this->getColumns();
            // Build array of columns indexed on table and column names
            foreach ($tcs as $c) {
                $this->columnsByName[$c->getName()] = $c;
            }
        }
        
    }
    
    public function setDebug($flag) {
        $this->debug = $flag;
    }
    
    public function setColumnsByName($columns) {
        $this->columnsByName = $columns;
    }
    
    public function getColumnsByName() {
        return $this->columnsByName;
    }
    
    public function getColumnByName($columnName) {
        return (array_key_exists($columnName, $this->columnsByName) ? $this->columnsByName[$columnName] : false);
    }
    
    public function addColumn($columnName, $type = NULL, $options = array()) {
        
        $oldColumn = $this->getColumnByName($columnName);
        $this->debug ? print "    Check column $columnName" . ($oldColumn ? " (EXISTS)\n" : " (NEW)\n"): null;
        
        if(!$this->exists() || ($this->exists() && !$oldColumn)) {
            parent::addColumn($columnName, $type, $options);
        } else {
            
            $change = false;
            if ($oldColumn->getType() != $type) {
                $this->debug ? print "      Changing column type " . $oldColumn->getType() . " => $type\n" : null;
                $change = true;
            }
            
            // phinx is not returning the set/enum values correctly so we can't compare them 
            // if it's a set/enum, just modify it
            if (in_array($oldColumn->getType(), ['set','enum'])) {
                $this->debug ? print "      Column is a " . $oldColumn->getType() . ". Setting options\n" : null;
                $change = true;
            } else {
                foreach ($options as $opt => $oval) {
                    $getter = 'get' . ucfirst($opt);
                    if ($oldColumn->$getter() != $oval) {
                        $this->debug ? print "      Updating option " . $opt . ": " . $oldColumn->$getter() . " => " . $oval . "\n" : null;
                        $change = true;
                    }
                }
            }
            
            if ($change) {
                parent::changeColumn($columnName, $type, $options);
            }

        }
        
        return $this;
    }
    
    public function addIndex($columns, $options = array()) {
        
        if(!$this->exists() || !$this->hasIndex($columns)) {
            parent::addIndex($columns, $options);
        }
        return $this;
        
    }
    
    public function addForeignKey($columns, $referencedTable, $referencedColumns = array('id'), $options = array()) {
        
        if(!$this->exists() || !$this->hasForeignKey($columns)) {
            parent::addForeignKey($columns, $referencedTable, $referencedColumns, $options);
        }
        return $this;
        
    }
    
}