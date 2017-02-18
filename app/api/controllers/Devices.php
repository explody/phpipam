<?php

/**
 *	phpIPAM API class to work with devices.
 */
class Devices_controller extends Common_api_functions
{
    /**
     * _params provided.
     *
     * @var mixed
     */
    public $_params;

    /**
     * subcontrollers.
     *
     * @var mixed
     */
    protected $subcontrollers;

    /**
     * identifiers.
     *
     * @var mixed
     */
    protected $identifiers;

    /**
     * Database object.
     *
     * @var mixed
     */
    protected $Database;

    /**
     * Response.
     *
     * @var mixed
     */
    protected $Response;

    /**
     * Master Tools object.
     *
     * @var mixed
     */
    protected $Tools;

    /**
     * Main Admin class.
     *
     * @var mixed
     */
    protected $Admin;

    /**
     * Main Subnets class.
     *
     * @var mixed
     */
    protected $Subnets;
    
    /**
     * Default fields to search.
     *
     * @var mixed
     */
    protected $default_search_fields = ['hostname','ip_addr','description','version'];

    /**
     * __construct function.
     *
     * @param class $Database
     * @param class $Tools
     * @param mixed $params   // post/get values
     * @param class $Response
     */
    public function __construct($Database, $Tools, $params, $Response)
    {
        $this->Database = $Database;
        $this->Response = $Response;
        $this->Tools = $Tools;
        $this->_params = $params;
        
        // init required objects
        $this->init_object('Admin', $Database);
        $this->init_object('Subnets', $Database);
        
        // set valid keys
        $this->set_valid_keys("devices");
    }

    /**
     * returns general Controllers and supported methods.
     */
    public function OPTIONS()
    {
        // validate
        $this->validate_options_request();

        // get api
        $app = $this->Tools->fetch_object('api', 'app_id', $this->_params->app_id);

        // methods
		$result = array();
		$result['methods'] = array(
								array("href"=>"/api/".$this->_params->app_id."/devices/", 		
                                      "methods"=>array(
                                          array("rel"=>"options", 
                                                "method"=>"OPTIONS"))),
                                array("href"=>"/api/".$this->_params->app_id."/devices/search/{search_term}", 		
                                      "methods"=>array(
                                          array("rel"=>"search", 
                                                "method"=>"GET"))),                
								array("href"=>"/api/".$this->_params->app_id."/devices/{id}/", 	
                                      "methods"=>array(
                                          array("rel"=>"read", "method"=>"GET"),
										  array("rel"=>"create", "method"=>"POST"),
										  array("rel"=>"update", "method"=>"PATCH"),
										  array("rel"=>"delete", "method"=>"DELETE")
                                      )
                                ),
                             );
        # Response
        return array('code' => 200, 
                     'data' => array('permissions' => $this->Subnets->parse_permissions($app->app_permissions), 
                                     'controllers' => $result));
    }

    /**
     *
     *	structure:
     *		/devices/{id|action}/{parameter}/
     *
     *		/devices/id/id2/id3/
     *
     *		- {id|action}		- defines id for that object or an action (optional)
     *		- {parameter}		- additional parameter (optional)
     */
    public function GET()
    {

        if (!isset($this->_params->id)) {
            $result = $this->Tools->fetch_all_objects('devices',  'id');
            // result
            if (!$result) {
                $result = [];  # empty set is not the same as a 404
            } 
            return array('code' => 200, 'data' => $this->prepare_result($result, 'devices', true, false));
            
        } else {
            
            if ($this->_params->id == 'search') {
                if (isset($this->_params->id2)) {
                    
                    
                    $base_query = "SELECT * from devices where ";
                    
                    # Search all custom fields
                    $cfs = array_keys($this->Tools->fetch_custom_fields('devices'));
                    
                    # Merge default fields with custom fields
                    $search_fields = array_merge($cfs, $this->default_search_fields);
                    
                    # Using the search fields, build a string to query parameters chained together with " or "
                    $search_term = $this->_params->id2;
                    $extended_query = implode(' or ', array_map( 
                                                         function($k) { 
                                                             return " $k like ? "; 
                                                         }, $search_fields));
                    
                    # Set up an array of parameters to match the query we built
                    $query_params = array_fill(0, count($search_fields), "%$search_term%");
                    
                    # Put together with the base query
                    $search_query = $base_query . $extended_query;

                    # Search query
                    $devices = $this->Database->getObjectsQuery($search_query, $query_params);
                    
                    return array('code' => 200, 'data' => $this->prepare_result($devices, 'devices', true, false));
                } else {
                    $this->Response->throw_exception(500, 'No search term given');
                }
                # search stuff
            } else {
            
                // numeric
                if (!is_numeric($this->_params->id)) {
                    $this->Response->throw_exception(404, 'ID must be numeric');
                }
                
                if ($this->_params->id2 == 'addresses') {
                    $result = $this->Tools->fetch_multiple_objects("ipaddresses", 'switch', $this->_params->id, 'id', true);
                } else {
                    $result = $this->Tools->fetch_object('devices', 'id', $this->_params->id);
                    if (!$result) {
                        $this->Response->throw_exception(404, 'Device not found');
                    }
                }

                return array('code' => 200, 'data' => $this->prepare_result($result, 'devices', true, false));

            }
        }
    }

