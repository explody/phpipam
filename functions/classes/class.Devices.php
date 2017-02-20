<?php

/**
*	phpIPAM Devices class
*/

class Devices extends Tools {
    
    /**
     * Result printing class
     *
     * @var mixed
     * @access public
     */
    public $Result;

    /**
     * Database class
     *
     * @var mixed
     * @access protected
     */
    protected $Database;

    /**
     * Logging class
     *
     * @var mixed
     * @access public
     */
    public $Log;

    /**
     * Default sort field
     *
     * @var string
     * @access private
     */
    private $defsort = 'hostname';

    /**
     * Devices table name
     *
     * @var string
     * @access private
     */
    private $tbl = 'devices';
    
    /**
     * Device types table name
     *
     * @var string
     * @access private
     */
    private $typetbl = 'deviceTypes';
    
    /**
     * Columns in the device table that contain ID references to other tables
     * and can be resolved to objects.  This maps column name in devices
     * to the table name.
     *
     * @var array
     * @access public
     */
    public static $extRefs = array(
        'section' =>'sections',
        'location'=>'locations',
        'rack'    =>'racks',
        'type'    =>'deviceTypes'
    );
      
    /**
     * constructor
     *
     * @access public
     */
    public function __construct (Database_PDO $database) {
    	# Save database object
    	$this->Database = $database;
    	# initialize Result
    	$this->Result = new Result ();
    	# Log object
    	$this->Log = new Logging ($this->Database);
    }
    
    /**
	 * Fetch all device objects
	 *
	 * @access private
	 * @param string $sort Optional table column on which to sort
	 * @return array|bool
	 */
    private function _all($sort) {
        return $this->fetch_all_objects($this->tbl, $sort);
    }
    
    /**
	 * Arrange a list (array) of devices into a md array with object props 
     * as the keys.  Objects with missing or empty props will be put under 
     * a key called "No <property>"
	 *
	 * @access private
     * @param array $dev a 2d array of device objects
	 * @param string $groupby The obj property from the devices to use as array keys
     * @param string|bool $sort Optional string corresponding to table column / object property
     *                          on which to sort. Not used right now
	 * @return array
	 */
    private function _group($devs, $groupby, $sort=false) {
        
        $grouped = array();
        foreach ($devs as $did=>$dev) {
            
            if (property_exists($dev, $groupby)) {
                // TODO: get rid of delimited fields in the DB
                // Special case(s) 
                // For sections, we have to split on ';' and add the device
                // to the hash under each section id to which it is associated
                if ($groupby === "sections") {
                    $sections = explode(";", $dev->sections);
                    if (!empty($sections[0])) {
                        $gkeys = $sections;
                    } else {
                        $gkeys = ['No sections'];
                    }
                } else {
                    $gkeys = (empty($dev->$groupby) ? ["No $groupby"] : [$dev->$groupby]);
                }
            } else {
                $gkeys = ['No ' . $groupby];
            }
            
            foreach ($gkeys as $gk) {
                $grouped[$gk][] = $dev;
            }
        }
        
        // Neat but unnecessary right now as SQL sorting is working fine
        // if ($sort) {
        //     foreach ($grouped as $g=>&$ds) {
        //         usort($ds, Tools::sort_objs($sort));
        //     }
        // }
        
        // Sort the keys
        ksort($grouped);
        
        return $grouped;
    }
    
    /**
	 * Fetch objects with a specific db query
	 *
	 * @access private
	 * @param string $query The SQL query in template form
     * @param array $values Values to inject into the sql query template
     * @param string $sort Optional column name on which to sort
     * @param string $groupby Optional object property name to use for grouping the results (see _group())
	 * @return array|bool
	 */
    private function _byquery($query, $values = [], $sort = false, $groupby = false) {
        $devs = $this->Database->getObjectsQuery($query,$values,$sort);
        if ($groupby) {
            $devs = $this->_group($devs, $groupby, $sort);
        }
        return $devs;
    }
    
    /**
	 * Fetch all device objects
	 *
	 * @access public
     * @param string $sort Optional column name on which to sort
     * @param string $groupby Optional object property name to use for grouping the results (see _group())
	 * @return array
	 */
    public function all($sort = false, $groupby = false){
        $sort = ($sort ? $sort : $this->defsort);
        $devs = $this->_all($sort);
        
        if ($groupby) {
            return $this->_group($devs, $groupby);
        } else {
            return $devs;
        }
    }
    
    /**
	 * Fetch all device objects for a particular section.
     * TODO: this query is mysql specific. should generalize it.
	 *
	 * @access public
     * @param int $sid Section ID
     * @param string $sort Optional column name on which to sort
     * @param string $groupby Optional object property name to use for grouping the results (see _group())
	 * @return array
	 */
    public function for_section($sid, $sort = NULL, $groupby = false) {
        $sort = ($sort === NULL ? $this->defsort : $sort);
        // Put the sid directly into the query because parameterizing the regex query does not appear to work
        $q = 'SELECT * from devices where sections regexp "(;*)' . $sid . '(;*)"';
        return $this->_byquery($q,[],$sort,$groupby);
    }
    
    /**
	 * Fetch all device objects for a particular location
	 *
	 * @access public
     * @param int $lid Location ID
     * @param string $sort Optional column name on which to sort
     * @param string $groupby Optional object property name to use for grouping the results (see _group())
	 * @return array
	 */
    public function for_location($lid, $sort = NULL, $groupby = false) {
        $sort = ($sort === NULL ? $this->defsort : $sort);
        $q = 'SELECT * from devices where location=?';
        return $this->_byquery($q,[$lid],$sort,$groupby);
    }
    
    /**
	 * Fetch all device types
	 *
	 * @access public
	 * @return array Device type objects
	 */
    public function types($sort='name') {
        return $this->fetch_all_objects($this->typetbl,$sort);
    }
    
    /**
	 * Fetch device type name by ID
	 *
	 * @access public
     * @param int $tid Device type ID
	 * @return string|bool Device type name or false if no result
	 */
    public function type_name($tid) {
        $q = "SELECT name from `" . $this->typetbl . "` where id=?";
        $ts = $this->_byquery($q,[$tid]);
        if (empty($ts)) {
            return false;
        } else {
            return $ts[0]->name;
        }
    }
}


?>
