<?php

/**
 *	phpIPAM API class for common functions
 *
 *
 */
class Common_api_functions {


	/**
	 * controller_keys
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $controller_keys;

	/**
	 * _params provided from request
	 *
	 * @var mixed
	 * @access public
	 */
	public $_params;

	/**
	 * Lock transaction to avoid duplicate entries or errors
	 *
	 * (default value: 0)
	 *
	 * @var bool
	 * @access public
	 */
	public $lock = 0;

	/**
	 * File to write lock to
	 *
	 * (default value: "_lock.txt")
	 *
	 * @var string
	 * @access public
	 */
	public $lock_file_name = "/tmp/phpipam_api_lock.txt";

    /**
     * File handler
     *
     * (default value: false)
     *
     * @var bool|resource
     * @access private
     */
    private $lock_file_handler = false;

    /**
     * Custom fields
     *
     * @var mixed
     * @access public
     */
    public $custom_fields;

	/**
	 * valid_keys
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $valid_keys;

	/**
	 * custom_keys
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $custom_keys;

	/**
	 * Keys to be removed
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $remove_keys = ['editDate','links'];

	/**
	 * keys
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $keys;

	/**
	 * Master Tools class
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Tools;

	/**
	 * Response class
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Response;

	/**
	 * Master subnets class
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Subnets;

	/**
	 * Initializes new Object.
	 *
	 * @access protected
	 * @param mixed $Object_name		// object name
	 * @param mixed $Database	       // Database object
	 */
	protected function init_object ($Object_name, $Database) {
		// admin fix
		if($Object_name=="Admin")	    { $this->{$Object_name}	= new $Object_name ($Database, false); }
		// User fix
		elseif($Object_name=="User")	{ $this->{$Object_name}	= new $Object_name ($Database, true); $this->{$Object_name}->user = null; }
		// default
		else					        { $this->{$Object_name}	= new $Object_name ($Database); }
		// set exit method
		$this->{$Object_name}->Result->exit_method = "exception";
		// set API flag
		$this->{$Object_name}->api = true;
	}

	/**
	 * Sets valid keys for actions
	 *
	 * @access protected
	 * @param mixed $controller
	 * @return void
	 */
	protected function set_valid_keys ($controller) {
		# array of controller keys
		$this->controller_keys = array("app_id", "controller");

		# array of all valid keys - fetch from SCHEMA
		$this->valid_keys = $this->Tools->fetch_standard_fields ($controller);

		# add custom fields
		$custom_fields = $this->Tools->fetch_custom_fields($controller);
		if(sizeof($custom_fields)>0) {
			foreach($custom_fields as $cf) {
				$this->custom_keys[] = $cf['name'];
			}
		}

		# save custom fields
		$this->custom_fields = $custom_fields;

		# merge all
		$this->valid_keys = array_merge($this->controller_keys, $this->valid_keys);
		if(isset($this->custom_keys)) {
			$this->valid_keys = array_merge($this->valid_keys, $this->custom_keys);
		}

		# remove keys that we universally ignore inbound
		foreach($this->valid_keys as $k=>$v) {
			if(in_array($v, $this->remove_keys)) {
				unset($this->valid_keys[$k]);
			}
		}
	}

	/**
	 * Prepares result, creates links if requested and transforms address/subnet to
	 *	decimal format
	 *
	 * @access protected
	 * @param mixed $result
	 * @param mixed $controller (default: null)
	 * @param bool $links (default: true)
	 * @param bool $transform_address (default: true)
	 * @return void
	 */
	protected function prepare_result ($result, $controller = null, $links = true, $transform_address = true) {
		// empty controller
		$controller = is_null($controller) ? $this->_params->controller : $controller;

		// links
		if($links) {
			// explicitly set to no
			if(@$this->_params->links!="false")
								{ $result = $this->add_links ($result, $controller); }
		}
		// filter
		if (isset($this->_params->filter_by)) {
								{ $result = $this->filter_result ($result); }
		}
		// transform address
		if($transform_address)	{ $result = $this->transform_address ($result); }

		// remove subnets and addresses if needed
		$result = $this->remove_folders ($result);
		$result = $this->remove_subnets ($result);

		// remap keys
        if ($result) {
    		$result = $this->remap_keys ($result);
        }

		# return
		return $result;
	}

