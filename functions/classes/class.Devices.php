<?php

/**
*	phpIPAM Devices class
*/

class Devices extends Tools {
    
    /**
     * Tools class
     *
     * @var mixed
     * @access public
     */
    public $Tools;

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
        'sections' =>'sections',
        'location' =>'locations',
        'rack'     =>'racks',
        'type'     =>'deviceTypes'
    );
      
    /**
     * constructor
     *
     * @access public
     */
    public function __construct (Database_PDO $database) {
    	# Save database object
    	$this->Database = $database;
    	# initialize Tools
    	$this->Tools = new Tools ($this->Database);
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
	 * Fetch device objects with a specific db query
	 *
	 * @access private
	 * @param string $query The SQL query in template form
     * @param array $values Values to inject into the sql query template
     * @param string $sort Optional column name on which to sort
     * @param string $groupby Optional object property name to use for grouping the results (see Common:group_objects)
	 * @return array|bool
	 */
    private function _byquery($query, $values = [], $sort = false, $groupby = false) {
        $devs = $this->Database->getObjectsQuery($query,$values,$sort);
        if ($groupby) {
            $devs = Tools::group_objects($devs, $groupby, $sort);
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
