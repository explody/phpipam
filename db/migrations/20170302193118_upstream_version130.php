<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class UpstreamVersion130 extends Ipam\Migration\RepeatableMigration
{
    public function change()
    {
        // https://github.com/phpipam/phpipam/commit/313b294b5bf152c9a79e8c3c2c60b9fe7245e291
        $this->table('settings')->addColumn('enforceUnique','boolean',['default'=>'1'])->save();
        $this->execute("UPDATE `subnets` set `vrfId` = 0 WHERE `vrfId` IS NULL");
        $this->execute("UPDATE `settings` set `version` = '1.3'");

        // https://github.com/phpipam/phpipam/commit/fe3efe1d82cad0b87bfcefdff5cbd02e41d429dd
        $this->execute("UPDATE `lang` SET `l_code` = 'en_GB.UTF-8' WHERE `l_code` = 'en_GB.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'sl_SI.UTF-8' WHERE `l_code` = 'sl_SI.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'fr_FR.UTF-8' WHERE `l_code` = 'fr_FR.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'nl_NL.UTF-8' WHERE `l_code` = 'nl_NL.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'de_DE.UTF-8' WHERE `l_code` = 'de_DE.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'pt_BR.UTF-8' WHERE `l_code` = 'pt_BR.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'es_ES.UTF-8' WHERE `l_code` = 'es_ES.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'cs_CZ.UTF-8' WHERE `l_code` = 'cs_CZ.UTF8'");
        $this->execute("UPDATE `lang` SET `l_code` = 'en_US.UTF-8' WHERE `l_code` = 'en_US.UTF8'");
        
    }
}
