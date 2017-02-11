<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class Version129 extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        // With RepeatableMigration, we always use addColumn() because the migration will determine 
        // whether the column needs to be added or changed.
        
        // https://github.com/phpipam/phpipam/commit/bc03cd189fcaac33e47fd8baf10733068e3005d1
        if ($this->table('settingsDomain')->exists()) {
            $this->dropTable('settingsDomain');
        }
        
        // https://github.com/phpipam/phpipam/commit/e514034b0b30fae8ad47dd331b05c61f2cd7e481
        $this->table('sections')->addColumn('showSupernetOnly', 'boolean',['null'=>true,'default'=>'0','after'=>'showVRF'])
                                ->save();
                                
        // https://github.com/phpipam/phpipam/commit/af22f5484be949f73704c71be0a13becfe83464a
        $this->table('subnets')->addColumn('lastScan', 'timestamp', ['null'=>true, 'after'=>'editDate'])->save();
        $this->table('subnets')->addColumn('lastDiscovery', 'timestamp', ['null'=>true, 'after'=>'lastScan'])->save();
                               
        // https://github.com/phpipam/phpipam/commit/d33fbd4756d3ec04e410b60242061189d062173c
        $this->table('users')->addColumn('username','string',['limit'=>255])->save();
        $this->table('logs')->addColumn('username','string',['limit'=>255])->save();
        $this->table('requests')->addColumn('dns_name','string',['limit'=>100])->save();
        $this->table('requests')->addColumn('description','string',['limit'=>64])->save();
        
        // https://github.com/phpipam/phpipam/commit/19ba3624047e7dfbd18ad7bfb15e2c219c5bfe6b
        $this->table('settings')->addColumn('updateTags', 'boolean', ['default'=>'0'])->save();
        $this->table('ipTags')->addColumn('updateTag', 'boolean', ['default'=>'0'])->save();
        $this->table('ipTags')->getAdapter()->query("update ipTags set updateTag=1 where type in ('Offline','Used','Reserved','DHCP')");
        
        // https://github.com/phpipam/phpipam/commit/5ad533cfe282d8367d5908a4f0c1ecc917fa7500
        $this->table('settings')->addColumn('maintenanceMode','boolean',['default'=>'0'])->save();
        
        //https://github.com/phpipam/phpipam/commit/fb8834633454d85f45478b6c046e2c68df31df16
        $this->table('settings')->addColumn('pingStatus','string',['limit'=>32,'default'=>'1800;3600'])->save();
        
        // https://github.com/phpipam/phpipam/commit/0922e1380a3826c049488a90a984196a053b6386
        // Not applying this as we've rewritten custom fields

    }
}
