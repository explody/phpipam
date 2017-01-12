<?php

use Phinx\Migration\AbstractMigration;

class BaseSchema extends AbstractMigration
{

    public $baseSchemaTables = [
        'api' => [
            'options' => [],
            'columns' => [
                ['app_id', 'string', ['length'=>32,'default'=>'']],
                ['app_code', 'string', ['length'=>32,'default'=>'','null'=>true]],
                ['app_permissions', 'integer', ['length'=>1,'default'=>1,'null'=>true]],
                ['app_comment', 'text', ['null'=>true]],
                ['app_security', 'set', ['values'=>[0=>'crypt',1=>'ssl',2=>'user',3=>'none'],'default'=>'ssl']],
                ['app_lock', 'integer', ['length'=>1,'default'=>0]],
                ['app_lock_wait', 'integer', ['length'=>4,'default'=>30]],
            ],
            'indexes' => [
                [['app_id'],['unique'=>true]],
            ],
            'fkeys' => [
            ],
        ],
        'changelog' => [
            'options' => ['id'=>'cid'],
            'columns' => [
                ['ctype', 'set', ['values'=>[0=>'ip_addr',1=>'subnet',2=>'section'],'default'=>'']],
                ['coid', 'integer', []],
                ['cuser', 'integer', []],
                ['caction', 'set', ['values'=>[0=>'add',1=>'edit',2=>'delete',3=>'truncate',4=>'resize',5=>'perm_change'],'default'=>'edit']],
                ['cresult', 'set', ['values'=>[0=>'error',1=>'success'],'default'=>'']],
                ['cdate', 'datetime', []],
                ['cdiff', 'string', ['length'=>2048,'null'=>true]],
            ],
            'indexes' => [
                [['coid'],[]],
            ],
            'fkeys' => [
            ],
        ],
        'deviceTypes' => [
            'options' => ['id'=>'tid'],
            'columns' => [
                ['tname', 'string', ['length'=>128,'null'=>true]],
                ['tdescription', 'string', ['length'=>128,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'devices' => [
            'options' => [],
            'columns' => [
                ['hostname', 'string', ['length'=>32,'null'=>true]],
                ['ip_addr', 'string', ['length'=>100,'null'=>true]],
                ['type', 'integer', ['length'=>2,'default'=>0,'null'=>true]],
                ['description', 'string', ['length'=>256,'null'=>true]],
                ['version', 'string', ['length'=>64,'null'=>true]],
                ['sections', 'string', ['length'=>128,'null'=>true]],
                ['snmp_community', 'string', ['length'=>100,'null'=>true]],
                ['snmp_version', 'set', ['values'=>[0=>'0',1=>'1',2=>'2'],'default'=>'0','null'=>true]],
                ['snmp_port', 'integer', ['length'=>16777215,'default'=>161,'null'=>true]],
                ['snmp_timeout', 'integer', ['length'=>16777215,'default'=>1000000,'null'=>true]],
                ['rack', 'integer', ['null'=>true]],
                ['rack_start', 'integer', ['null'=>true]],
                ['rack_size', 'integer', ['null'=>true]],
                ['snmp_queries', 'string', ['length'=>128,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
                [['hostname'],[]],
            ],
            'fkeys' => [
            ],
        ],
        'instructions' => [
            'options' => [],
            'columns' => [
                ['instructions', 'text', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'ipTags' => [
            'options' => [],
            'columns' => [
                ['type', 'string', ['length'=>32,'null'=>true]],
                ['showtag', 'integer', ['length'=>255,'default'=>1,'null'=>true]],
                ['bgcolor', 'string', ['length'=>7,'default'=>'#000','null'=>true]],
                ['fgcolor', 'string', ['length'=>7,'default'=>'#fff','null'=>true]],
                ['compress', 'set', ['values'=>[0=>'No',1=>'Yes'],'default'=>'No']],
                ['locked', 'set', ['values'=>[0=>'No',1=>'Yes'],'default'=>'No']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'ipaddresses' => [
            'options' => [],
            'columns' => [
                ['subnetId', 'integer', ['null'=>true]],
                ['ip_addr', 'string', ['length'=>100]],
                ['is_gateway', 'boolean', ['default'=>0,'null'=>true]],
                ['description', 'string', ['length'=>64,'null'=>true]],
                ['dns_name', 'string', ['length'=>100,'null'=>true]],
                ['mac', 'string', ['length'=>20,'null'=>true]],
                ['owner', 'string', ['length'=>32,'null'=>true]],
                ['state', 'integer', ['length'=>3,'default'=>2,'null'=>true]],
                ['switch', 'integer', ['null'=>true]],
                ['port', 'string', ['length'=>32,'null'=>true]],
                ['note', 'text', ['null'=>true]],
                ['lastSeen', 'datetime', ['default'=>'1970-01-01 00:00:01','null'=>true]],
                ['excludePing', 'binary', ['length'=>1,'default'=>0,'null'=>true]],
                ['PTRignore', 'binary', ['length'=>1,'default'=>0,'null'=>true]],
                ['PTR', 'integer', ['default'=>0,'null'=>true]],
                ['firewallAddressObject', 'string', ['length'=>100,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
                ['location', 'integer', ['null'=>true]],
            ],
            'indexes' => [
                [['ip_addr','subnetId'],['name'=>'sid_ip_unique','unique'=>true]],
                [['subnetId'],[]],
            ],
            'fkeys' => [
            ],
        ],
        'lang' => [
            'options' => ['id'=>'l_id'],
            'columns' => [
                ['l_code', 'string', ['length'=>12,'default'=>'']],
                ['l_name', 'string', ['length'=>32,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'locations' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>128,'default'=>'']],
                ['description', 'text', ['null'=>true]],
                ['lat', 'string', ['length'=>12,'null'=>true]],
                ['long', 'string', ['length'=>12,'null'=>true]],
                ['address', 'string', ['length'=>128,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'loginAttempts' => [
            'options' => [],
            'columns' => [
                ['datetime', 'timestamp', ['default'=>'CURRENT_TIMESTAMP','update'=>'CURRENT_TIMESTAMP']],
                ['ip', 'string', ['length'=>128,'default'=>'']],
                ['count', 'integer', ['length'=>2]],
            ],
            'indexes' => [
                [['ip'],['unique'=>true]],
            ],
            'fkeys' => [
            ],
        ],
        'logs' => [
            'options' => [],
            'columns' => [
                ['severity', 'integer', ['null'=>true]],
                ['date', 'string', ['length'=>32,'null'=>true]],
                ['username', 'string', ['length'=>64,'null'=>true]],
                ['ipaddr', 'string', ['length'=>64,'null'=>true]],
                ['command', 'string', ['length'=>128,'default'=>0,'null'=>true]],
                ['details', 'string', ['length'=>1024,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'nameservers' => [
            'options' => [],
            'columns' => [
                ['name', 'string', []],
                ['namesrv1', 'string', ['null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['permissions', 'string', ['length'=>128,'null'=>true]],
                ['editDate', 'timestamp', ['update'=>'CURRENT_TIMESTAMP','null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'nat' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>64,'null'=>true]],
                ['type', 'set', ['values'=>[0=>'source',1=>'static',2=>'destination'],'default'=>'source','null'=>true]],
                ['src', 'text', ['null'=>true]],
                ['dst', 'text', ['null'=>true]],
                ['src_port', 'integer', ['length'=>5,'null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['device', 'integer', ['null'=>true]],
                ['dst_port', 'integer', ['length'=>5,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'pstnNumbers' => [
            'options' => [],
            'columns' => [
                ['prefix', 'integer', ['null'=>true]],
                ['number', 'string', ['length'=>32,'null'=>true]],
                ['name', 'string', ['length'=>128,'null'=>true]],
                ['owner', 'string', ['length'=>128,'null'=>true]],
                ['state', 'integer', ['null'=>true]],
                ['deviceId', 'integer', ['null'=>true]],
                ['description', 'text', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'pstnPrefixes' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>128,'null'=>true]],
                ['prefix', 'string', ['length'=>32,'null'=>true]],
                ['start', 'string', ['length'=>32,'null'=>true]],
                ['stop', 'string', ['length'=>32,'null'=>true]],
                ['master', 'integer', ['default'=>0,'null'=>true]],
                ['deviceId', 'integer', ['null'=>true]],
                ['description', 'text', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'racks' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>64,'default'=>'']],
                ['size', 'integer', ['length'=>2,'null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['location', 'integer', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'requests' => [
            'options' => [],
            'columns' => [
                ['subnetId', 'integer', ['null'=>true]],
                ['ip_addr', 'string', ['length'=>100,'null'=>true]],
                ['description', 'string', ['length'=>32,'null'=>true]],
                ['dns_name', 'string', ['length'=>32,'null'=>true]],
                ['state', 'integer', ['default'=>2,'null'=>true]],
                ['owner', 'string', ['length'=>32,'null'=>true]],
                ['requester', 'string', ['length'=>128,'null'=>true]],
                ['comment', 'text', ['null'=>true]],
                ['processed', 'binary', ['length'=>1,'null'=>true]],
                ['accepted', 'binary', ['length'=>1,'null'=>true]],
                ['adminComment', 'text', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'scanAgents' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>128,'null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['type', 'set', ['values'=>[0=>'direct',1=>'api',2=>'mysql'],'default'=>'']],
                ['code', 'string', ['length'=>32,'null'=>true]],
                ['last_access', 'datetime', ['null'=>true]],
            ],
            'indexes' => [
                [['code'],[]],
            ],
            'fkeys' => [
            ],
        ],
        'sections' => [
            'options' => ['id'=>true],
            'columns' => [
                ['name', 'string', ['length'=>128,'null'=>false]],
                ['description', 'text', ['null'=>true]],
                ['masterSection', 'integer', ['default'=>0,'null'=>true]],
                ['permissions', 'string', ['length'=>1024,'null'=>true]],
                ['strictMode', 'binary', ['length'=>1,'default'=>0]],
                ['subnetOrdering', 'string', ['length'=>16,'null'=>true]],
                ['order', 'integer', ['length'=>3,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
                ['showVLAN', 'boolean', ['default'=>0]],
                ['showVRF', 'boolean', ['default'=>0]],
                ['DNS', 'string', ['length'=>128,'null'=>true]],
            ],
            'indexes' => [
                [['id'],['name'=>'id_2','unique'=>true]],
                [['id'],[]],
                [['name'],['unique'=>true]],
            ],
            'fkeys' => [
            ],
        ],
        'settings' => [
            'options' => [],
            'columns' => [
                ['siteTitle', 'string', ['length'=>64,'null'=>true]],
                ['siteAdminName', 'string', ['length'=>64,'null'=>true]],
                ['siteAdminMail', 'string', ['length'=>64,'null'=>true]],
                ['siteDomain', 'string', ['length'=>32,'null'=>true]],
                ['siteURL', 'string', ['length'=>64,'null'=>true]],
                ['siteLoginText', 'string', ['length'=>128,'null'=>true]],
                ['domainAuth', 'boolean', ['null'=>true]],
                ['enableIPrequests', 'boolean', ['null'=>true]],
                ['enableVRF', 'boolean', ['default'=>1,'null'=>true]],
                ['enableDNSresolving', 'boolean', ['null'=>true]],
                ['enableFirewallZones', 'boolean', ['default'=>0]],
                ['firewallZoneSettings', 'string', ['length'=>1024,'default'=>'{"zoneLength":3,"ipType":{"0":"v4","1":"v6"},"separator":"_","indicator":{"0":"own","1":"customer"},"zoneGenerator":"2","zoneGeneratorType":{"0":"decimal","1":"hex","2":"text"},"deviceType":"3","padding":"on","strictMode":"on"}']],
                ['enablePowerDNS', 'boolean', ['default'=>0,'null'=>true]],
                ['powerDNS', 'text', ['null'=>true]],
                ['enableMulticast', 'boolean', ['default'=>0,'null'=>true]],
                ['enableNAT', 'boolean', ['default'=>0,'null'=>true]],
                ['enableSNMP', 'boolean', ['default'=>0,'null'=>true]],
                ['enableThreshold', 'boolean', ['default'=>0,'null'=>true]],
                ['enableRACK', 'boolean', ['default'=>0,'null'=>true]],
                ['link_field', 'string', ['length'=>32,'default'=>0,'null'=>true]],
                ['version', 'string', ['length'=>5,'null'=>true]],
                ['dbverified', 'binary', ['length'=>1,'default'=>0]],
                ['donate', 'boolean', ['default'=>0,'null'=>true]],
                ['IPfilter', 'string', ['length'=>128,'null'=>true]],
                ['vlanDuplicate', 'integer', ['length'=>1,'default'=>0,'null'=>true]],
                ['vlanMax', 'integer', ['length'=>8,'default'=>4096,'null'=>true]],
                ['subnetOrdering', 'string', ['length'=>16,'default'=>'subnet,asc','null'=>true]],
                ['visualLimit', 'integer', ['length'=>2,'default'=>0]],
                ['autoSuggestNetwork', 'boolean', ['default'=>0]],
                ['permitUserVlanCreate', 'boolean', ['default'=>0]],
                ['pingStatus', 'string', ['length'=>12,'default'=>'1800;3600']],
                ['defaultLang', 'integer', ['length'=>3,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
                ['vcheckDate', 'datetime', ['null'=>true]],
                ['api', 'binary', ['length'=>1,'default'=>0]],
                ['enableChangelog', 'boolean', ['default'=>1]],
                ['scanPingPath', 'string', ['length'=>64,'default'=>'/bin/ping','null'=>true]],
                ['scanFPingPath', 'string', ['length'=>64,'default'=>'/bin/fping','null'=>true]],
                ['scanPingType', 'set', ['values'=>[0=>'ping',1=>'pear',2=>'fping'],'default'=>'ping']],
                ['scanMaxThreads', 'integer', ['length'=>4,'default'=>128,'null'=>true]],
                ['prettyLinks', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No']],
                ['hiddenCustomFields', 'string', ['length'=>1024,'null'=>true]],
                ['inactivityTimeout', 'integer', ['length'=>5,'default'=>3600]],
                ['authmigrated', 'integer', ['length'=>255,'default'=>0]],
                ['tempShare', 'boolean', ['default'=>0,'null'=>true]],
                ['tempAccess', 'text', ['null'=>true]],
                ['log', 'set', ['values'=>[0=>'Database',1=>'syslog',2=>'both'],'default'=>'Database']],
                ['subnetView', 'integer', ['length'=>255,'default'=>0]],
                ['enableLocations', 'boolean', ['default'=>1]],
                ['enablePSTN', 'boolean', ['default'=>1]],
                ['DHCP', 'string', ['length'=>256,'default'=>'{"type":"kea","settings":{"file":"/etc/kea/kea.conf"}}','null'=>true]],
                ['enableDHCP', 'boolean', ['default'=>0,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'settingsDomain' => [
            'options' => [],
            'columns' => [
                ['account_suffix', 'string', ['length'=>256,'default'=>'@domain.local','null'=>true]],
                ['base_dn', 'string', ['length'=>256,'default'=>'CN=Users,CN=Company,DC=domain,DC=local','null'=>true]],
                ['domain_controllers', 'string', ['length'=>256,'default'=>'dc1.domain.local;dc2.domain.local','null'=>true]],
                ['use_ssl', 'boolean', ['default'=>0,'null'=>true]],
                ['use_tls', 'boolean', ['default'=>0,'null'=>true]],
                ['ad_port', 'integer', ['length'=>5,'default'=>389,'null'=>true]],
                ['adminUsername', 'string', ['length'=>64,'null'=>true]],
                ['adminPassword', 'string', ['length'=>64,'null'=>true]],
                ['editDate', 'timestamp', ['update'=>'CURRENT_TIMESTAMP','null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'settingsMail' => [
            'options' => [],
            'columns' => [
                ['mtype', 'set', ['values'=>[0=>'localhost',1=>'smtp'],'default'=>'localhost']],
                ['msecure', 'set', ['values'=>[0=>'none',1=>'ssl',2=>'tls'],'default'=>'none']],
                ['mauth', 'set', ['values'=>[0=>'yes',1=>'no'],'default'=>'no']],
                ['mserver', 'string', ['length'=>128,'null'=>true]],
                ['mport', 'integer', ['length'=>5,'default'=>25,'null'=>true]],
                ['muser', 'string', ['length'=>64,'null'=>true]],
                ['mpass', 'string', ['length'=>64,'null'=>true]],
                ['mAdminName', 'string', ['length'=>64,'null'=>true]],
                ['mAdminMail', 'string', ['length'=>64,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'subnets' => [
            'options' => [],
            'columns' => [
                ['subnet', 'string', ['null'=>true]],
                ['mask', 'string', ['length'=>3,'null'=>true]],
                ['sectionId', 'integer', ['null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['firewallAddressObject', 'string', ['length'=>100,'null'=>true]],
                ['vrfId', 'integer', ['null'=>true]],
                ['masterSubnetId', 'integer', ['default'=>0]],
                ['allowRequests', 'boolean', ['default'=>0,'null'=>true]],
                ['vlanId', 'integer', ['null'=>true]],
                ['showName', 'boolean', ['default'=>0,'null'=>true]],
                ['device', 'integer', ['length'=>10,'default'=>0,'null'=>true]],
                ['permissions', 'string', ['length'=>1024,'null'=>true]],
                ['pingSubnet', 'boolean', ['default'=>0,'null'=>true]],
                ['discoverSubnet', 'binary', ['length'=>1,'default'=>0,'null'=>true]],
                ['DNSrecursive', 'boolean', ['default'=>0,'null'=>true]],
                ['DNSrecords', 'boolean', ['default'=>0,'null'=>true]],
                ['nameserverId', 'integer', ['default'=>0,'null'=>true]],
                ['scanAgent', 'integer', ['null'=>true]],
                ['isFolder', 'boolean', ['default'=>0,'null'=>true]],
                ['isFull', 'boolean', ['default'=>0,'null'=>true]],
                ['state', 'integer', ['length'=>3,'default'=>2,'null'=>true]],
                ['threshold', 'integer', ['length'=>3,'default'=>0,'null'=>true]],
                ['editDate', 'timestamp', ['update'=>'CURRENT_TIMESTAMP','null'=>true]],
                ['linked_subnet', 'integer', ['null'=>true]],
                ['location', 'integer', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'userGroups' => [
            'options' => ['id'=>'g_id'],
            'columns' => [
                ['g_name', 'string', ['length'=>32,'null'=>true]],
                ['g_desc', 'string', ['length'=>1024,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'users' => [
            'options' => ['id'=>true,'primary_key'=>[0=>'username']],
            'columns' => [
                ['username', 'string', ['length'=>64,'default'=>'']],
                ['authMethod', 'integer', ['length'=>2,'default'=>1,'null'=>true]],
                ['password', 'char', ['length'=>128,'null'=>true]],
                ['groups', 'string', ['length'=>1024,'null'=>true]],
                ['role', 'text', ['null'=>true]],
                ['real_name', 'string', ['length'=>128,'null'=>true]],
                ['email', 'string', ['length'=>64,'null'=>true]],
                ['pdns', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No','null'=>true]],
                ['domainUser', 'binary', ['length'=>1,'default'=>0,'null'=>true]],
                ['widgets', 'string', ['length'=>1024,'default'=>'statistics;favourite_subnets;changelog;top10_hosts_v4','null'=>true]],
                ['lang', 'integer', ['default'=>9,'null'=>true]],
                ['favourite_subnets', 'string', ['length'=>1024,'null'=>true]],
                ['mailNotify', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No','null'=>true]],
                ['mailChangelog', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No','null'=>true]],
                ['passChange', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No']],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
                ['lastLogin', 'timestamp', ['null'=>true]],
                ['lastActivity', 'timestamp', ['null'=>true]],
                ['compressOverride', 'set', ['values'=>[0=>'default',1=>'Uncompress'],'default'=>'default']],
                ['hideFreeRange', 'boolean', ['default'=>0,'null'=>true]],
                ['token', 'string', ['length'=>24,'null'=>true]],
                ['token_valid_until', 'datetime', ['null'=>true]],
                ['pstn', 'integer', ['length'=>1,'default'=>1,'null'=>true]],
                ['menuType', 'set', ['values'=>[0=>'Static',1=>'Dynamic'],'default'=>'Dynamic','null'=>true]],
                ['editVlan', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'No','null'=>true]],
            ],
            'indexes' => [
                [['id'],[]],
                [['id'],['name'=>'id_2','unique'=>true]],
                [['username'],['unique'=>true]],
            ],
            'fkeys' => [
            ],
        ],
        'usersAuthMethod' => [
            'options' => [],
            'columns' => [
                ['type', 'set', ['values'=>[0=>'local',1=>'HTTP',2=>'AD',3=>'LDAP',4=>'NetIQ',5=>'Radius',6=>'SAML2'],'default'=>'local']],
                ['params', 'string', ['length'=>1024,'null'=>true]],
                ['protected', 'set', ['values'=>[0=>'Yes',1=>'No'],'default'=>'Yes']],
                ['description', 'text', ['null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'vlanDomains' => [
            'options' => [],
            'columns' => [
                ['name', 'string', ['length'=>64,'null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['permissions', 'string', ['length'=>128,'null'=>true]],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'vlans' => [
            'options' => ['id'=>'vlanId'],
            'columns' => [
                ['domainId', 'integer', ['default'=>1]],
                ['name', 'string', []],
                ['number', 'integer', ['length'=>4,'null'=>true]],
                ['description', 'text', ['null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'vrf' => [
            'options' => ['id'=>'vrfId'],
            'columns' => [
                ['name', 'string', ['length'=>32,'default'=>'']],
                ['rd', 'string', ['length'=>32,'null'=>true]],
                ['description', 'string', ['length'=>256,'null'=>true]],
                ['sections', 'string', ['length'=>128,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'widgets' => [
            'options' => ['id'=>'wid'],
            'columns' => [
                ['wtitle', 'string', ['length'=>64,'default'=>'']],
                ['wdescription', 'string', ['length'=>1024,'null'=>true]],
                ['wfile', 'string', ['length'=>64,'default'=>'']],
                ['wparams', 'string', ['length'=>1024,'null'=>true]],
                ['whref', 'set', ['values'=>[0=>'yes',1=>'no'],'default'=>'no']],
                ['wsize', 'set', ['values'=>[0=>'4',1=>'6',2=>'8',3=>'12'],'default'=>6]],
                ['wadminonly', 'set', ['values'=>[0=>'yes',1=>'no'],'default'=>'no']],
                ['wactive', 'set', ['values'=>[0=>'yes',1=>'no'],'default'=>'no']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'firewallZoneMapping' => [
            'options' => [],
            'columns' => [
                ['zoneId', 'integer', []],
                ['alias', 'string', ['null'=>true]],
                ['deviceId', 'integer', ['null'=>true]],
                ['interface', 'string', ['null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
                [['deviceId'],['name'=>'devId_idx']],
            ],
            'fkeys' => [
                ['deviceId', 'devices', 'id', ['delete'=>'CASCADE','update'=>'NO_ACTION']],
            ],
        ],
        'firewallZones' => [
            'options' => [],
            'columns' => [
                ['generator', 'boolean', []],
                ['length', 'integer', ['length'=>2,'null'=>true]],
                ['padding', 'boolean', ['null'=>true]],
                ['zone', 'string', ['length'=>31]],
                ['indicator', 'string', ['length'=>8]],
                ['description', 'text', ['null'=>true]],
                ['permissions', 'string', ['length'=>1024,'null'=>true]],
                ['editDate', 'timestamp', ['null'=>true,'update'=>'CURRENT_TIMESTAMP']],
            ],
            'indexes' => [
            ],
            'fkeys' => [
            ],
        ],
        'firewallZoneSubnet' => [
            'options' => ['id'=>false],
            'columns' => [
                ['zoneId', 'integer', []],
                ['subnetId', 'integer', []],
            ],
            'indexes' => [
                [['subnetId'],['name'=>'fk_subnetId_idx']],
                [['zoneId'],['name'=>'fk_zoneId_idx']],
            ],
            'fkeys' => [
                ['subnetId', 'subnets', 'id', ['delete'=>'CASCADE','update'=>'NO_ACTION']],
                ['zoneId', 'firewallZones', 'id', ['delete'=>'CASCADE','update'=>'NO_ACTION']],
            ],
        ],
    ];

    public function change()
    {
        
        foreach ($this->baseSchemaTables as $tn => $td) {
            
            $t = $this->table($tn,$td['options']);
            foreach ($td['columns'] as $col) {
                $t->addColumn($col[0], $col[1], $col[2]);
            }
            foreach ($td['indexes'] as $idx) {
                $t->addIndex($idx[0], $idx[1]);
            }
            foreach ($td['fkeys'] as $fk) {
                $t->addForeignKey($fk[0], $fk[1], $fk[2], $fk[3]);
            }
            $t->create();
        }
    
    }                
}