	/**
	 * Filters result
	 *
	 *	parameters: filter_by, filter_value
	 *
	 * @access protected
	 * @param mixed $result
	 * @return void
	 */
	protected function filter_result ($result) {
    	// remap keys before applying filter
    	$result = $this->remap_keys ($result);
		// validate
		$this->validate_filter_by ($result);

		// filter - array
		if (is_array($result)) {
			foreach ($result as $m=>$r) {
				foreach ($r as $k=>$v) {
					if ($k == $this->_params->filter_by) {
						if ($v != $this->_params->filter_value) {
							unset($result[$m]);
							break;
						}
					}
				}
			}
		}
		// filter - single
		else {
			foreach ($result as $k=>$v) {
				if ($k == $this->_params->filter_by) {
					if ($v != $this->_params->filter_value) {
						unset($result);
						break;
					}
				}
			}
		}

		# null?
		if (sizeof($result)==0)				{ $this->Response->throw_exception(404, 'No results (filter applied)'); }
        # reindex filtered result
        else {
            $result = array_values($result);
        }


		# result
		return $result;
	}

	/**
	 * Validates filter_by
	 *
	 *  Takes first result, checks all keys against provided filter_by value
	 *
	 * @access protected
	 * @param mixed $result
	 * @return void
	 */
	protected function validate_filter_by ($result) {
		// validate filter
		if (is_array($result))	{ $result_tmp = $result[0]; }
		else					{ $result_tmp = $result; }

        // validate filter_value
        if(!isset($this->_params->filter_value)) {
            $this->Response->throw_exception(400, 'Missing filter_value');
        }
        elseif (strlen($this->_params->filter_value)==0) {
            $this->Response->throw_exception(400, 'Empty filter_value');
        }

        // validate filter_by
		$error = true;
		if(is_array($result_tmp)) {
    		foreach ($result_tmp as $k=>$v) {
    			if ($k==$this->_params->filter_by) {
    				$error = false;
    			}
    		}
		}
		else {
    		$error = false;
		}

		// die
		if ($error)	{
    		$this->Response->throw_exception(400, 'Invalid filter_by');
        }
	}

	/**
	 * Creates links for GET requests
	 *
	 * @access private
	 * @param mixed $result
	 * @param mixed $controller
	 * @return void
	 */
	protected function add_links ($result, $controller=null) {
		// lower controller
		$controller = strtolower($controller);

		// multiple options
		if(is_array($result)) {
			foreach($result as $k=>$r) {
				// fix for Vlans and vrfs
				if($controller=="vlans")				{ $r->id = $r->vlanId; }
				if($controller=="tools/vlans")			{ $r->id = $r->vlanId; }
				if($controller=="vrfs")					{ $r->id = $r->vrfId; }

				$m=0;
				// custom links
				$custom_links = $this->define_links ($controller);
				if($custom_links!==false) {
					foreach($this->define_links ($controller) as $link=>$method) {
						// self only !
						if ($link=="self") {
						$result[$k]->links[$m] = new stdClass ();
						$result[$k]->links[$m]->rel  	= $link;
						$result[$k]->links[$m]->href 	= "/api/".$this->_params->app_id."/$controller/".$r->id."/";
						}
					}
				}

				// remove id for vlans
				if($controller=="vlans")	{ unset($r->id); }
				if($controller=="vrfs")		{ unset($r->id); }
			}
		}
		// single item
		else {
				// fix for Vlans and Vrfs
				if($controller=="vlans")				{ $result->id = $result->vlanId; }
				if($controller=="tools/vlans")			{ $result->id = $result->vlanId; }
				if($controller=="vrfs")					{ $result->id = $result->vrfId; }

				$m=0;
				// custom links
				$custom_links = $this->define_links ($controller);
				if($custom_links!==false) {
					foreach($this->define_links ($controller) as $link=>$method) {
						$result->links[$m] = new stdClass ();
						$result->links[$m]->rel  	= $link;
						// self ?
						if ($link=="self")
						$result->links[$m]->href 	= "/api/".$this->_params->app_id."/$controller/".$result->id."/";
						else
						$result->links[$m]->href 	= "/api/".$this->_params->app_id."/$controller/".$result->id."/$link/";
						$result->links[$m]->methods = $method;
						// next
						$m++;
					}
				}

				// remove id for vlans
				if($controller=="vlans")	{ unset($result->id); }
				if($controller=="vrfs")		{ unset($result->id); }
		}
		# return
		return $result;
	}

