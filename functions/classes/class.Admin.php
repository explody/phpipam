<?php

/**
 *	phpIPAM Admin class
 */

class Admin extends Common_functions {


	/**
	 * (array of objects) to store users, user id is array index
	 *
	 * @var mixed
	 * @access public
	 */
	public $users;

	/**
	 * (array of objects) to store groups, group id is array index
	 *
	 * @var mixed
	 * @access public
	 */
	public $groups;

	/**
	 * id of last edited/added table
	 *
	 * (default value: null)
	 *
	 * @var mixed
	 * @access public
	 */
	public $lastId = null;

	/**
	 * flag is user is admin
	 *
	 * (default value: false)
	 *
	 * @var bool
	 * @access private
	 */
	private $isadmin = false;

	/**
	 * if admin user is required to connect. Can be overridden
	 *
	 * (default value: true)
	 *
	 * @var bool
	 * @access private
	 */
	private $admin_required = true;

	/**
	 * Result
	 *
	 * @var mixed
	 * @access public
	 */
	public $Result;

	/**
	 * User
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $User;

	/**
	 * Database
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Database;

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
	 * Log
	 *
	 * @var mixed
	 * @access public
	 */
	public $Log;




	/**
	 * __construct function.
	 *
	 * @access public
	 * @param Database_PDO $database
	 * @param bool $admin_required (default: true)
	 */
	public function __construct (Database_PDO $database, $admin_required = true) {
		# initialize database object
		$this->Database = $database;
		# initialize Result
		$this->Result = new Result ();
		# set debugging
		$this->set_debugging ();
		# set admin flag
		$this->set_admin_required ($admin_required);
		# verify that user is admin
		$this->is_admin ();
		# Log object
		$this->Log = new Logging ($this->Database);
	}

	/**
	 * Saves last insert ID on object modification.
	 *
	 * @access public
	 * @return void
	 */
	public function save_last_insert_id () {
		$this->lastId = $this->Database->lastInsertId();
	}

	/**
	 * Sets admin required flag if needed
	 *
	 * @access public
	 * @param boolean $bool
	 * @return void
	 */
	public function set_admin_required ($bool) {
		$this->admin_required = is_bool($bool) ? $bool : true;
	}

