<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class SetupCompletedFlag extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        $table = $this->table('settings')
                    ->addColumn('setup_completed', 'boolean', array('default'=>0,'null'=>false))
                    ->save();
    }
}

?>