	/**
	 * Defines links for controller
	 *
	 * @access private
	 * @param mixed $controller
	 * @return void
	 */
	private function define_links ($controller) {
    	// init
    	$result = array();
		// sections
		if($controller=="sections") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["subnets"]          = array ("GET");
			// return
			return $result;
		}
		// subnets
		elseif($controller=="subnets") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["addresses"]        = array ("GET");
			$result["addresses/{ip}"]   = array ("GET");
			$result["gateway"]          = array ("GET");
			$result["usage"]            = array ("GET");
			$result["first_free"]       = array ("GET");
			$result["slaves"]           = array ("GET");
			$result["slaves_recursive"] = array ("GET");
			$result["truncate"]         = array ("DELETE");
			$result["permissions"]      = array ("DELETE");
			$result["resize"]           = array ("PATCH");
			$result["split"]            = array ("PATCH");
			$result["permissions"]      = array ("PATCH");
			// return
			return $result;
		}
		// addresses
		elseif($controller=="addresses") {
			$result["self"]				= array ("GET","POST","DELETE","PATCH");
			$result["ping"]				= array ("GET");
			// return
			return $result;
		}
		// tags
		elseif($controller=="addresses/tags") {
			$result["self"]				= array ("GET");
			$result["addresses"]		= array ("GET");
			// return
			return $result;
		}
		// tools - devices
		elseif($controller=="devices") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["addresses"]        = array ("GET");
			// return
			return $result;
		}
		// tools - devices
		elseif($controller=="tools/devicetypes") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["devices"]        	= array ("GET");
			// return
			return $result;
		}
		// tools - tags
		elseif($controller=="tools/iptags") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["addresses"]        = array ("GET");
			// return
			return $result;
		}
		// tools - tags
		elseif($controller=="tools/vlans") {
			$result["self"]			 	= array ("GET");
			$result["subnets"]          = array ("GET");
			// return
			return $result;
		}
		// tools - tags
		elseif($controller=="tools/vrf") {
			$result["self"]			 	= array ("GET");
			$result["subnets"]          = array ("GET");
			// return
			return $result;
		}
		// tags
		elseif($controller=="iptags") {
			$result["self"]				= array ("GET");
			$result["addresses"]		= array ("GET");
			// return
			return $result;
		}
		// tags
		elseif($controller=="devices") {
			$result["self"]				= array ("GET");
			$result["addresses"]		= array ("GET");
			// return
			return $result;
		}
		// vlan domains
		elseif($controller=="l2domains") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["vlans"]          	= array ("GET");
			// return
			return $result;
		}
		// vlans
		elseif($controller=="vlans") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["subnets"]          = array ("GET");
			// return
			return $result;
		}
		// vrfs
		elseif($controller=="vrfs") {
			$result["self"]			 	= array ("GET","POST","DELETE","PATCH");
			$result["subnets"]          = array ("GET");
			// return
			return $result;
		}

		// default
		return false;
	}

	/**
	 * Transforms IP address and subnet
	 *
	 * @access protected
	 * @param mixed $result
	 * @return void
	 */
	protected function transform_address ($result) {
		// multiple options
		if (is_array($result)) {
			foreach($result as $k=>$r) {
				// remove IP
				if (isset($r->ip))					{ unset($r->ip); }
				// transform
				if (isset($r->subnet))				{ $r->subnet  = $this->Subnets->transform_address ($r->subnet,  "dotted"); }
				elseif (isset($r->ip_addr))			{ $r->ip_addr = $this->Subnets->transform_address ($r->ip_addr, "dotted"); }
			}
		}
		// single item
		else {
				// remove IP
				if (isset($result->ip))				{ unset($result->ip); }
				// transform
				if (isset($result->subnet))			{ $result->subnet  = $this->Subnets->transform_address ($result->subnet,  "dotted"); }
				elseif (isset($result->ip_addr))	{ $result->ip_addr = $this->Subnets->transform_address ($result->ip_addr, "dotted"); }
		}

		# return
		return $result;
	}

	/**
	 * Validates posted keys and returns proper inset values
	 *
	 * @access private
	 * @return void
	 */
	protected function validate_keys () {
    	// init values
    	$values = array();
    	// loop
		foreach($this->_params as $pk=>$pv) {
			if(!in_array($pk, $this->valid_keys) && !in_array($pk, $this->remove_keys)) { 
                $this->Response->throw_exception(400, 'Invalid request key '.$pk); 
            }
			// set parameters
			else {
				if(!in_array($pk, $this->controller_keys)) {
					 $values[$pk] = $pv;
				}
			}
		}
        
        # Quietly drop any ignored keys
        foreach ($this->remove_keys as $ik) {
    		unset($values[$ik]);
        }
        
		# return
		return $values;
	}

	/**
	 * Validates OPTIONS request
	 *
	 * @access protected
	 * @return void
	 */
	protected function validate_options_request () {
		foreach($this->_params as $key=>$val) {
			if(!in_array($key, array("app_id", "controller", "id"))) {
													{ $this->Response->throw_exception(400, 'Invalid request key parameter '.$key); }
			}
		}
	}

	/**
	 * Validates MAC address
	 *
	 * @access public
	 * @param mixed $mac
	 * @return void
	 */
	public function validate_mac ($mac) {
    	// first put it to common format (1)
    	$mac = $this->reformat_mac_address ($mac);
    	// we permit empty
        if (strlen($mac)==0)                                                            { return true; }
    	elseif (preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac) != 1)   { return false; }
    	else                                                                            { return true; }
	}

	/**
	 * Reformats MAC address to requested format
	 *
	 * @access public
	 * @param mixed $mac
	 * @param string $format (default: 1)
	 *      1 : 00:66:23:33:55:66
	 *      2 : 00-66-23-33-55-66
	 *      3 : 0066.2333.5566
	 *      4 : 006623335566
	 * @return void
	 */
	public function reformat_mac_address ($mac, $format = 1) {
    	// strip al tags first
    	$mac = strtolower(str_replace(array(":",".","-"), "", $mac));
    	// format 4
    	if ($format==4) {
        	return $mac;
    	}
    	// format 3
    	if ($format==3) {
        	$mac = str_split($mac, 4);
        	$mac = implode(".", $mac);
    	}
    	// format 2
    	elseif ($format==2) {
        	$mac = str_split($mac, 2);
        	$mac = implode("-", $mac);
    	}
    	// format 1
    	else {
        	$mac = str_split($mac, 2);
        	$mac = implode(":", $mac);
    	}
    	// return
    	return $mac;
	}

	/**
	 * Returns array of possible permissions
	 *
	 * @access public
	 * @return void
	 */
	public function get_possible_permissions () {
		// set
		$permissions = array(
    		            "na"=>0,
    		            "ro"=>1,
    		            "rw"=>2,
    		            "rwa"=>3
                        );
        // return
		return $permissions;
	}

	/**
	 * This method removes all folders if controller is subnets
	 *
	 * @access protected
	 * @param mixed $result
	 * @return void
	 */
	protected function remove_folders ($result) {
		// must be subnets
		if($this->_params->controller!="subnets") {
			return $result;
		}
		else {
			// multiple options
			if (is_array($result)) {
				foreach($result as $k=>$r) {
					// remove
					if($r->isFolder=="1")				{ unset($r); }
			}	}
			// single item
			else {
					// remove
					if($result->isFolder=="1")			{ unset($result); }
			}
			# return
			if($result===false)	{ $this->Response->throw_exception(404, "No subnets found"); }
			else				{ return $result; }
	}	}

	/**
	 * This method removes all subnets if controller is subnets
	 *
	 * @access protected
	 * @param mixed $result
	 * @return void
	 */
	protected function remove_subnets ($result) {
		// must be subnets
		if($this->_params->controller!="folders") {
			return $result;
		}
		else {
			// multiple options
			if (is_array($result)) {
				foreach($result as $k=>$r) {
					// remove
					if($r->isFolder!="1")				{ unset($r); }
			}	}
			// single item
			else {
					// remove
					if($result->isFolder!="1")			{ unset($result); }
			}
			# return
			if($result===NULL)	{ $this->Response->throw_exception(404, "No folders found"); }
			else				{ return $result; }
	}	}




	/**
	 * Remaps keys based on request type
	 *
	 * @access protected
	 * @param mixed $result (default: null)
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	protected function remap_keys ($result = false) {
		// define keys array
		$this->keys = array("switch"=>"deviceId", "state"=>"tag", "ip_addr"=>"ip", "dns_name"=>"hostname");

		// exceptions
		if($controller=="vlans") { 
            $this->keys['vlanId'] = "id"; 
        }
		if($controller=="vrfs") { 
            $this->keys['vrfId'] = "id"; 
        }

		// If we have been given result data, it's presumed to be outbound to a client so remap as results
		if ($result) { 
            return $this->remap_result_keys ($result); 
        }
        // Otherwise, it's inbound data from a client so remap as an inbound add/update
		else {
            return $this->remap_update_keys (); 
        }
	}

	/**
	 * Updates request keys to database ones
	 *
	 * @access private
	 * @return void
	 */
	private function remap_update_keys () {
		// loop
		foreach($this->keys as $k=>$v) {
            
            // If the existing key is in the controller's keys but the new key is not, do not remap it
            if( in_array($v, $this->valid_keys) && !in_array($k, $this->valid_keys) ) {
                continue;
            }
            
			// match
			if(array_key_exists($v, $this->_params)) {
				// replace
				$this->_params->{$k} = $this->_params->{$v};
				// remove
				unset($this->_params->{$v});
			}
		}
	}

	/**
	 * Remap result keys - what is offered to client
	 *
	 * @access private
	 * @param mixed $result
	 * @return void
	 */
	private function remap_result_keys ($result) {
		# single
		if(!is_array($result)) {
			// params
			$result_remapped = new StdClass ();
			// search and replace
			foreach($result as $k=>$v) {
				if(array_key_exists($k, $this->keys)) {
					// replace
					$key = $this->keys[$k];
					$result_remapped->{$key} = $v;
				}
				else {
					$result_remapped->{$k} = $v;
				}
			}
		}
		# array
		else {
			// create a new array for the remapped data
			$result_remapped = array();

			// loop
			foreach ($result as $m=>$r) {
				// start object
				$result_remapped[$m] = new StdClass ();

				// search and replace
				foreach($r as $k=>$v) {
					if(array_key_exists($k, $this->keys)) {
						// replace
						$key_val = $this->keys[$k];
						$result_remapped[$m]->{$key_val} = $v;
					}
					else {
						$result_remapped[$m]->{$k} = $v;
					}
				}
			}
		}

		# result
		return $result_remapped;
	}






    /* ! @transaction_locking --------------- */

    /**
     * Open file handler to manage lock file
     *
     * @access private
     * @return void
     */
    private function file_init_handler () {
        try {
            $this->lock_file_handler = fopen($this->lock_file_name, 'w');
        }
        catch ( Exception $e ) {
            $this->Response->throw_exception(500, "Cannot init file handler for $this->lock_file_name ".$e->getMessage());
        }
    }

    /**
     * Adds Exclusive lock and writes 1 to file
     *
     * @access private
     * @return void
     */
    private function file_add_lock () {
        try {
            // add lock
            flock($this->lock_file_handler, LOCK_EX);
            // write content
            $this->file_write_content ("1");
        }
        catch ( Exception $e ) {
            $this->Response->throw_exception(500, "Cannot add LOCK_UN to $this->lock_file_name ".$e->getMessage());
        }
    }
    /**
     * Removes exclusive lock
     *
     * @access private
     * @return void
     */
    private function file_remove_lock () {
        try {
            // write content
            $this->file_write_content ("0");
            // close handler
            fclose($this->lock_file_handler);
        }
        catch ( Exception $e ) {
            $this->Response->throw_exception(500, "Cannot remove LOCK_UN from $this->lock_file_name ".$e->getMessage());
        }
    }

    /**
     * Write content to file.
     *
     * @access private
     * @param string $content (default: "")
     * @return void
     */
    private function file_write_content ($content = "") {
        try {
            fwrite($this->lock_file_handler, $content);
        }
        catch ( Exception $e ) {
            $this->Response->throw_exception(500, "Cannot write content to $this->lock_file_name ".$e->getMessage());
        }
    }

	/**
	 * Resets lock file name
	 *
	 * @access public
	 * @param string $file (default: "")
	 * @return void
	 */
	public function set_transaction_lock_file ($file = "") {
        if(strlen($file)>0) {
            $this->lock_file_name = $file;
        }
	}

	/**
	 * Sets translaction lock
	 *
	 * @access public
	 * @return void
	 */
	public function add_transaction_lock () {
    	$this->file_init_handler ();
        $this->file_add_lock ();
	}

	/**
	 * Removes transaction lock
	 *
	 * @access public
	 * @return void
	 */
	public function remove_transaction_lock () {
    	$this->file_remove_lock();
	}

	/**
	 * Checks for lock
	 *
	 * @access public
	 * @return void
	 */
	public function is_transaction_locked () {
        // check for stalled lock file
        $this->check_stalled_file ();
        // response
        if(file_exists($this->lock_file_name)) {
            return file_get_contents($this->lock_file_name) == "1" ? true : false;
        }
        else {
            return false;
        }

	}

	/**
	 * Removes stalled lock file if needed
	 *
	 * @access private
	 * @return void
	 */
	private function check_stalled_file () {
    	if(file_exists($this->lock_file_name)) {
        	// if more that 60 seconds remove it
        	if((time() - filemtime($this->lock_file_name)) > 60) {
            	$this->file_init_handler ();
            	$this->remove_transaction_lock ();
        	}
    	}
	}

}

?>