<?php

require_once VENDOR . '/autoload.php';

/**
 * phpIPAM class with common functions, used in all other classes
 *
 * @author: Miha Petkovsek <miha.petkovsek@gmail.com>
 */
class Common_functions  {

    /**
     * settings
     *
     * (default value: null)
     *
     * @var mixed
     * @access public
     */
    public $settings = null;

    /**
     * If Jdon validation error occurs it will be saved here
     *
     * (default value: false)
     *
     * @var bool
     * @access public
     */
    public $json_error = false;

    /**
     * Cache file to store all results from queries to
     *
     *  structure:
     *
     *      [table][index] = (object) $content
     *
     *
     * (default value: array())
     *
     * @var array
     * @access public
     */
    public $cache = array();

    /**
     * cache_check_exceptions
     *
     * (default value: array())
     *
     * @var array
     * @access private
     */
    private $cache_check_exceptions = array();

    /**
     * Default font
     *
     * (default value: "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:12px)
     *
     * @var string
     * @access public
     */
    public $mail_font_style = "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:12px;color:#333;'>";

    /**
     * Default font
     *
     * (default value: "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:12px)
     *
     * @var string
     * @access public
     */
    public $mail_font_style_light = "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:11px;color:#777;'>";

    /**
     * Default font for links
     *
     * (default value: "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:12px)
     *
     * @var string
     * @access public
     */
    public $mail_font_style_href = "<font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:12px;color:#a0ce4e;'>";

    /**
     * Database
     *
     * @var mixed
     * @access protected
     */
    protected $Database;

    /**
     * Result
     *
     * @var mixed
     * @access public
     */
    public $Result;

    /**
     * Log
     *
     * @var mixed
     * @access public
     */
    public $Log;

    /**
     * Net_IPv4
     *
     * @var mixed
     * @access protected
     */
    protected $Net_IPv4;

    /**
     * Net_IPv6
     *
     * @var mixed
     * @access protected
     */
    protected $Net_IPv6;

    /**
     * NET_DNS object
     *
     * @var mixed
     * @access protected
     */
    protected $DNS2;

    /**
     * debugging flag
     *
     * @var mixed
     * @access protected
     */
    protected $debugging;







    /**************************
     * Static methods
     **************************/

    /**
     * For use with usort as the callable. Will sort array of objects on $prop
     * 
     * @access public
     * @param string $prop object property on which to sort
     */
     public static function sort_objs($prop) {
         return function ($a, $b) use ($prop) {
             return strnatcmp($a->$prop, $b->$prop);
         };
     }
     
     /**
      * Take a simple array of objects and return a count where a property matches a given value
      * 
      * @access public
      * @param array $objects An array of objects
      * @param string $prop object property to check
      * @param mixed $val value to compare with $prop
      * @return int 
      */
      public static function count_where($objects, $prop, $val) {
          $cnt = 0;
          foreach($objects as $obj) {
              if (property_exists($obj, $prop)) {
                  if ($obj->$prop == $val) {
                      $cnt++;
                  }
              }
          }
          return $cnt;
      }
     
     /**
      * Take a simple array of objects and return a 2D array, indexed on a given property
      * 
      * @access public
      * @param array $objects An array of objects
      * @param string $prop object property on which to sort
      * @return array 
      */
      public static function index_array($objects, $prop) {
          $indexed = [];
          foreach($objects as $obj) {
              $indexed[$obj->$prop] = $obj;
          }
          return $indexed;
      }

     /**
      *	Takes an array of objects and returns an array of <option> elements
      *  for use in <selects>.
      * 
      *  @access public
      *  @param array $objects List of objects on which to iterate 
      *  @param string $valprop Property of the objects to use for the <option> 
      *                         value
      *  @param string $nameprop Property of the objects to use for the content 
      *                          of the <option>. Defaults to the same as $valprop
      *  @param array $selected Array in {k=>v} where if object.k === v, we'll 
      *                         add 'selected'
      *  @param array $classes List of classes to add to the <option>
      */
      public static function generate_options($objects, 
                                              $valprop, 
                                              $nameprop = false, 
                                              $selected = false, 
                                              $classes = false) {
           $options = [];
           foreach ($objects as $obj) {
               $o = '<option value="' . $obj->$valprop . '"';
               if ($classes) {
                   $o = $o . 'class="' . implode(" ", $classes) . '"';
               }
               if ($selected) {
                   // This enables passing multiple possible values to check 
                   foreach ($selected as $k=>$v) {
                       if ($obj->$k === $v) {
                           $o = $o . ' selected';
                       }
                   }
               } 
               
               $o = $o . '>' . ($nameprop ? $obj->$nameprop : $obj->valprop) . '</option>';
               
               $options[] = $o;
           }
           return $options;
      }