    /**
     * Creates a new device
     *
     *		/devices
     */
    public function POST()
    {
        # Put incoming keys in order
        $this->remap_keys();
        
        # check for valid keys
        $values = $this->validate_keys();

        # validations
        $this->validate_post_patch();

        # only 1 parameter ?
        if (sizeof($values) == 1) {
            $this->Response->throw_exception(400, 'No parameters');
        }
        #return array('code' => 200, 'data' => $values);
        # execute update
        if (!$this->Admin->object_modify('devices', 'add', '', $values)) {
            $this->Response->throw_exception(500, $this->_params->id.' object creation failed');
        } else {
            $result = $this->Tools->fetch_object('devices', 'id', $this->Admin->lastId);
            return array('code' => 201, 'data' => $this->prepare_result($result, 'devices', true, false));
        }
    }

    /**
     * HEAD, no response.
     */
    public function HEAD()
    {
        return $this->GET();
    }

    /**
     * Updates a device
     */
    public function PATCH()
    {

        # Put incoming keys back in order
        $this->remap_keys(); 
        
        # validations
        $this->validate_post_patch();

        # validate and prepare keys
        $values = $this->validate_keys();

        # only 1 parameter ?
        if (sizeof($values) == 1) {
            $this->Response->throw_exception(400, 'No parameters');
        }

        # execute update
        if (!$this->Admin->object_modify('devices', 'edit',  'id', $values)) {
            $this->Response->throw_exception(500, $table_name.' object edit failed');
        } else {
            // fetch the updated object and hand it back to the client
            $result = $this->Tools->fetch_object('devices', 'id', $values['id']);
            return array('code' => 200, 'data' => $this->prepare_result($result, 'devices', true, false));
        }
    }

    /**
     * Deletes existing vlan.
     */
    public function DELETE()
    {

        # set variables for delete
        $values = array();
        $values['id'] = $this->_params->id;

        # execute delete
        if (!$this->Admin->object_modify('devices', 'delete', 'id', $values)) {
            $this->Response->throw_exception(500, $this->_params->id.' object delete failed');
        } else {

            // delete all references
            $this->Admin->remove_object_references('ipaddresses', 'switch', $this->_params->id);

            // set result
            return array('code' => 200, 'data' => 'deleted device ' . $this->_params->id);
        }
    }

    /**
     * Validations for post and patch.
     */
    private function validate_post_patch()
    {
        $this->validate_device_type();
        #$this->validate_ip();
    }

    /**
     * Validates device type.
     */
    private function validate_device_type()
    {
        if (isset($this->_params->type)) {
            // numeric
            if (!is_numeric($this->_params->type)) {
                $this->Response->throw_exception(400, 'Invalid devicetype identifier');
            }
            // check
            if ($this->Tools->fetch_object('deviceTypes', 'id', $this->_params->type) === false) {
                $this->Response->throw_exception(400, 'Device type does not exist');
            }
        }
    }
    
    /**
	 * Validates IP address
	 *
	 * @access private
	 * @return void
	 */
	private function validate_ip () {
		if (isset($this->_params->ip_addr)) {
			// check
			if(strlen($err = $this->Subnets->verify_cidr_address($this->_params->ip_addr."/32"))>1) { 
                $this->Response->throw_exception(400, $err); 
            }

		}
	}
}
