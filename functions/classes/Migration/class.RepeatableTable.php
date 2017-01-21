<?php

namespace Ipam\Migration;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;

class RepeatableTable extends Table
{
    /**
     * @var array
     */
    protected $columnsByName = [];
    
    /**
     * @var bool
     */
    protected $debug = false;
    
    /**
     * {@inheritdoc}
     */
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
    
    /**
     * Sets the debugging flag
     *
     * @param bool $flag true/false
     * @return void
     */
    public function setDebug($flag) {
        $this->debug = $flag;
    }

    /**
     * Returns the array of columns, indexed on column name
     *
     * @return array
     */
    public function getColumnsByName() {
        return $this->columnsByName;
    }
    
    /**
     * Returns one column object from the array of columns
     *
     * @return false|Phinx\Db\Table\Column
     */
    public function getColumnByName($columnName) {
        return (array_key_exists($columnName, $this->columnsByName) ? $this->columnsByName[$columnName] : false);
    }
    
    /**
     * {@inheritdoc}
     * 
     * Wrapper around the parent's addColumn(). Differs in that it will check if the table and column
     * exist. If not, it will add as normal. If both do, it will compare the column type and options.
     * If they are different than the given column options, it will trigger a changeColumn()
     */
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
    
    /**
     * {@inheritdoc}
     * 
     * Wrapper around the parent's addIndex(). If the index already exists, does nothing. If not, adds it.
     */
    public function addIndex($columns, $options = array()) {
        
        if(!$this->exists() || !$this->hasIndex($columns)) {
            parent::addIndex($columns, $options);
        }
        return $this;
        
    }
    
    /**
     * {@inheritdoc}
     * 
     * Wrapper around the parent's addForeignKey(). If the FK already exists, does nothing. If not, adds it.
     */
    public function addForeignKey($columns, $referencedTable, $referencedColumns = array('id'), $options = array()) {
        
        if(!$this->exists() || !$this->hasForeignKey($columns)) {
            parent::addForeignKey($columns, $referencedTable, $referencedColumns, $options);
        }
        return $this;
        
    }
    
}