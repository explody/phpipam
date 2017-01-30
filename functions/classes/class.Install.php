<?php

use Phinx\Wrapper\TextWrapper;

/**
 *	phpIPAM Install class
 */

class Install extends Common_functions {


	/**
	 * to store DB exceptions
	 *
	 * @var mixed
	 * @access public
	 */
	public $exception;

	/**
	 * Database parameters
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $db;

	/**
	 * debugging flag
	 *
	 * (default value: false)
	 *
	 * @var bool
	 * @access public
	 */
	public $debugging = false;

	/**
	 * Result
	 *
	 * @var mixed
	 * @access public
	 */
	public $Result;

	/**
	 * Database
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $Database;

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
	 * @param Database_PDO $Database
	 */
	public function __construct (Database_PDO $Database) {
        
        $this->c = IpamConfig::config();

		# initialize Result
		$this->Result = new Result ();
		# initialize object
		$this->Database = $Database;

        $app = require(VENDOR . '/robmorgan/phinx/app/phinx.php');
        $this->wrap = new Phinx\Wrapper\TextWrapper($app, ['environment' => $this->c->environment, 
                                                           'configuration' => CONFIG_DIR . '/phinx.php', 
                                                           'parser' => 'php']);

		# Log object
		try { $this->Database->connect(); } catch ( Exception $e ) {}
	}

	/**
	 * @check methods
	 * ------------------------------
	 */

	/**
	 * @postinstallation functions
	 * ------------------------------
	 */

	/**
	 * Post installation settings update.
	 *
	 * @access public
	 * @param mixed $adminpass
	 * @param mixed $siteTitle
	 * @param mixed $siteURL
	 * @return void
	 */
	function setup_basic_save($adminpass, $siteTitle, $siteURL) {
		# update Admin pass
		$this->basic_admin_pass ($adminpass);
		# update settings
		$this->basic_settings ($siteTitle, $siteURL);
		# ok
		return true;
	}

	/**
	 * Saves admin password from setup
	 *
	 * @access public
	 * @param mixed $adminpass
	 * @return void
	 */
	public function basic_admin_pass ($adminpass) {
		try { 
            $this->Database->updateObject("users", array("password"=>$adminpass, "passChange"=>"No","username"=>"Admin"), "username"); 
        }
		catch (Exception $e) { 
            $this->Result->show("danger", $e->getMessage(), false); 
        }
		return true;
	}

	/**
	 * Saves siteURL and siteTitle from setup
	 *
	 * @access private
	 * @param mixed $siteTitle
	 * @param mixed $siteURL
	 * @return void
	 */
	private function basic_settings ($siteTitle, $siteURL) {
		try { 
            $this->Database->updateObject("settings", array("siteTitle"=>$siteTitle, "siteURL"=>$siteURL,"id"=>1), "id"); 
        }
		catch (Exception $e) { 
            $this->Result->show("danger", $e->getMessage(), false); 
        }
		return true;
	}
    
    /**
	 * Sets setup_completed to true
	 *
	 * @access private
	 * @return void
	 */
	public function mark_setup_completed () {
		try { 
            $this->Database->updateObject("settings", array("setup_completed"=>1,"id"=>1), "id"); 
        }
		catch (Exception $e) { 
            $this->Result->show("danger", $e->getMessage(), false); 
        }
		return true;
	}

	/**
	 * @upgrade database
	 * -----------------
	 */

	/**
	 * Upgrade database checks and executes.
	 *
	 * @access public
	 * @return void
	 */
	public function migrate_database () {
        return call_user_func([$this->wrap, 'getMigrate']);
	}

}