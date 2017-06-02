<?php

/**
 *	phpIPAM API class to work with devices.
 *  TODO: Integrate with the Locations class
 */
class Locations_controller extends Common_api_functions
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
    protected $default_search_fields = ['name','description','address'];

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
        $this->set_valid_keys("locations");
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
                                array("href"=>"/api/".$this->_params->app_id."/locations/",
                                      "methods"=>array(
                                          array("rel"=>"options",
                                                "method"=>"OPTIONS"))),
                                array("href"=>"/api/".$this->_params->app_id."/locations/search/{search_term}",
                                      "methods"=>array(
                                          array("rel"=>"search",
                                                "method"=>"GET"))),
                                array("href"=>"/api/".$this->_params->app_id."/locations/{id}/",
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
            $result = $this->Tools->fetch_all_objects('locations', 'id');
            return array('code' => 200, 'data' => $this->prepare_result($result, 'locations', true, false));
        } else {
            if ($this->_params->id == 'search') {
                if (isset($this->_params->id2)) {
                    $base_query = "SELECT * from locations where ";
                    
                    # Search all custom fields
                    $cfs = array_keys($this->Tools->fetch_custom_fields('locations'));
                    
                    # Merge default fields with custom fields
                    $search_fields = array_merge($cfs, $this->default_search_fields);
                    
                    # Using the search fields, build a string to query parameters chained together with " or "
                    $search_term = $this->_params->id2;
                    $extended_query = implode(' or ', array_map(
                                                         function ($k) {
                                                             return " $k like ? ";
                                                         }, $search_fields));
                    
                    # Set up an array of parameters to match the query we built
                    $query_params = array_fill(0, count($search_fields), "%$search_term%");
                    
                    # Put together with the base query
                    $search_query = $base_query . $extended_query;

                    # Search query
                    $devices = $this->Database->getObjectsQuery($search_query, $query_params);
                    
                    return array('code' => 200, 'data' => $this->prepare_result($devices, 'locations', true, false));
                } else {
                    $this->Response->throw_exception(500, 'No search term given');
                }
                # search stuff
            } else {
            
                // numeric
                if (!is_numeric($this->_params->id)) {
                    $this->Response->throw_exception(404, 'ID must be numeric');
                }

                $result = $this->Tools->fetch_object('locations', 'id', $this->_params->id);
                if (!$result) {
                    $this->Response->throw_exception(404, 'Location not found');
                }

                return array('code' => 200, 'data' => $this->prepare_result($result, 'locations', true, false));
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

        # only 1 parameter ?
        if (sizeof($values) == 1) {
            $this->Response->throw_exception(400, 'No parameters');
        }

        # execute update
        if (!$this->Admin->object_modify('locations', 'add', '', $values)) {
            $this->Response->throw_exception(500, $this->_params->id.' object creation failed');
        } else {
            $result = $this->Tools->fetch_object('locations', 'id', $this->Admin->lastId);
            return array('code' => 201, 'data' => $this->prepare_result($result, 'locations', true, false));
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

        # validate and prepare keys
        $values = $this->validate_keys();

        # only 1 parameter ?
        if (sizeof($values) == 1) {
            $this->Response->throw_exception(400, 'No parameters');
        }

        # execute update
        if (!$this->Admin->object_modify('locations', 'edit', 'id', $values)) {
            $this->Response->throw_exception(500, $table_name.' object edit failed');
        } else {
            // fetch the updated object and hand it back to the client
            $result = $this->Tools->fetch_object('locations', 'id', $values['id']);
            return array('code' => 200, 'data' => $this->prepare_result($result, 'locations', true, false));
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
        if (!$this->Admin->object_modify('locations', 'delete', 'id', $values)) {
            $this->Response->throw_exception(500, $this->_params->id.' object delete failed');
        } else {
            // set result
            return array('code' => 200, 'data' => 'deleted location ' . $this->_params->id);
        }
    }
    
}
