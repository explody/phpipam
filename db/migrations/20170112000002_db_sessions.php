<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class DbSessions extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        $this->table('settings')->addColumn('dbSessions', 'set', array('after'=>'subnetView','values'=>array('Yes','No'), 'null'=>false, 'default'=>'No'))->update();
        $this->table('session_data', array('id'=>false, 'primary_key'=>array('session_id')))
                ->addColumn('session_id', 'string', array('limit'=>32,'default'=>''))
                ->addColumn('hash', 'string', array('limit'=>32,'default'=>''))
                ->addColumn('session_data', 'blob')
                ->addColumn('session_expire', 'integer', array('default'=>0))
                ->save();
    }
}

?>
