<?php

use Phinx\Migration\AbstractMigration;

class InitialData extends AbstractMigration
{
    
    public $initialData = [
        'instructions' => [
            'columns' => ['id','instructions'],
            'data' => [
                [1, 'You can write instructions under admin menu!']
            ]
        ],
        'settings' => [
            'columns' => ['id', 'subnetId', 'ip_addr', 'description', 'dns_name', 'state'],
            'data' => [
                [1,3,'168427779','Server1','server1.cust1.local',2],
                [2,3,'168427780','Server2','server2.cust1.local',2],
                [3,3,'168427781','Server3','server3.cust1.local',3],
                [4,3,'168427782','Server4','server4.cust1.local',3],
                [5,3,'168428021','Gateway',NULL,2],
                [6,4,'168428286','Gateway',NULL,2],
                [7,4,'168428042','Server1','ser1.client2.local',2],
                [8,6,'172037636','DHCP range',NULL,4],
                [9,6,'172037637','DHCP range',NULL,4],
                [10,6,'172037638','DHCP range',NULL,4]
            ]
        ],
        'sections' => [
            'columns' => ['id', 'name', 'description', 'permissions'],
            'data' => [
                [1,'Customers','Section for customers','{\"3\":\"1\",\"2\":\"2\"}'],
                [2,'IPv6','Section for IPv6 addresses','{\"3\":\"1\",\"2\":\"2\"}']
            ]
        ],
        'settings' => [
            'columns' => ['id', 'siteTitle', 'siteAdminName', 'siteAdminMail', 'siteDomain', 'siteURL', 'domainAuth', 'enableIPrequests', 'enableVRF', 'enableDNSresolving', 'version', 'donate', 'IPfilter', 'vlanDuplicate', 'subnetOrdering', 'visualLimit'],
            'data' => [
                [1, 'phpipam IP address management', 'Sysadmin', 'admin@domain.local', 'domain.local', 'http://yourpublicurl.com', 0, 0, 0, 0, '1.1', 0, 'mac;owner;state;switch;note;firewallAddressObject', 1, 'subnet,asc', 24]
            ]
        ],
        'settingsDomain' => [
            'columns' => ['id', 'account_suffix', 'base_dn', 'domain_controllers'],
            'data' => [
                [1,'@domain.local','CN=Users,CN=Company,DC=domain,DC=local','dc1.domain.local;dc2.domain.local']
            ]
        ],
        'settingsMail' => [
            'columns' => ['id', 'mtype'],
            'data' => [
                [1, 'localhost']
            ]
        ],
        'subnets' => [
            'columns' => ['id', 'subnet', 'mask', 'sectionId', 'description', 'vrfId', 'masterSubnetId', 'allowRequests', 'vlanId', 'showName', 'permissions', 'isFolder'],
            'data' => [
                [1,'336395549904799703390415618052362076160','64',2,'Private subnet 1',0,'0',1,1,1,'{\"3\":\"1\",\"2\":\"2\"}',0],
                [2,'168427520','16','1','Business customers',0,'0',1,0,1,'{\"3\":\"1\",\"2\":\"2\"}',0],
                [3,'168427776','24','1','Customer 1',0,'2',1,0,1,'{\"3\":\"1\",\"2\":\"2\"}',0],
                [4,'168428032','24','1','Customer 2',0,'2',1,0,1,'{\"3\":\"1\",\"2\":\"2\"}',0],
                [5, '0', '', 1, 'My folder', 0, 0, 0, 0, 0, '{\"3\":\"1\",\"2\":\"2\"}', 1],
                [6, '172037632', '24', 1, 'DHCP range', 0, 5, 0, 0, 1, '{\"3\":\"1\",\"2\":\"2\"}', 0]
            ]
        ],
        'userGroups' => [
            'columns' => ['g_id', 'g_name', 'g_desc'],
            'data' => [
                [2,'Operators','default Operator group'],
                [3,'Guests','default Guest group (viewers)']
            ]
        ],
        'users' => [
            'columns' => ['id', 'username', 'password', 'groups', 'role', 'real_name', 'email', 'domainUser','widgets', 'passChange'],
            'data' => [
                [1,'admin', NULL, '', 'Administrator', 'phpIPAM Admin', 'admin@domain.local', 0, 'statistics;favourite_subnets;changelog;access_logs;error_logs;top10_hosts_v4', 'Yes']
            ]
        ],
        'lang' => [
            'columns' => ['l_id', 'l_code', 'l_name'],
            'data' => [
                [1, 'en_GB.UTF8', 'English'],
                [2, 'sl_SI.UTF8', 'Slovenščina'],
                [3, 'fr_FR.UTF8', 'Français'],
                [4, 'nl_NL.UTF8', 'Nederlands'],
                [5, 'de_DE.UTF8', 'Deutsch'],
                [6, 'pt_BR.UTF8', 'Brazil'],
                [7, 'es_ES.UTF8', 'Español'],
                [8, 'cs_CZ.UTF8', 'Czech'],
                [9, 'en_US.UTF8', 'English (US)']
            ]
        ],
        'vlans' => [
            'columns' => ['vlanId', 'name', 'number', 'description'],
            'data' => [
                [1,'IPv6 private 1',2001,'IPv6 private 1 subnets'],
                [2,'Servers DMZ',4001,'DMZ public']
            ]
        ],
        'vlanDomains' => [
            'columns' => ['id', 'name', 'description', 'permissions'],
            'data' => [
                [1, 'default', 'default L2 domain', NULL]
            ]
        ],
        'nameservers' => [
            'columns' => ['id', 'name', 'namesrv1', 'description', 'permissions'],
            'data' => [
                [1, 'Google NS', '8.8.8.8;8.8.4.4', 'Google public nameservers', '1;2']
            ]
        ],
        'widgets' => [
            'columns' => ['wid', 'wtitle', 'wdescription', 'wfile', 'wparams', 'whref', 'wsize', 'wadminonly', 'wactive'],
            'data' => [
                [1, 'Statistics', 'Shows some statistics on number of hosts, subnets', 'statistics', NULL, 'no', '4', 'no', 'yes'],
                [2, 'Favourite subnets', 'Shows 5 favourite subnets', 'favourite_subnets', NULL, 'yes', '8', 'no', 'yes'],
                [3, 'Top 10 IPv4 subnets by number of hosts', 'Shows graph of top 10 IPv4 subnets by number of hosts', 'top10_hosts_v4', NULL, 'yes', '6', 'no', 'yes'],
                [4, 'Top 10 IPv6 subnets by number of hosts', 'Shows graph of top 10 IPv6 subnets by number of hosts', 'top10_hosts_v6', NULL, 'yes', '6', 'no', 'yes'],
                [5, 'Top 10 IPv4 subnets by usage percentage', 'Shows graph of top 10 IPv4 subnets by usage percentage', 'top10_percentage', NULL, 'yes', '6', 'no', 'yes'],
                [6, 'Last 5 change log entries', 'Shows last 5 change log entries', 'changelog', NULL, 'yes', '12', 'no', 'yes'],
                [7, 'Active IP addresses requests', 'Shows list of active IP address request', 'requests', NULL, 'yes', '6', 'yes', 'yes'],
                [8, 'Last 5 informational logs', 'Shows list of last 5 informational logs', 'access_logs', NULL, 'yes', '6', 'yes', 'yes'],
                [9, 'Last 5 warning / error logs', 'Shows list of last 5 warning and error logs', 'error_logs', NULL, 'yes', '6', 'yes', 'yes'],
                [10,'Tools menu', 'Shows quick access to tools menu', 'tools', NULL, 'yes', '6', 'no', 'yes'],
                [11,'IP Calculator', 'Shows IP calculator as widget', 'ipcalc', NULL, 'yes', '6', 'no', 'yes'],
                [12,'IP Request', 'IP Request widget', 'iprequest', NULL, 'no', '6', 'no', 'yes'],
                [13,'Threshold', 'Shows threshold usage for top 5 subnets', 'threshold', NULL, 'yes', '6', 'no', 'yes'],
                [14,'Inactive hosts', 'Shows list of inactive hosts for defined period', 'inactive-hosts', 86400, 'yes', '6', 'yes', 'yes'],
                [15, 'Locations', 'Shows map of locations', 'locations', NULL, 'yes', '6', 'no', 'yes']
            ]
        ],
        'deviceTypes' => [
            'columns' => ['tid', 'tname', 'tdescription'],
            'data' => [
                [1, 'Switch', 'Switch'],
                [2, 'Router', 'Router'],
                [3, 'Firewall', 'Firewall'],
                [4, 'Hub', 'Hub'],
                [5, 'Wireless', 'Wireless'],
                [6, 'Database', 'Database'],
                [7, 'Workstation', 'Workstation'],
                [8, 'Laptop', 'Laptop'],
                [9, 'Other', 'Other']
            ]
        ],
        'usersAuthMethod' => [
            'columns' => ['id', 'type', 'params', 'protected', 'description'],
            'data' => [
                [1, 'local', NULL, 'Yes', 'Local database'],
                [2, 'http', NULL, 'Yes', 'Apache authentication']
            ]
        ],
        'ipTags' => [
            'columns' => ['id', 'type', 'showtag', 'bgcolor', 'fgcolor', 'compress', 'locked'],
            'data' => [
                [1, 'Offline', 1, '#f59c99', '#ffffff', 'No', 'Yes'],
                [2, 'Used', 0, '#a9c9a4', '#ffffff', 'No', 'Yes'],
                [3, 'Reserved', 1, '#9ac0cd', '#ffffff', 'No', 'Yes'],
                [4, 'DHCP', 1, '#c9c9c9', '#ffffff', 'Yes', 'Yes']
            ]
        ],
        'scanAgents' => [
            'columns' => ['id', 'name', 'description', 'type'],
            'data' => [
                [1, 'localhost', 'Scanning from local machine', 'direct']
            ]
        ]
        
    ];

    public function change() {
        
        foreach ($this->initialData as $table => $tableInitialData) {
            $columns = $tableInitialData['columns'];
            $current_table = $this->table($table);
            $inserts = [];
            foreach ($tableInitialData['data'] as $row) {
                $insert = array_combine($columns, $row);
                
                // To make this repeatable, check to see if the target table already has a row
                // with the ID in the insert statement.  If so, do not try and re-insert the data.
                $id_field = $columns[0];
                $id_val = $row[0];
                $st = $current_table->getAdapter()->query("select count(*) as count from $table where $id_field=$id_val");
                $st->execute();
                $count = $st->fetch()['count'];
                
                // if 0, the row does not exist, so insert it
                if ($count == 0) {
                    array_push($inserts, $insert);
                }
            }
            
            if (count($inserts) > 0) {
                $current_table->insert($inserts)->save();
            }

        }
        
    }

}

?>