	/**
	 * Checks if current user is admin
	 *
	 * @access public
	 * @return void
	 */
	public function is_admin () {
		// user not required for cli
		if (php_sapi_name()!="cli") {
			# initialize user class
			$this->User = new User ($this->Database);
    		# save settings
    		$this->settings = $this->User->settings;
    		# if required die !
    		if($this->User->is_admin(false)!==true && $this->admin_required==true) {
    			// popup ?
    			if(@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") 	{ $this->Result->show("danger", _("Administrative privileges required"),true, true); }
    			else 														{ $this->Result->show("danger", _("Administrative privileges required"),true); }
    		}
		}
	}













	/**
	 *	@general update methods
	 *	--------------------------------
	 */

	/**
	 * Modify database object
	 *
	 * @param string $table
	 * @param string $action
	 * @param string $id
	 * @param mixed $values
	 * @return void
	 */
	public function object_modify ($table, $action=null, $field="id", $values) {

		# execute based on action
		if($action=="add")					{ return $this->object_add ($table, $values); }
		elseif($action=="edit")				{ return $this->object_edit ($table, $field, $values); }
		elseif($action=="edit-multiple")	{ return $this->object_edit_multiple ($table, $field, $values); }		//$field = array of ids
		elseif($action=="delete")			{ return $this->object_delete ($table, $field, $values[$field]); }
		else								{ return $this->Result->show("danger", _("Invalid action"), true); }
	}

	/**
	 * Create new database object
	 *
	 *		$values are all values that should be passed to create object
	 *
	 * @access private
	 * @param mixed $table
	 * @param mixed $values
	 * @return boolean
	 */
	private function object_add ($table, $values) {
		# null empty values
		$values = $this->reformat_empty_array_fields ($values, null);

		# execute
		try { $this->Database->insertObject($table, $values); }
		catch (Exception $e) {
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			$this->Log->write( "$table object creation", "Failed to create new $table database object<hr>".$e->getMessage()."<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 2);
			return false;
		}
		# save ID
		$this->save_last_insert_id ();
		# ok
		$this->Log->write( "$table object creation", "New $table database object created<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 0);
		return true;
	}

	/**
	 * Edit object in table by specified object id
	 *
	 *		$values are all values that should be passed to edit object,
	 *		id will be used to match field to update.
	 *
	 * @access private
	 * @param mixed $table			//name of table to update
	 * @param array $values			//update variables
	 * @return boolean
	 */
	private function object_edit ($table, $key="id", $values) {
		# null empty values
		$values = $this->reformat_empty_array_fields ($values, null);

		# execute
		try { $this->Database->updateObject($table, $values, $key); }
		catch (Exception $e) {
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			$this->Log->write( "$table object $values[$key] edit", "Failed to edit object $key=$values[$key] in $table<hr>".$e->getMessage()."<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 2);
			return false;
		}
		# save ID
		$this->save_last_insert_id ();
		# ok
		$this->Log->write( "$table object $values[$key] edit", "Object $key=$values[$key] in $table edited<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 0);
		return true;
	}

	/**
	 * Edit multiple objects in table by specified object id
	 *
	 *		$values are all values that should be passed to edit object,
	 *		ids will be used to match fields to update.
	 *
	 * @access private
	 * @param mixed $table			//name of table to update
	 * @param array $values			//update variables
	 * @param string $ids
	 * @return boolean
	 */
	private function object_edit_multiple ($table, $ids, $values) {
		# null empty values
		$values = $this->reformat_empty_array_fields ($values, null);

		# execute
		try { $this->Database->updateMultipleObjects($table, $ids, $values); }
		catch (Exception $e) {
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			$this->Log->write( "$table multiple objects edit", "Failed to edit multiple objects in $table<hr>".$e->getMessage()."<hr>".$this->array_to_log($ids)."<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 2);
			return false;
		}
		# save ID
		$this->save_last_insert_id ();
		# ok
		$this->Log->write( "$table multiple objects edit", "Multiple objects in $table edited<hr>".$this->array_to_log($ids)."<hr>".$this->array_to_log($this->reformat_empty_array_fields ($values, "NULL")), 0);
		return true;
	}

	/**
	 * Delete object in table by specified object id
	 *
	 * @access private
	 * @param mixed $table		//table to update
	 * @param string $field		//field selection (where $field = $id)
	 * @param mixed $id			//field identifier
	 * @return boolean
	 */
	private function object_delete ($table, $field="id", $id) {
		# execute
		try { $this->Database->deleteRow($table, $field, $id); }
		catch (Exception $e) {
			$this->Log->write( "$table object $values[$id] delete", "Failed to delete object $field=$id in $table<hr>".$e->getMessage(), 2);
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			return false;
		}
		# save ID
		$this->save_last_insert_id ();
		# ok
		$this->Log->write( "$table object $id edit", "Object $field=$id in $table deleted.", 0);
		return true;
	}

	/**
	 * Removes or replaces all old object references
	 *
	 * @access public
	 * @param mixed $table
	 * @param mixed $field
	 * @param mixed $old_value
	 * @param mixed $new_value (Default: NULL)
	 * @return null|false
	 */
	public function remove_object_references ($table, $field, $old_value, $new_value = NULL) {
		try { $this->Database->runQuery("update `$table` set `$field` = ? where `$field` = ?;", array($new_value, $old_value)); }
		catch (Exception $e) {
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			return false;
		}
	}

	/**
	 * Resets or replaces all old object references
	 *
	 * @access public
	 * @param mixed $table
	 * @param mixed $field
	 * @param mixed $old_value
	 * @param mixed $new_value
	 * @return null|false
	 */
	public function update_object_references ($table, $field, $old_value, $new_value) {
		try { $this->Database->runQuery("update `$table` set `$field` = ? where `$field` = ?;", array($new_value, $old_value)); }
		catch (Exception $e) {
			$this->Result->show("danger", _("Error: ").$e->getMessage(), false);
			return false;
		}
	}

	/**
	 * Empties table
	 *
	 * @access public
	 * @param mixed $table (default: null)
	 * @return boolean
	 */
	public function truncate_table ($table = null) {
		# null table
		if(is_null($table)||strlen($table)==0) return false;
		else {
			try { $res = $this->Database->emptyTable($table); }
			catch (Exception $e) {
				$this->Result->show("danger", _("Error: ").$e->getMessage());
				return false;
			}
			# result
			return true;
		}
	}






	/**
	 *	@group methods
	 *	--------------------------------
	 */

	/**
	 * Parse user groups
	 *
	 *	input:  array of group ids
	 *	output: array of groups ( "id"=>array($group) )
	 *
	 * @access public
	 * @param mixed $group_ids
	 * @return void
	 */
	public function groups_parse ($group_ids) {
		if(sizeof($group_ids)>0) {
	    	foreach($group_ids as $g_id) {
	    		$group = $this->fetch_object ("userGroups", "g_id", $g_id);
	    		$out[$group->g_id] = (array) $group;
	    	}
	    }
	    # return array of groups
	    return isset($out) ? $out : array();
	}


	/**
	 * Parse user groups
	 *
	 *	input:  array of group ids
	 *	output: array of ids (  "id"=>id )
	 *
	 * @access public
	 * @param mixed $group_ids
	 * @return void
	 */
	public function groups_parse_ids ($group_ids) {
		if(sizeof($group_ids) >0) {
		    foreach($group_ids as $g_id) {
	    		$group = $this->fetch_object ("userGroups", "g_id", $g_id);
	    		$out[$group->g_id] = $group->g_id;
	    	}
	    }
	    # return array of group ids
	    return isset($out) ? $out : array();
	}

	/**
	 * Fetches all users that are in group
	 *
	 * @access public
	 * @return array of user ids
	 */
	public function group_fetch_users ($group_id) {
		# get all users
		$users = $this->fetch_all_objects("users");
		# check if $gid in array
		foreach($users as $u) {
			$group_array = json_decode($u->groups, true);
			$group_array = $this->groups_parse($group_array);

			if(sizeof($group_array)>0) {
				foreach($group_array as $group) {
					if(in_array($group_id, $group)) {
						$out[] = $u->id;
					}
				}
			}
		}
		# return
		return isset($out) ? $out : array();
	}

	/**
	 * Fetches all users that are not admins and are not in group
	 *
	 * @access public
	 * @param mixed $group_id
	 * @return void
	 */
	public function group_fetch_missing_users ($group_id) {
		# get all users
		$users = $this->fetch_all_objects("users");

		# check if $gid in array
		foreach($users as $u) {
			if($u->role != "Administrator") {
				$g = json_decode($u->groups, true);
				if(!@in_array($group_id, $g)) { $out[] = $u->id; }
			}
		}
		# return
		return $out;
	}

	/**
	 * This function adds new group access to user account
	 *
	 * @access private
	 * @param mixed $gid
	 * @param mixed $uid
	 * @return boolean
	 */
	public function add_group_to_user ($gid, $uid) {
		# get old groups
		$user = $this->fetch_object ("users", "id", $uid);

		# append new group
		$g = json_decode($user->groups, true);
		$g[$gid] = $gid;
		$g = json_encode($g);

		# update
		if(!$this->update_user_groups($uid, $g)) { return false; }
		else									 { return true; }
	}

	/**
	 * This function removes group from users account.
	 *
	 * @access public
	 * @param mixed $gid
	 * @param mixed $uid
	 * @return boolean
	 */
	public function remove_group_from_user($gid, $uid) {
		# get old groups
		$user = $this->fetch_object ("users", "id", $uid);

		# remove group
		$g = json_decode($user->groups, true);
		unset($g[$gid]);
		$g = json_encode($g);

		# update
		if(!$this->update_user_groups($uid, $g)) 	{ return false; }
		else										{ return true; }
	}


	/**
	 * Update groups for specified user
	 *
	 * @access public
	 * @param mixed $uid
	 * @param string $groups
	 * @return void
	 */
	public function update_user_groups ($uid, $groups) {
	    return $this->object_modify ("users", "edit", "id", array("id"=>$uid, "groups"=>$groups));
	}

	/**
	 * Update group permissions for section
	 *
	 * @access public
	 * @param mixed $sid
	 * @param string $groups
	 * @return void
	 */
	public function update_section_groups($sid, $groups) {
	    return $this->object_modify ("sections", "edit", "id", array("id"=>$sid, "permissions"=>$groups));
	}

	/**
	 * Removes all users from specified group on group delete
	 *
	 * @access public
	 * @param int $gid	//group id
	 * @return boolean
	 */
	public function remove_group_from_users($gid) {
		# get all users
		$users = $this->fetch_all_objects("users");
		# check if $gid in array
		foreach($users as $u) {
			$g  = json_decode($u->groups, true);
			$go = $g;
			$g  = $this->groups_parse($g);
			# check
			if(sizeof($g)>0) {
				foreach($g as $gr) {
					if(in_array($gid, $gr)) {
						unset($go[$gid]);
						$ng = json_encode($go);
						$this->update_user_groups($u->id,$ng);
					}
				}
			}
		}
		return true;
	}

	/**
	 * Removes group ID from all section permissions
	 *
	 * @access public
	 * @param mixed $gid
	 * @return boolean
	 */
	public function remove_group_from_sections ($gid) {
		# get all sections
		$sections = $this->fetch_all_objects ("sections", "id");
		# check if $gid in array
		foreach($sections as $s) {
			$g = json_decode($s->permissions, true);

			if(sizeof($g)>0) {
				if(array_key_exists($gid, $g)) {
					unset($g[$gid]);
					$ng = json_encode($g);
					$this->update_section_groups($s->id,$ng);
				}
			}
		}
		return true;
	}












	/**
	 *	@search/replace fields
	 *	--------------------------------
	 */

	/**
	 * Replace fields
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $search
	 * @param mixed $replace
	 * @return void
	 */
	public function replace_fields ($field, $search, $replace) {
		# check number of items
		$count = $this->count_database_objects ("ipaddresses", $field, "%$search%", true);
		# if some exist update
		if($count>0) {
			# update
		    try { $cnt = $this->Database->runQuery("update `ipaddresses` set `$field` = replace(`$field`, ?, ?);", array($search, $replace)); }
		    catch (Exception $e) {
			    $this->Result->show("danger alert-absolute", _("Error: ").$e->getMessage(), true);
		    }
		    # ok, print count
		    $this->Result->show("success alert-absolute", _('Replaced').' '. $count .' '._('items successfully').'!', false);
		}
		else {
			$this->Result->show("info alert-absolute", _("No records found to replace"), false);
		}
	}


}