      /**
       *  Takes an array of objects arranged in groups such as 
       *   [ grp => array(obj1,obj2,obj3) ]
       *  and generates an array of option groups and options such as
       *   [ '<optgroup' => array('<option>','<option>','<option>') ]
       *  WARN: does not generate or include a closing </optgroup> tag
       *  See generate_options() for param details
        * 
       *  @access public
       *  @param array $objects Array of objects as described above 
       *  @param array $gclasses List of classes to add to the <optgroup>
       *  @param array $oclasses List of classes to pass on to generate_options()
       */
       public static function generate_option_groups($objgroups, 
                                                     $valprop, 
                                                     $nameprop = false, 
                                                     $selected = false,
                                                     $gclasses = false,
                                                     $oclasses = false) {
            $optgroups = [];
            foreach ($objgroups as $grp=>$objects) {
                $options = self::generate_options($objects,$valprop,$nameprop,$selected,$oclasses);
                $og = '<optgroup label="' . $grp . '" ';
                if ($gclasses) {
                    $og = $og . 'class="' . implode(" ", $classes) . '" ';
                }
                $og = $og . '>';
                $optgroups[$og] = $options;
            }
            
            return $optgroups;
       }

       /**
        * Arrange an array of objects into a new array with specific object props 
        * as the keys.  Objects with missing or empty props will be put under 
        * a key called "No <property>".
        *
        * @access public
        * @param array $dev Array of objects, such as returned from getObjects()
        * @param string $groupby The property from the objects to use as array keys
        * @param bool $sort Sort the array on its keys before returning.
        * @return array
        */
       public static function group_objects($objs, $groupby, $sort=true) {
           
           $grouped = array();
           foreach ($objs as $okey=>$obj) {
               
               if (!empty($obj->$groupby)) {
                   // TODO: get rid of delimited fields in the DB
                   // Special case(s) 
                   // For sections, we have to split on ';' and add the device
                   // to the hash under each section id to which it is associated
                   if ($groupby === "sections") {
                       $sections = explode(";", $obj->sections);
                       if (!empty($sections[0])) {
                           $gkeys = $sections;
                       } else {
                           $gkeys = ['No sections'];
                       }
                   } else {
                       $gkeys = (empty($obj->$groupby) ? ["No $groupby"] : [$obj->$groupby]);
                   }
               } else {
                   $gkeys = ['No ' . $groupby];
               }
               
               foreach ($gkeys as $gk) {
                   $grouped[$gk][] = $obj;
               }
           }
           
           // Sort the keys
           if ($sort) {
               ksort($grouped);
           }
           
           return $grouped;
       }
       
       
    /**
     *	@general fetch methods
     *	--------------------------------
     */


