<?php

class Legacy extends Common_functions {

	/**
	 * settings
	 *
	 * (default value: null)
	 *
	 * @var object
	 * @access public
	 */
	public $settings = null;

	/**
	 * (array) IP address types from Addresses object
	 *
	 * (default value: null)
	 *
	 * @var mixed
	 * @access public
	 */
	public $address_types = null;

	/**
	 * PEAR NET IPv4 object
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Net_IPv4;

	/**
	 * PEAR NET IPv6 object
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Net_IPv6;

	/**
	 * Addresses object
	 *
	 * (default value: false)
	 *
	 * @var bool|object
	 * @access protected
	 */
	protected $Addresses = false;

	/**
	 * for Result printing
	 *
	 * @var object
	 * @access public
	 */
	public $Result;

	/**
	 * debugging flag
	 *
	 * (default value: false)
	 *
	 * @var bool
	 * @access protected
	 */
	protected $debugging = false;

	/**
	 * Database connection
	 *
	 * @var object
	 * @access protected
	 */
	protected $Database;





	/**
	 * __construct method
	 *
	 * @access public
	 */
	public function __construct (Database_PDO $database) {
		# set database object
		$this->Database = $database;
		# initialize Result
		$this->Result = new Result ();
		# set debugging
		$this->set_debugging ();
	}
    
    /**
     *	@custom field methods
     *	--------------------------------
     */

