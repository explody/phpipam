<?php

use Phinx\Migration\AbstractMigration;

class ExistingCustomFields extends AbstractMigration
{
    public function skip()
    {
        $this->table('subnets')
                ->addColumn('DHCP', 'boolean', array('default'=>0,'null'=>true,'comment'=>'TRUE/FALSE if this should be served from one of our DHCP servers'))
                ->addColumn('DHCPGW', 'string', array('length'=>128,'null'=>true,'comment'=>'DHCP Subnet Gateway'))
                ->addColumn('DHCPSTART', 'string', array('length'=>16,'null'=>true,'comment'=>'DHCP SCOPE START'))
                ->addColumn('DHCPEND', 'string', array('length'=>16,'null'=>true,'comment'=>'DHCP SCOPE END'))
                ->addColumn('DOMAIN', 'string', array('length'=>128,'null'=>true,'comment'=>'Default domain'))
                ->addColumn('DOMAIN_SEARCH', 'string', array('length'=>256,'null'=>true,'comment'=>'Space or comma delimited list of client search domains'))
                ->update();
                
        $this->table('ipaddresses')
                ->addColumn('reserve', 'boolean', array('default'=>0, 'comment'=>'True/False Reserve this address'))
                ->addColumn('external_gateway', 'boolean', array('default'=>0,'null'=>true,'comment'=>'External Gateway'))
                ->update();
                
        $this->table('devices')
                ->addColumn('vendor', 'string', array('length'=>156,'null'=>true,'comment'=>'Vendor'))
                ->addColumn('model', 'string', array('length'=>124,'null'=>true,'comment'=>'Model'))
                ->addColumn('oomnitza_url', 'string', array('null'=>true))
                ->addColumn('oomnitza_id', 'string', array('length'=>64,'null'=>true))
                ->addColumn('jss_url', 'string', array('null'=>true))
                ->addColumn('jss_id', 'integer', array('length'=>12,'null'=>true))
                ->addColumn('serial', 'string', array('length'=>64,'null'=>true))
                ->addColumn('mac_addr', 'string', array('length'=>17,'null'=>true))
                ->addColumn('last_user', 'string', array('length'=>64,'null'=>true))
                ->addColumn('model_id', 'string', array('null'=>true))
                ->addColumn('location', 'integer', array('null'=>true))
                ->update();
    }
}

?>