    /**
     * Fetch all objects from specified table in database
     *
     * @access public
     * @param mixed $table
     * @param mixed $sortField (default:id)
     * @param mixed bool (default:true)
     * @return bool|object
     */
    public function fetch_all_objects ($table=null, $sortField="id", $sortAsc=true) {
        # null table
        if(is_null($table)||strlen($table)==0) return false;
        # fetch
        try { $res = $this->Database->getObjects($table, $sortField, $sortAsc); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage());
            return false;
        }
        # save
        if (sizeof($res)>0) {
            foreach ($res as $r) {
                $this->cache_write ($table, $r->id, $r);
            }
        }
        # result
        return sizeof($res)>0 ? $res : false;
    }
    
    /**
     * Fetch all objects from specified table in database
     * TODO: violates DRY
     *
     * @access public
     * @param mixed $table
     * @param mixed $sortField (default:id)
     * @param int $limit (default:100)
     * @param int $offset (default:0)
     * @param mixed bool (default:true)
     * @return bool|object
     */
    public function fetch_objects ($table=null, $sortField="id", $sortAsc=true, $limit=20, $offset=0) {
        # null table
        if(is_null($table)||strlen($table)==0) return false;
        
        # If limit is not a positive integer, set it to default
        if ($limit < 0 || !is_numeric($limit)) {
            $limit = 20;
        }
        
        try { $res = $this->Database->getObjects($table, $sortField, $sortAsc, $limit, $offset); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage());
            return false;
        }
        # save
        if (sizeof($res)>0) {
            foreach ($res as $r) {
                $this->cache_write ($table, $r->id, $r);
            }
        }
        # result
        return sizeof($res)>0 ? $res : false;
    }

    /**
     * Fetches specified object specified table in database
     *
     * @access public
     * @param mixed $table
     * @param mixed $method (default: null)
     * @param mixed $value
     * @return bool|object
     */
    public function fetch_object ($table=null, $method=null, $value) {
        # null table
        if(is_null($table)||strlen($table)==0) return false;

        // checks
        if(is_null($table))		return false;
        if(strlen($table)==0)   return false;
        if(is_null($method))	return false;
        if(is_null($value))		return false;
        if($value===0)		    return false;

        # null method
        $method = is_null($method) ? "id" : $this->Database->escape($method);

        # check cache
        $cached_item = $this->cache_check($table, $value);
        if($cached_item!==false) {
            return $cached_item;
        }
        else {
            try { $res = $this->Database->getObjectQuery("SELECT * from `$table` where `$method` = ? limit 1;", array($value)); }
            catch (Exception $e) {
                $this->Result->show("danger", _("Error: ").$e->getMessage());
                return false;
            }
            # save to cache array
            if(sizeof($res)>0) {
                // set identifier
                $method = $this->cache_set_identifier ($table);
                // save
                $this->cache_write ($table, $res->{$method}, $res);
                return $res;
            }
            else {
                return false;
            }
        }
    }

    /**
     * Fetches multiple objects in specified table in database
     *
     *	doesnt cache
     *
     * @access public
     * @param mixed $table
     * @param mixed $field
     * @param mixed $value
     * @param string $sortField (default: 'id')
     * @param bool $sortAsc (default: true)
     * @param bool $like (default: false)
     * @param array|mixed $result_fields (default: *)
     * @return bool|array
     */
    public function fetch_multiple_objects ($table, $field, $value, $sortField = 'id', $sortAsc = true, $like = false, $result_fields = "*") {
        # null table
        if(is_null($table)||strlen($table)==0) return false;
        else {
            try { $res = $this->Database->findObjects($table, $field, $value, $sortField, $sortAsc, $like, false, $result_fields); }
            catch (Exception $e) {
                $this->Result->show("danger", _("Error: ").$e->getMessage());
                return false;
            }
            # save to cach
            if (sizeof($res)>0) {
                foreach ($res as $r) {
                    $this->cache_write ($table, $r->id, $r);
                }
            }
            # result
            return sizeof($res)>0 ? $res : false;
        }
    }

    /**
     * Count objects in database.
     *
     * @access public
     * @param mixed $table
     * @param mixed $field
     * @param mixed $val (default: null)
     * @param bool $like (default: false)
     * @return int
     */
    public function count_database_objects ($table, $field, $val=null, $like = false) {
        # if null
        try { $cnt = $this->Database->numObjectsFilter($table, $field, $val, $like); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage());
            return false;
        }
        return $cnt;
    }


    /**
     * Get all admins that are set to receive changelog
     *
     * @access public
     * @param bool|mixed $subnetId
     * @return bool|array
     */
    public function changelog_mail_get_recipients ($subnetId = false) {
        // fetch all users with mailNotify
        $notification_users = $this->fetch_multiple_objects ("users", "mailChangelog", "Yes", "id", true);
        // recipients array
        $recipients = array();
        // any ?
        if ($notification_users!==false) {
             // if subnetId is set check who has permissions
            if (isset($subnetId)) {
                 foreach ($notification_users as $u) {
                    // inti object
                    $Subnets = new Subnets ($this->Database);
                    //check permissions
                    $subnet_permission = $Subnets->check_permission($u, $subnetId);
                    // if 3 than add
                    if ($subnet_permission==3) {
                        $recipients[] = $u;
                    }
                }
            }
            else {
                foreach ($notification_users as $u) {
                    if($u->role=="Administrator") {
                        $recipients[] = $u;
                    }
                }
            }
            return sizeof($recipients)>0 ? $recipients : false;
        }
        else {
            return false;
        }
    }




    /**
     * fetches settings from database
     *
     * @access private
     * @return void
     */
    public function get_settings () {
        # constant defined
        if (defined('SETTINGS')) {
            if ($this->settings === null || $this->settings === false) {
                $this->settings = json_decode(SETTINGS);
            }
        }
        else {
            # cache check
            if($this->settings === null) {
                try { $settings = $this->Database->getObject("settings", 1); }
                catch (Exception $e) { $this->Result->show("danger", _("Database error: ").$e->getMessage()); }
                # save
                if ($settings!==false)	 {
                    $this->settings = $settings;
                    define(SETTINGS, json_encode($settings));
                }
            }
        }
    }

    /**
     * get_settings alias
     *
     * @access public
     * @return void
     */
    public function settings () {
        return $this->get_settings();
    }


    /**
     * Write result to cache.
     *
     * @access protected
     * @param mixed $table
     * @param mixed $id
     * @param mixed $object
     * @return void
     */
    protected function cache_write ($table, $id, $object) {
        // get method
        $identifier = $this->cache_set_identifier ($table);
        // check if cache is already set, otherwise save
        if ($this->cache_check_exceptions!==false) {
            if (!isset($this->cache[$table][$identifier][$id])) {
                $this->cache[$table][$identifier][$id] = (object) $object;
                // add ip ?
                $ip_check = $this->cache_check_add_ip($table);
                if ($ip_check!==false) {
                    $this->cache[$table][$identifier][$id]->ip = $this->transform_address ($object->{$ip_check}, "dotted");
                }
            }
        }
    }

    /**
     * Check if caching is not needed
     *
     * @access protected
     * @param mixed $table
     * @return bool
     */
    protected function cache_check_exceptions ($table) {
        // define
        $exceptions = array("deviceTypes");
        // check
        return in_array($table, $exceptions) ? true : false;
    }

    /**
     * Cehck if ip is to be added to result
     *
     * @access protected
     * @param mixed $table
     * @return bool|mixed
     */
    protected function cache_check_add_ip ($table) {
        // define
        $ip_tables = array("subnets"=>"subnet", "ipaddresses"=>"ip_addr");
        // check
        return array_key_exists ($table, $ip_tables) ? $ip_tables[$table] : false;
    }

    /**
     * Set identifier for table - exceptions.
     *
     * @access protected
     * @param mixed $table
     * @return mixed
     */
    protected function cache_set_identifier ($table) {
        // vlan and subnets have different identifiers
        if ($table=="vlans")        { return "vlanId"; }
        elseif ($table=="vrf")      { return "vrfId"; }
        else                        { return "id"; }
    }

    /**
     * Checks if object alreay exists in cache..
     *
     * @access protected
     * @param mixed $table
     * @param mixed $id
     * @return bool|array
     */
    protected function cache_check ($table, $id) {
        // get method
        $method = $this->cache_set_identifier ($table);
        // check if cache is already set, otherwise return false
        if (isset($this->cache[$table][$method][$id]))  { return (object) $this->cache[$table][$method][$id]; }
        else                                            { return false; }
    }


    /**
     * Sets debugging
     *
     * @access private
     * @return void
     */
    public function set_debugging () {
        require CONFIG;
        $this->debugging = $debugging ? true : false;
    }


    /**
     * Initializes PEAR Net IPv4 object
     *
     * @access public
     * @return void
     */
    public function initialize_pear_net_IPv4 () {
        //initialize NET object
        if(!is_object($this->Net_IPv4)) {
            //initialize object
            $this->Net_IPv4 = new Net_IPv4();
        }
    }

    /**
     * Initializes PEAR Net IPv6 object
     *
     * @access public
     * @return void
     */
    public function initialize_pear_net_IPv6 () {
        //initialize NET object
        if(!is_object($this->Net_IPv6)) {
            //initialize object
            $this->Net_IPv6 = new Net_IPv6();
        }
    }

    /**
     * Initializes PEAR Net IPv6 object
     *
     * @access public
     * @return void
     */
    public function initialize_pear_net_DNS2 () {
        //initialize NET object
        if(!is_object($this->DNS2)) {
            //initialize object
            $this->DNS2 = new Net_DNS2_Resolver();
        }
    }

    /**
     * Strip tags from array or field to protect from XSS
     *
     * @access public
     * @param array|string $input
     * @return array|string
     */
    public function strip_input_tags ($input) {
        if(is_array($input)) {
            foreach($input as $k=>$v) {
                $input[$k] = $this->strip_input_tags($v);
            }
        }
        else {
            $input = strip_tags($input);
        }
        # stripped
        return $input;
    }

    /**
     * Changes empty array fields to specified character
     *
     * @access public
     * @param array|object $fields
     * @param string $char (default: "/")
     * @return array
     */
    public function reformat_empty_array_fields ($fields, $char = "/") {
        $out = array();
        // loop
        foreach($fields as $k=>$v) {
            if(is_array($v)) {
                $out[$k] = $v;
            }
            else {
                if(is_null($v) || strlen($v)==0) {
                    $out[$k] =     $char;
                } else {
                    $out[$k] = $v;
                }
            }
        }
        # result
        return $out;
    }

    /**
     * Removes empty array fields
     *
     * @access public
     * @param array $fields
     * @return array
     */
    public function remove_empty_array_fields ($fields) {
        // init
        $out = array();
        // loop
        foreach($fields as $k=>$v) {
            if(is_null($v) || strlen($v)==0) {
            }
            else {
                $out[$k] = $v;
            }
        }
        # result
        return $out;
    }

    /**
     * Function to verify checkbox if 0 length
     *
     * @access public
     * @param mixed $field
     * @return int|mixed
     */
    public function verify_checkbox ($field) {
        return @$field==""||strlen(@$field)==0 ? 0 : $field;
    }

    /**
     * identify ip address type - ipv4 or ipv6
     *
     * @access public
     * @param mixed $address
     * @return mixed IP version
     */
    public function identify_address ($address) {
        # dotted representation
        if (strpos($address, ":"))         { return 'IPv6'; }
        elseif (strpos($address, "."))     { return 'IPv4'; }
        # decimal representation
        else  {
            # IPv4 address
            if(strlen($address) < 12)     { return 'IPv4'; }
            # IPv6 address
            else                         { return 'IPv6'; }
        }
    }

    /**
     * Alias of identify_address_format function
     *
     * @access public
     * @param mixed $address
     * @return mixed
     */
    public function get_ip_version ($address) {
        return $this->identify_address ($address);
    }

    /**
     * Transforms array to log format
     *
     * @access public
     * @param mixed $logs
     * @param bool $changelog
     * @return mixed
     */
    public function array_to_log ($logs, $changelog = false) {
        $result = "";
        # reformat
        if(is_array($logs)) {
            // changelog
            if ($changelog===true) {
                foreach($logs as $key=>$req) {
                    # ignore __ and PHPSESSID
                    if( (substr($key,0,2) == '__') || (substr($key,0,9) == 'PHPSESSID') || (substr($key,0,4) == 'pass') || $key=='plainpass' ) {}
                    else                                                                   { $result .= "[$key]: $req<br>"; }
                }

            }
            else {
                foreach($logs as $key=>$req) {
                    # ignore __ and PHPSESSID
                    if( (substr($key,0,2) == '__') || (substr($key,0,9) == 'PHPSESSID') || (substr($key,0,4) == 'pass') || $key=='plainpass' ) {}
                    else                                                                   { $result .= " ". $key . ": " . $req . "<br>"; }
                }
            }
        }
        return $result;
    }

    /**
     * Transforms seconds to hms
     *
     * @access public
     * @param mixed $sec
     * @param bool $padHours (default: false)
     * @return mixed
     */
    public function sec2hms($sec, $padHours = false) {
        // holds formatted string
        $hms = "";

        // get the number of hours
        $hours = intval(intval($sec) / 3600);

        // add to $hms, with a leading 0 if asked for
        $hms .= ($padHours)
              ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
              : $hours. ':';

        // get the seconds
        $minutes = intval(($sec / 60) % 60);

        // then add to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

        // seconds
        $seconds = intval($sec % 60);

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // return hms
        return $hms;
    }

    /**
     * Shortens text to max chars
     *
     * @access public
     * @param mixed $text
     * @param int $chars (default: 25)
     * @return mixed
     */
    public function shorten_text($text, $chars = 25) {
        //count input text size
        $startLen = strlen($text);
        //cut onwanted chars
        $text = substr($text,0,$chars);
        //count output text size
        $endLen = strlen($text);

        //append dots if it was cut
        if($endLen != $startLen) {
            $text = $text."...";
        }

        return $text;
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
     * @return mixed
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
     * Create URL for base
     *
     * @access public
     * @return mixed
     */
    public function createURL () {
        # reset url for base
        if($_SERVER['SERVER_PORT'] == "443")         { $url = "https://$_SERVER[HTTP_HOST]"; }
        // reverse proxy doing SSL offloading
        elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')  { $url = "https://$_SERVER[HTTP_X_FORWARDED_HOST]"; }
        elseif(isset($_SERVER['HTTP_X_SECURE_REQUEST'])  && $_SERVER['HTTP_X_SECURE_REQUEST'] == 'true')     { $url = "https://$_SERVER[SERVER_NAME]"; }
        // custom port
        elseif($_SERVER['SERVER_PORT']!="80")          { $url = "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]"; }
        // normal http
        else                                         { $url = "http://$_SERVER[HTTP_HOST]"; }

        //result
        return $url;
    }

    /**
     * Creates links from text fields if link is present
     *
     *    source: https://css-tricks.com/snippets/php/find-urls-in-text-make-links/
     *
     * @access public
     * @param mixed $field_type
     * @param mixed $text
     * @return mixed
     */
    public function create_links ($text, $field_type = "varchar") {
        // create links only for varchar fields
        if (strpos($field_type, "varchar")!==false) {
            // regular expression
            $reg_exUrl = "#(http|https|ftp|ftps|telnet|ssh)://\S+[^\s.,>)\];'\"!?]#";

            // Check if there is a url in the text
            if(preg_match($reg_exUrl, $text, $url)) {
               // make the urls hyper links
               $text = preg_replace($reg_exUrl, "<a href='{$url[0]}' target='_blank'>{$url[0]}</a> ", $text);
            }
        }
        // return text
        return $text;
    }

    /**
     * Sets valid actions
     *
     * @access private
     * @return string[]
     */
    private function get_valid_actions() {
        return array(
                "add"      => ["POST"],
                "all-add"  => ["POST"],
                "edit"     => ["POST"],
                "all-edit" => ["POST"],
                "delete"   => ["POST"],
                "truncate" => ["POST"],
                "split"    => ["POST"],
                "resize"   => ["POST"],
                "move"     => ["POST"],
                "remove"   => ["POST"],
                "read"     => ["POST","GET"], # For consolidating all ajax calls through ajax.php we need more actions
                "test"     => ["POST","GET"], #
                "save"     => ["POST","GET"], #
                "regen"    => ["POST","GET"], #
                "scan"     => ["POST","GET"], #
                "search"   => ["POST","GET"], #
                "export"   => ["POST","GET"], #
                "import"   => ["POST","GET"]  #
              );
    }
    
    /**
     * Validate the HTTP method
     *
     * @access public
     * @param string $method
     * @param bool $popup
     * @return bool|void
     */
    public function validate_method($method, $popup = false) {
        
        $methods = ['GET','POST','PUT','PATCH','DELETE'];
        
        if (in_array($method, $methods)) {
            return true;
        } else { 
            $this->Result->show("danger", _("Invalid HTTP method"), true, $popup);
        }
        
    }

    /**
     * Validate posted action on scripts. Actually validates 'action' + HTTP method
     *
     * @access public
     * @param mixed $action
     * @param bool $popup
     * @return mixed|bool
     */
    public function validate_action($action, $method, $popup = false) {

        $valid_actions = $this->get_valid_actions();

        if (array_key_exists($action, $valid_actions)) {
            if (in_array($method, $valid_actions[$action])) {
                return true;
            } else {
                $this->Result->show("danger", _("Invalid HTTP method"), true, $popup);
            }
        } else {
            $this->Result->show("danger", _("Invalid action!"), true, $popup);
        }
    
    }

    /**
     * Validates email address.
     *
     * @access public
     * @param mixed $email
     * @return bool
     */
    public function validate_email($email) {
        return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email) ? true : false;
    }

    /**
     * Validate hostname
     *
     * @access public
     * @param mixed $hostname
     * @param bool $permit_root_domain
     * @return bool|mixed
     */
    public function validate_hostname($hostname, $permit_root_domain=true) {
        // first validate hostname
        $valid =  (preg_match("/^([a-z_\d](-*[a-z_\d])*)(\.([a-z_\d](-*[a-z_\d])*))*$/i", $hostname)     //valid chars check
                && preg_match("/^.{1,253}$/", $hostname)                                         //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $hostname)   );                 //length of each label
        // if it fails return immediately
        if (!$valid) {
            return $valid;
        }
        // than validate root_domain if requested
        elseif ($permit_root_domain)    {
            return $valid;
        }
        else {
            if(strpos($hostname, ".")!==false)  { return $valid; }
            else                                { return false; }
        }
    }

    /**
     * Validates IP address
     *
     * @access public
     * @param mixed $ip
     * @return bool
     */
    public function validate_ip ($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP)===false) { return false; }
        else                                            { return true; }
    }

    /**
     * Validates MAC address
     *
     * @access public
     * @param mixed $mac
     * @return bool
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
     * Validates json from provided string.
     *
     * @access public
     * @param mixed $string
     * @return mixed
     */
    public function validate_json_string($string) {
        // for older php versions make sure that function "json_last_error_msg" exist and create it if not
        if (!function_exists('json_last_error_msg')) {
            function json_last_error_msg() {
                static $ERRORS = array(
                    JSON_ERROR_NONE => 'No error',
                    JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                    JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
                    JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                    JSON_ERROR_SYNTAX => 'Syntax error',
                    JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
                );

                $error = json_last_error();
                return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
            }
        }

        // try to decode
        json_decode($string);
        // check for error
        $parse_result = json_last_error_msg();
        // save possible error
        if($parse_result!=="No error") {
            $this->json_error = $parse_result;
        }
        // return true / false
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Transforms ipv6 to nt
     *
     * @access public
     * @param mixed $ipv6
     * @return mixed
     */
    public function ip2long6 ($ipv6) {
        if($ipv6 == ".255.255.255") {
            return false;
        }
        $ip_n = inet_pton($ipv6);
        $bits = 15; // 16 x 8 bit = 128bit
        $ipv6long = "";

        while ($bits >= 0)
        {
            $bin = sprintf("%08b",(ord($ip_n[$bits])));
            $ipv6long = $bin.$ipv6long;
            $bits--;
        }
        return gmp_strval(gmp_init($ipv6long,2),10);
    }

    /**
     * Transforms int to ipv6
     *
     * @access public
     * @param mixed $ipv6long
     * @return mixed
     */
    public function long2ip6($ipv6long) {
        $bin = gmp_strval(gmp_init($ipv6long,10),2);
        $ipv6 = "";

        if (strlen($bin) < 128) {
            $pad = 128 - strlen($bin);
            for ($i = 1; $i <= $pad; $i++) {
                $bin = "0".$bin;
            }
        }

        $bits = 0;
        while ($bits <= 7)
        {
            $bin_part = substr($bin,($bits*16),16);
            $ipv6 .= dechex(bindec($bin_part)).":";
            $bits++;
        }
        // compress result
        return inet_ntop(inet_pton(substr($ipv6,0,-1)));
    }

    /**
     * Identifies IP address format
     *
     *	0 = decimal
     *	1 = dotted
     *
     * @access public
     * @param mixed $address
     * @return mixed decimal or dotted
     */
    public function identify_address_format ($address) {
        return is_numeric($address) ? "decimal" : "dotted";
    }

    /**
     * Transforms IP address to required format
     *
     *	format can be decimal (1678323323) or dotted (10.10.0.0)
     *
     * @access public
     * @param mixed $address
     * @param string $format (default: "dotted")
     * @return mixed requested format
     */
    public function transform_address ($address, $format = "dotted") {
        # no change
        if($this->identify_address_format ($address) == $format)		{ return $address; }
        else {
            if($this->identify_address_format ($address) == "dotted")	{ return $this->transform_to_decimal ($address); }
            else														{ return $this->transform_to_dotted ($address); }
        }
    }

    /**
     * Transform IP address from decimal to dotted (167903488 -> 10.2.1.0)
     *
     * @access public
     * @param mixed $address
     * @return mixed dotted format
     */
    public function transform_to_dotted ($address) {
        if ($this->identify_address ($address) == "IPv4" ) 				{ return(long2ip($address)); }
        else 								 			  				{ return($this->long2ip6($address)); }
    }

    /**
     * Transform IP address from dotted to decimal (10.2.1.0 -> 167903488)
     *
     * @access public
     * @param mixed $address
     * @return int IP address
     */
    public function transform_to_decimal ($address) {
        if ($this->identify_address ($address) == "IPv4" ) 				{ return( sprintf("%u", ip2long($address)) ); }
        else 								 							{ return($this->ip2long6($address)); }
    }

    /**
     * Returns text representation of json errors
     *
     * @access public
     * @param mixed $error_int
     * @return mixed
     */
    public function json_error_decode ($error_int) {
        // init
        $error = array();
        // error definitions
        $error[0] = "JSON_ERROR_NONE";
        $error[1] = "JSON_ERROR_DEPTH";
        $error[2] = "JSON_ERROR_STATE_MISMATCH";
        $error[3] = "JSON_ERROR_CTRL_CHAR";
        $error[4] = "JSON_ERROR_SYNTAX";
        $error[5] = "JSON_ERROR_UTF8";
        // return def
        if (isset($error[$error_int]))	{ return $error[$error_int]; }
        else							{ return "JSON_ERROR_UNKNOWN"; }
    }

    /**
     * Fetches latlng from googlemaps by provided address
     *
     * @access public
     * @param mixed $address
     * @return array
     */
    public function get_latlng_from_address ($address) {
        // replace spaces
        $address = str_replace(' ','+',$address);
        // get grocode
        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
        $output= json_decode($geocode);
        // return result
        return array("lat"=>$output->results[0]->geometry->location->lat, "lng"=>$output->results[0]->geometry->location->lng);
    }

    /**
     * Updates location to latlng from address
     *
     * @access public
     * @param mixed $id
     * @param mixed $lat
     * @param mixed $lng
     * @return bool
     */
    public function update_latlng ($id, $lat, $lng) {
        # execute
        try { $this->Database->updateObject("locations", array("id"=>$id, "lat"=>$lat, "long"=>$lng), "id"); }
        catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Creates image link to rack.
     *
     * @access public
     * @param bool $rackId (default: false)
     * @param bool $deviceId (default: false)
     * @return mixed|bool
     */
    public function create_rack_link ($rackId = false, $deviceId = false) {
        if(!$rackId) {
            return false;
        }
        else {
            //device ?
            if ($deviceId) {
                return $this->createURL ().BASE."ajx/tools/racks/draw_rack/?action=read&rackId=$rackId&deviceId=$deviceId";
            }
            else {
                return $this->createURL ().BASE."ajx/tools/racks/draw_rack/?action=read&rackId=$rackId";
            }
        }
    }







    /**
     *	@breadcrumbs functions
     * ------------------------
     */

    /**
     * print_breadcrumbs function.
     *
     * @access public
     * @param mixed $Section
     * @param mixed $Subnet
     * @param mixed $req
     * @param mixed $Address (default: null)
     * @return void
     */
    public function print_breadcrumbs ($Section, $Subnet, $req, $Address=null) {
        # subnets
        if($req['page'] == "subnets")		{ $this->print_subnet_breadcrumbs ($Subnet, $req, $Address); }
        # folders
        elseif($req['page'] == "folder")	{ $this->print_folder_breadcrumbs ($Section, $Subnet, $req); }
        # tools
        elseif ($req['page'] == "tools") 	{ $this->print_tools_breadcrumbs ($req); }
    }

    /**
     * Print address breadcrumbs
     *
     * @access private
     * @param mixed $Subnet
     * @param mixed $req
     * @param mixed $Address
     * @return void
     */
    private function print_subnet_breadcrumbs ($Subnet, $req, $Address) {
        if(isset($req['subnetId'])) {
            # get all parents
            $parents = $Subnet->fetch_parents_recursive ($req['subnetId']);

            print "<ul class='breadcrumb'>";
            # remove root - 0
            //array_shift($parents);

            # section details
            $section = (array) $this->fetch_object ("sections", "id", $req['section']);

            # section name
            print "	<li><a href='".create_link("subnets",$section['id'])."'>$section[name]</a> <span class='divider'></span></li>";

            # all parents
            foreach($parents as $parent) {
                $subnet = (array) $Subnet->fetch_subnet("id",$parent);
                if($subnet['isFolder']==1) {
                    print "	<li><a href='".create_link("folder",$section['id'],$parent)."'><i class='icon-folder-open icon-gray'></i> $subnet[description]</a> <span class='divider'></span></li>";
                } else {
                    print "	<li><a href='".create_link("subnets",$section['id'],$parent)."'>$subnet[description] ($subnet[ip]/$subnet[mask])</a> <span class='divider'></span></li>";
                }
            }
            # parent subnet
            $subnet = (array) $Subnet->fetch_subnet("id",$req['subnetId']);
            # ip set
            if(isset($req['ipaddrid'])) {
                $ip = (array) $Address->fetch_address ("id", $req['ipaddrid']);
                print "	<li><a href='".create_link("subnets",$section['id'],$subnet['id'])."'>$subnet[description] ($subnet[ip]/$subnet[mask])</a> <span class='divider'></span></li>";
                print "	<li class='active'>$ip[ip]</li>";			//IP address
            }
            else {
                print "	<li class='active'>$subnet[description] ($subnet[ip]/$subnet[mask])</li>";		//active subnet

            }
            print "</ul>";
        }
    }

    /**
     * Print folder breadcrumbs
     *
     * @access private
     * @param obj $Section
     * @param obj $Subnet
     * @param mixed $req
     * @return void
     */
    private function print_folder_breadcrumbs ($Section, $Subnet, $req) {
        if(isset($req['subnetId'])) {
            # get all parents
            $parents = $Subnet->fetch_parents_recursive ($req['subnetId']);
            print "<ul class='breadcrumb'>";
            # remove root - 0
            array_shift($parents);

            # section details
            $section = (array) $Section->fetch_section(null, $req['section']);

            # section name
            print "	<li><a href='".create_link("subnets",$section['id'])."'>$section[name]</a> <span class='divider'></span></li>";

            # all parents
            foreach($parents as $parent) {
                $parent = (array) $parent;
                $subnet = (array) $Subnet->fetch_subnet(null,$parent[0]);
                if ($subnet['isFolder']=="1")
                print "	<li><a href='".create_link("folder",$section['id'],$parent[0])."'><i class='icon-folder-open icon-gray'></i> $subnet[description]</a> <span class='divider'></span></li>";
                else
                print "	<li><a href='".create_link("subnets",$section['id'],$parent[0])."'><i class='icon-folder-open icon-gray'></i> $subnet[description]</a> <span class='divider'></span></li>";
            }
            # parent subnet
            $subnet = (array) $Subnet->fetch_subnet(null,$req['subnetId']);
            print "	<li>$subnet[description]</li>";																		# active subnet
            print "</ul>";
        }
    }

    /**
     * Prints tools breadcrumbs
     *
     * @access public
     * @param mixed $req
     * @return void
     */
    private function print_tools_breadcrumbs ($req) {
        print "<ul class='breadcrumb'>";
        print "	<li><a href='".create_link("tools")."'>"._('Tools')."</a> <span class='divider'></span></li>";
        if(!isset($req['subnetId'])) {
            print "	<li class='active'>$req[section]</li>";
        }
        else {
            print "	<li class='active'><a href='".create_link("tools", $req['section'])."'>$req[section]</a> <span class='divider'></span></li>";

            # pstn
            if ($_GET['section']=="pstn-prefixes") {
                # get all parents
                $Tools = new Tools ($this->Database);
                $parents = $Tools->fetch_prefix_parents_recursive ($req['subnetId']);
                # all parents
                foreach($parents as $parent) {
                    $prefix = $this->fetch_object("pstnPrefixes", "id", $parent[0]);
                    print "	<li><a href='".create_link("tools",$req['section'],$parent[0])."'><i class='icon-folder-open icon-gray'></i> $prefix->name</a> <span class='divider'></span></li>";
                }

            }
            $prefix = $this->fetch_object("pstnPrefixes", "id", $req['subnetId']);
            print "	<li class='active'>$prefix->name</li>";
        }
        print "</ul>";
    }

    /**
     * Prints site title
     *
     * @access public
     * @param mixed $get
     * @return void
     */
    public function get_site_title ($get) {
        // remove html tags
        $get = $this->strip_input_tags ($get);
        // init
        $title[] = $this->settings->siteTitle;

        // page
        if (isset($get['page'])) {
            // dashboard
            if ($get['page']=="dashboard") {
                return $this->settings->siteTitle." Dashboard";
            }
            // install, upgrade
            elseif ($get['page']=="temp_share" || $get['page']=="request_ip" || $get['page']=="opensearch") {
                $title[] = ucwords($get['page']);
            }
            // sections, subnets
            elseif ($get['page']=="subnets" || $get['page']=="folder") {
                // subnets
                $title[] = "Subnets";

                // section
                if (isset($get['section'])) {
                     $se = $this->fetch_object ("sections", "id", $get['section']);
                    if($se!==false) {
                        $title[] = $se->name;
                    }
                }
                // subnet
                if (isset($get['subnetId'])) {
                     $sn = $this->fetch_object ("subnets", "id", $get['subnetId']);
                    if($sn!==false) {
                        if($sn->isFolder) {
                            $title[] = $sn->description;
                        }
                        else {
                            $sn->description = strlen($sn->description)>0 ? " (".$sn->description.")" : "";
                            $title[] = $this->transform_address($sn->subnet, "dotted")."/".$sn->mask.$sn->description;
                        }
                    }
                }
                // ip address
                if (isset($get['ipaddrid'])) {
                    $ip = $this->fetch_object ("ipaddresses", "id", $get['ipaddrid']);
                    if($ip!==false) {
                        $title[] = $this->transform_address($ip->ip_addr, "dotted");
                    }
                }
            }
            // tools, admin
            elseif ($get['page']=="tools" || $get['page']=="administration") {
                $title[] = ucwords($get['page']);
                // subpage
                if (isset($get['section'])) {
                    $title[] = ucwords($get['section']);
                }
                if (isset($get['subnetId'])) {
                    // vland domain
                    if($get['section']=="vlan") {
                         $se = $this->fetch_object ("vlanDomains", "id", $get['subnetId']);
                        if($se!==false) {
                            $title[] = $se->name." domain";
                        }
                    }
                    else {
                        $title[] = ucwords($get['subnetId']);
                    }
                }
            }
            else {
                $title[] = ucwords($get['page']);
            }
        }
        // return title
        return implode(" / ", $title);
    }

}
?>