    /**
     * Updates custom field definition
     *
     * @access public
     * @param array $field
     * @return bool
     */
    public function update_custom_field_definition ($field) {

        # set type definition and size of needed
        if($field['fieldType']=="bool" || $field['fieldType']=="text" || $field['fieldType']=="date" || $field['fieldType']=="datetime")	{ $field['ftype'] = $field['fieldType']; }
        else																																{ $field['ftype'] = $field['fieldType']."(".$field['fieldSize'].")"; }

        # default value null
        $field['fieldDefault'] = strlen($field['fieldDefault'])==0 ? NULL : $field['fieldDefault'];

        # character set if needed
        if($field['fieldType']=="varchar" || $field['fieldType']=="text" || $field['fieldType']=="set" || $field['fieldType']=="enum")	{ $charset = "CHARACTER SET utf8"; }
        else																															{ $charset = ""; }

        # escape fields
        $field['table'] 		= $this->Database->escape($field['table']);
        $field['name'] 			= $this->Database->escape($field['name']);
        $field['oldname'] 		= $this->Database->escape($field['oldname']);
        # strip values
        $field['action'] 		= $this->strip_input_tags($field['action']);
        $field['Comment'] 		= $this->strip_input_tags($field['Comment']);

        # set update query
        if($field['action']=="delete") 								{ $query  = "ALTER TABLE `$field[table]` DROP `$field[name]`;"; }
        else if ($field['action']=="edit"&&@$field['NULL']=="NO") 	{ $query  = "ALTER IGNORE TABLE `$field[table]` CHANGE COLUMN `$field[oldname]` `$field[name]` $field[ftype] $charset DEFAULT $field[fieldDefault] NOT NULL COMMENT '$field[Comment]';"; }
        else if ($field['action']=="edit") 							{ $query  = "ALTER TABLE `$field[table]` CHANGE COLUMN `$field[oldname]` `$field[name]` $field[ftype] $charset DEFAULT $field[fieldDefault] COMMENT '$field[Comment]';"; }
        else if ($field['action']=="add"&&@$field['NULL']=="NO") 	{ $query  = "ALTER TABLE `$field[table]` ADD COLUMN 	`$field[name]` 					$field[ftype] $charset DEFAULT $field[fieldDefault] NOT NULL COMMENT '$field[Comment]';"; }
        else if ($field['action']=="add")							{ $query  = "ALTER TABLE `$field[table]` ADD COLUMN 	`$field[name]` 					$field[ftype] $charset DEFAULT $field[fieldDefault] NULL COMMENT '$field[Comment]';"; }
        else {
            return false;
        }


        # set update query
        if($field['action']=="delete") 								{ $query  = "ALTER TABLE `$field[table]` DROP `$field[name]`;"; }
        else if ($field['action']=="edit"&&@$field['NULL']=="NO") 	{ $query  = "ALTER IGNORE TABLE `$field[table]` CHANGE COLUMN `$field[oldname]` `$field[name]` $field[ftype] $charset DEFAULT :default NOT NULL COMMENT :comment;"; }
        else if ($field['action']=="edit") 							{ $query  = "ALTER TABLE `$field[table]` CHANGE COLUMN `$field[oldname]` `$field[name]` $field[ftype] $charset DEFAULT :default COMMENT :comment;"; }
        else if ($field['action']=="add"&&@$field['NULL']=="NO") 	{ $query  = "ALTER TABLE `$field[table]` ADD COLUMN 	`$field[name]` 					$field[ftype] $charset DEFAULT :default NOT NULL COMMENT :comment;"; }
        else if ($field['action']=="add")							{ $query  = "ALTER TABLE `$field[table]` ADD COLUMN 	`$field[name]` 					$field[ftype] $charset DEFAULT :default NULL COMMENT :comment;"; }
        else {
            return false;
        }

        # set parametized values
        $params = array();
        if (strpos($query, ":default")>0)	$params['default'] = $field['fieldDefault'];
        if (strpos($query, ":comment")>0)	$params['comment'] = $field['Comment'];

        # execute
        try { $res = $this->Database->runQuery($query, $params); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage(), false);
            $this->Log->write( "Custom field $field[action]", "Custom Field $field[action] failed ($field[name])<hr>".$this->array_to_log($field), 2);
            return false;
        }
        # field updated
        $this->Log->write( "Custom field $field[action]", "Custom Field $field[action] success ($field[name])<hr>".$this->array_to_log($field), 0);
        return true;
    }

    /**
     * Save custom fields that should be hidden from normal display
     *
     * @access public
     * @param mixed $table				//name of custom fields table
     * @param mixed $filtered_fields	//array of field to hide for this table
     * @return boolean
     */
    public function save_custom_fields_filter ($table, $filtered_fields) {
        # old custom fields, save them to array
        $hidden_array = strlen($this->settings->hiddenCustomFields)>0 ? json_decode($this->settings->hiddenCustomFields, true) : array();

        # set new array for table
        if(is_null($filtered_fields))	{ unset($hidden_array[$table]); }
        else							{ $hidden_array[$table]=$filtered_fields; }

        # encode to json
        $hidden_json = json_encode($hidden_array);

        # update database
        try { $this->object_edit ("settings", $key="id", array("id"=>1,"hiddenCustomFields"=>$hidden_json)); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage(), true);
        }
        # ok
        return true;
    }

    /**
     * Reorders custom fields
     *
     * @access public
     * @param mixed $table
     * @param mixed $next
     * @param mixed $current
     * @return boolean
     */
    public function reorder_custom_fields ($table, $next, $current) {
        # get current field details
        $Tools = new Tools ($this->Database);
        $old = (array) $Tools->fetch_full_field_definition ($table, $current);

        # set update request
        if($old['Null']=="NO")	{ $query  = 'ALTER TABLE `'.$table.'` MODIFY COLUMN `'. $current .'` '.$old['Type'].' NOT NULL COMMENT "'.$old['Comment'].'" AFTER `'. $next .'`;'; }
        else					{ $query  = 'ALTER TABLE `'.$table.'` MODIFY COLUMN `'. $current .'` '.$old['Type'].' DEFAULT NULL COMMENT "'.$old['Comment'].'" AFTER `'. $next .'`;'; }

        # execute
        try { $res = $this->Database->runQuery($query); }
        catch (Exception $e) {
            $this->Result->show("danger", _("Error: ").$e->getMessage(), false);
            return false;
        }
        # ok
        return true;
    }
    
    /**
	 * Fetches standard tables from SCHEMA.sql file
	 *
	 * @access private
	 * @return array
	 */
	public function fetch_standard_tables () {
		# get SCHEMA.SQL file
		$schema = fopen(dirname(__FILE__) . "/../../db/SCHEMA.sql", "r");
		$schema = fread($schema, 100000);

		# get definitions to array, explode with CREATE TABLE `
		$creates = explode("CREATE TABLE `", $schema);
		# fill tables array
		$tables = array();
		foreach($creates as $k=>$c) {
			if($k>0)	{ $tables[] = strstr($c, "`", true); }	//we exclude first !
		}

		# return array of tables
		return $tables;
	}
    
    /**
     * Fetches standard database fields from SCHEMA.sql file
     *
     * @access public
     * @param mixed $table
     * @return array
     */
    public function fetch_standard_fields ($table) {
        # get SCHEMA.SQL file
        $schema = fopen(dirname(__FILE__) . "/../../db/SCHEMA.sql", "r");
        $schema = fread($schema, 100000);
        $schema = str_replace("\r\n", "\n", $schema);

        # get definition
        $definition = strstr($schema, "CREATE TABLE `$table` (");
        $definition = trim(strstr($definition, ";" . "\n", true));

        # get each line to array
        $definition = explode("\n", $definition);

        # go through,if it begins with ` use it !
        $out = array();
        foreach($definition as $d) {
            $d = trim($d);
            if(strpos(trim($d), "`")==0) {
                $d = strstr(trim($d, "`"), "`", true);
                $out[] = substr($d, strpos($d, "`"));
            }
        }
        # return array of fields
        return is_array($out) ? array_filter($out) : array();
    }
    
    // Kept for migration from old custom fields to new-style
    public function fetch_custom_fields ($table) {
            # fetch columns
           $fields = $this->fetch_columns ($table);

           $res = array();

           # save Field values only
           foreach($fields as $field) {
               # cast
               $field = (array) $field;

               $res[$field['Field']]['name']    = $field['Field'];
               $res[$field['Field']]['type']    = $field['Type'];
               $res[$field['Field']]['Comment'] = $field['Comment'];
               $res[$field['Field']]['Null']    = $field['Null'];
               $res[$field['Field']]['Default'] = $field['Default'];
           }

           # fetch standard fields
           $standard = $this->fetch_standard_fields ($table);

           # remove them
           foreach($standard as $st) {
               unset($res[$st]);
           }
           # return array
           return sizeof($res)==0 ? array() : $res;
    }
    
    /**
     * Fetch all fields configured in table - standard + custom
     *
     * @access private
     * @param mixed $table
     * @return array
     */
    private function fetch_columns ($table) {
        # escape method/table
        $table = $this->Database->escape($table);
        # fetch columns
        $query    = "show full columns from `$table`;";
        # fetch
        try { $fields = $this->Database->getObjectsQuery($query); }
        catch (Exception $e) { $this->Result->show("danger", $e->getMessage(), false);	return false; }

        return (array) $fields;
    }
    
    // Kept for migration from old custom fields to new-style
    public function fetch_custom_fields_numeric ($table) {
            # fetch all custom fields
            $custom_fields = $this->fetch_custom_fields ($table);
            # make numberic array
            if(sizeof($custom_fields>0)) {
                foreach($custom_fields as $f) {
                        $out[] = $f;
                }
                # result
                return isset($out) ? $out : array();
            }
            else {
                return array();
            }
    }
    
    /**
	 *	@database verification methods
	 *	------------------------------
	 */

	/**
	 * Checks if all database fields are installed ok
	 *
	 * @access public
	 * @return array
	 */
	public function verify_database () {

		# required tables from SCHEMA.sql
		$tables = $this->fetch_standard_tables();

		# fetch required fields
		foreach($tables as $t) {
			$fields[$t] = $this->fetch_standard_fields ($t);
		}

		/**
		 * check that each database exist - if it does check also fields
		 *		2 errors -> $tableError, $fieldError[table] = field
		 ****************************************************************/
		foreach($tables as $table) {

			//check if table exists
			if(!$this->table_exists($table)) {
				$error['tableError'][] = $table;
			}
			//check for each field
			else {
				foreach($fields[$table] as $field) {
					//if it doesnt exist store error
					if(!$this->field_exists($table, $field)) {
						$error['fieldError'][$table] = $field;
					}
				}
			}
		}

		# return array
		if(isset($error)) {
			return $error;
		} else 	{
			# update check field
			$this->update_db_verify_field ();
			# return empty array
			return array();
		}
	}
    
}