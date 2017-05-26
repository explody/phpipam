<?php

use Phinx\Migration\AbstractMigration;

class UpstreamVersion1302 extends AbstractMigration
{
    public function change()
    {
        # https://github.com/phpipam/phpipam/commit/d9374bc557eef4128a4f797b2896081772052b0b
        $this->table('api')->addColumn('app_nest_custom_fields','boolean',['default'=>'0'])
                           ->addColumn('app_show_links','boolean',['default'=>'0'])
                           ->save();
    
        # https://github.com/phpipam/phpipam/commit/a275e0c97d57459625fddac9608659fee15ebc5a
        $this->table('changelog')->addIndex('ctype',[])->save();
        
        # https://github.com/phpipam/phpipam/commit/f6e457b77fe1a485d879e968c9e8d4413c55d19b
        # TODO: get rid of these delimited fields
        $this->table('devices')->changeColumn('sections', 'string', ['limit'=>128,'null'=>true])->save();

        # https://github.com/phpipam/phpipam/commit/673324775ca3c5869138cbb47ae2678fed2bf4c2
        $this->execute("INSERT INTO `lang` (`l_code`, `l_name`) VALUES ('zh_CN.UTF-8', 'Chinese')");
        
        # https://github.com/phpipam/phpipam/commit/ee53b72ffccc28d845116700e29b24ad09415c39
        # I agree on extending the length, but it should be to the max allowable FQDN length - 255 chars
        $this->table('devices')->changeColumn('hostname', 'string', ['limit'=>255,'null'=>true])->save();
        
    }
}
