-- MySQL dump 10.13  Distrib 5.7.16, for osx10.11 (x86_64)
--
-- Host: localhost    Database: phpipam_dev
-- ------------------------------------------------------
-- Server version	5.7.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api`
--

DROP TABLE IF EXISTS `api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` varchar(32) NOT NULL DEFAULT '',
  `app_code` varchar(32) DEFAULT '',
  `app_permissions` int(1) DEFAULT '1',
  `app_comment` text,
  `app_security` set('crypt','ssl','user','none') NOT NULL DEFAULT 'ssl',
  `app_lock` int(1) NOT NULL DEFAULT '0',
  `app_lock_wait` int(4) NOT NULL DEFAULT '30',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api`
--

LOCK TABLES `api` WRITE;
/*!40000 ALTER TABLE `api` DISABLE KEYS */;
/*!40000 ALTER TABLE `api` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `changelog`
--

DROP TABLE IF EXISTS `changelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changelog` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `ctype` set('ip_addr','subnet','section') NOT NULL DEFAULT '',
  `coid` int(11) NOT NULL,
  `cuser` int(11) NOT NULL,
  `caction` set('add','edit','delete','truncate','resize','perm_change') NOT NULL DEFAULT 'edit',
  `cresult` set('error','success') NOT NULL DEFAULT '',
  `cdate` datetime NOT NULL,
  `cdiff` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `coid` (`coid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `changelog`
--

LOCK TABLES `changelog` WRITE;
/*!40000 ALTER TABLE `changelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `changelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customFields`
--

DROP TABLE IF EXISTS `customFields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customFields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `limit` int(11) DEFAULT '128',
  `order` int(11) NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `null` tinyint(1) NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `default` varchar(64) DEFAULT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customFields`
--

LOCK TABLES `customFields` WRITE;
/*!40000 ALTER TABLE `customFields` DISABLE KEYS */;
INSERT INTO `customFields` VALUES (1,'deviceTypes','description','description','string',NULL,128,1,0,1,1,NULL,'[]');
INSERT INTO `customFields` VALUES (2,'firewallZones','length','length','integer',NULL,2,1,0,1,1,NULL,'[]');
INSERT INTO `customFields` VALUES (3,'settings','permitUserVlanCreate','permitUserVlanCreate','integer',NULL,1,1,0,0,1,NULL,'[]');
/*!40000 ALTER TABLE `customFields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deviceTypes`
--

DROP TABLE IF EXISTS `deviceTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deviceTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deviceTypes`
--

LOCK TABLES `deviceTypes` WRITE;
/*!40000 ALTER TABLE `deviceTypes` DISABLE KEYS */;
INSERT INTO `deviceTypes` VALUES (1,'Switch','Switch');
INSERT INTO `deviceTypes` VALUES (2,'Router','Router');
INSERT INTO `deviceTypes` VALUES (3,'Firewall','Firewall');
INSERT INTO `deviceTypes` VALUES (4,'Hub','Hub');
INSERT INTO `deviceTypes` VALUES (5,'Wireless','Wireless');
INSERT INTO `deviceTypes` VALUES (6,'Database','Database');
INSERT INTO `deviceTypes` VALUES (7,'Workstation','Workstation');
INSERT INTO `deviceTypes` VALUES (8,'Laptop','Laptop');
INSERT INTO `deviceTypes` VALUES (9,'Other','Other');
/*!40000 ALTER TABLE `deviceTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(32) DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  `type` int(2) DEFAULT '0',
  `description` varchar(256) DEFAULT NULL,
  `version` varchar(64) DEFAULT NULL,
  `sections` varchar(128) DEFAULT NULL,
  `snmp_community` varchar(100) DEFAULT NULL,
  `snmp_version` set('0','1','2') DEFAULT '0',
  `snmp_port` mediumint(9) DEFAULT '161',
  `snmp_timeout` mediumint(9) DEFAULT '1000000',
  `rack` int(11) DEFAULT NULL,
  `rack_start` int(11) DEFAULT NULL,
  `rack_size` int(11) DEFAULT NULL,
  `snmp_queries` varchar(128) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hostname` (`hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `firewallZoneMapping`
--

DROP TABLE IF EXISTS `firewallZoneMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firewallZoneMapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zoneId` int(11) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `deviceId` int(11) DEFAULT NULL,
  `interface` varchar(255) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `devId_idx` (`deviceId`),
  CONSTRAINT `firewallzonemapping_ibfk_1` FOREIGN KEY (`deviceId`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `firewallZoneMapping`
--

LOCK TABLES `firewallZoneMapping` WRITE;
/*!40000 ALTER TABLE `firewallZoneMapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `firewallZoneMapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `firewallZoneSubnet`
--

DROP TABLE IF EXISTS `firewallZoneSubnet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firewallZoneSubnet` (
  `zoneId` int(11) NOT NULL,
  `subnetId` int(11) NOT NULL,
  KEY `fk_subnetId_idx` (`subnetId`),
  KEY `fk_zoneId_idx` (`zoneId`),
  CONSTRAINT `firewallzonesubnet_ibfk_1` FOREIGN KEY (`subnetId`) REFERENCES `subnets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `firewallzonesubnet_ibfk_2` FOREIGN KEY (`zoneId`) REFERENCES `firewallZones` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `firewallZoneSubnet`
--

LOCK TABLES `firewallZoneSubnet` WRITE;
/*!40000 ALTER TABLE `firewallZoneSubnet` DISABLE KEYS */;
/*!40000 ALTER TABLE `firewallZoneSubnet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `firewallZones`
--

DROP TABLE IF EXISTS `firewallZones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firewallZones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `generator` tinyint(1) NOT NULL,
  `length` int(2) DEFAULT NULL,
  `padding` tinyint(1) DEFAULT NULL,
  `zone` varchar(31) NOT NULL,
  `indicator` varchar(8) NOT NULL,
  `description` text,
  `permissions` varchar(1024) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `firewallZones`
--

LOCK TABLES `firewallZones` WRITE;
/*!40000 ALTER TABLE `firewallZones` DISABLE KEYS */;
/*!40000 ALTER TABLE `firewallZones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructions`
--

DROP TABLE IF EXISTS `instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instructions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructions`
--

LOCK TABLES `instructions` WRITE;
/*!40000 ALTER TABLE `instructions` DISABLE KEYS */;
INSERT INTO `instructions` VALUES (1,'You can write instructions under admin menu!');
/*!40000 ALTER TABLE `instructions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipTags`
--

DROP TABLE IF EXISTS `ipTags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) DEFAULT NULL,
  `showtag` tinyint(4) DEFAULT '1',
  `bgcolor` varchar(7) DEFAULT '#000',
  `fgcolor` varchar(7) DEFAULT '#fff',
  `compress` set('No','Yes') NOT NULL DEFAULT 'No',
  `locked` set('No','Yes') NOT NULL DEFAULT 'No',
  `updateTag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipTags`
--

LOCK TABLES `ipTags` WRITE;
/*!40000 ALTER TABLE `ipTags` DISABLE KEYS */;
INSERT INTO `ipTags` VALUES (1,'Offline',1,'#f59c99','#ffffff','No','Yes',1);
INSERT INTO `ipTags` VALUES (2,'Used',0,'#a9c9a4','#ffffff','No','Yes',1);
INSERT INTO `ipTags` VALUES (3,'Reserved',1,'#9ac0cd','#ffffff','No','Yes',1);
INSERT INTO `ipTags` VALUES (4,'DHCP',1,'#c9c9c9','#ffffff','Yes','Yes',1);
/*!40000 ALTER TABLE `ipTags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipaddresses`
--

DROP TABLE IF EXISTS `ipaddresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipaddresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnetId` int(11) DEFAULT NULL,
  `ip_addr` varchar(100) NOT NULL,
  `is_gateway` tinyint(1) DEFAULT '0',
  `description` varchar(64) DEFAULT NULL,
  `dns_name` varchar(100) DEFAULT NULL,
  `mac` varchar(20) DEFAULT NULL,
  `owner` varchar(32) DEFAULT NULL,
  `state` int(3) DEFAULT '2',
  `switch` int(11) DEFAULT NULL,
  `port` varchar(32) DEFAULT NULL,
  `note` text,
  `lastSeen` datetime DEFAULT '1970-01-01 00:00:01',
  `excludePing` binary(1) DEFAULT '0',
  `PTRignore` binary(1) DEFAULT '0',
  `PTR` int(11) DEFAULT '0',
  `firewallAddressObject` varchar(100) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `location` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_ip_unique` (`ip_addr`,`subnetId`),
  KEY `subnetId` (`subnetId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipaddresses`
--

LOCK TABLES `ipaddresses` WRITE;
/*!40000 ALTER TABLE `ipaddresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `ipaddresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_code` varchar(12) NOT NULL DEFAULT '',
  `l_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` VALUES (1,'en_GB.UTF8','English');
INSERT INTO `lang` VALUES (2,'sl_SI.UTF8','Slovenščina');
INSERT INTO `lang` VALUES (3,'fr_FR.UTF8','Français');
INSERT INTO `lang` VALUES (4,'nl_NL.UTF8','Nederlands');
INSERT INTO `lang` VALUES (5,'de_DE.UTF8','Deutsch');
INSERT INTO `lang` VALUES (6,'pt_BR.UTF8','Brazil');
INSERT INTO `lang` VALUES (7,'es_ES.UTF8','Español');
INSERT INTO `lang` VALUES (8,'cs_CZ.UTF8','Czech');
INSERT INTO `lang` VALUES (9,'en_US.UTF8','English (US)');
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` text,
  `lat` varchar(12) DEFAULT NULL,
  `long` varchar(12) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loginAttempts`
--

DROP TABLE IF EXISTS `loginAttempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loginAttempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(128) NOT NULL DEFAULT '',
  `count` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loginAttempts`
--

LOCK TABLES `loginAttempts` WRITE;
/*!40000 ALTER TABLE `loginAttempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `loginAttempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `severity` int(11) DEFAULT NULL,
  `date` varchar(32) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `ipaddr` varchar(64) DEFAULT NULL,
  `command` varchar(128) DEFAULT '0',
  `details` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1000,'BaseSchema','2017-02-01 23:20:32','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (1001,'InitialData','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (1002,'SetupCompletedFlag','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170112000000,'DeviceGroupSettings','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170112000001,'StandardizeDeviceTypes','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170112000002,'DbSessions','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170112171142,'CustomFieldsTable','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170123062524,'CustomFieldConversion','2017-02-01 23:20:33','2017-02-01 23:20:33',0);
INSERT INTO `migrations` VALUES (20170129170034,'Version129','2017-02-01 23:20:33','2017-02-01 23:20:34',0);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nameservers`
--

DROP TABLE IF EXISTS `nameservers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nameservers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `namesrv1` varchar(255) DEFAULT NULL,
  `description` text,
  `permissions` varchar(128) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nameservers`
--

LOCK TABLES `nameservers` WRITE;
/*!40000 ALTER TABLE `nameservers` DISABLE KEYS */;
INSERT INTO `nameservers` VALUES (1,'Google NS','8.8.8.8;8.8.4.4','Google public nameservers','1;2',NULL);
/*!40000 ALTER TABLE `nameservers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nat`
--

DROP TABLE IF EXISTS `nat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `type` set('source','static','destination') DEFAULT 'source',
  `src` text,
  `dst` text,
  `src_port` int(5) DEFAULT NULL,
  `description` text,
  `device` int(11) DEFAULT NULL,
  `dst_port` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nat`
--

LOCK TABLES `nat` WRITE;
/*!40000 ALTER TABLE `nat` DISABLE KEYS */;
/*!40000 ALTER TABLE `nat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pstnNumbers`
--

DROP TABLE IF EXISTS `pstnNumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pstnNumbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` int(11) DEFAULT NULL,
  `number` varchar(32) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `owner` varchar(128) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `deviceId` int(11) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pstnNumbers`
--

LOCK TABLES `pstnNumbers` WRITE;
/*!40000 ALTER TABLE `pstnNumbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `pstnNumbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pstnPrefixes`
--

DROP TABLE IF EXISTS `pstnPrefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pstnPrefixes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `start` varchar(32) DEFAULT NULL,
  `stop` varchar(32) DEFAULT NULL,
  `master` int(11) DEFAULT '0',
  `deviceId` int(11) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pstnPrefixes`
--

LOCK TABLES `pstnPrefixes` WRITE;
/*!40000 ALTER TABLE `pstnPrefixes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pstnPrefixes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `racks`
--

DROP TABLE IF EXISTS `racks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `racks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `size` int(2) DEFAULT NULL,
  `description` text,
  `location` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racks`
--

LOCK TABLES `racks` WRITE;
/*!40000 ALTER TABLE `racks` DISABLE KEYS */;
/*!40000 ALTER TABLE `racks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnetId` int(11) DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  `description` varchar(32) DEFAULT NULL,
  `dns_name` varchar(32) DEFAULT NULL,
  `state` int(11) DEFAULT '2',
  `owner` varchar(32) DEFAULT NULL,
  `requester` varchar(128) DEFAULT NULL,
  `comment` text,
  `processed` binary(1) DEFAULT NULL,
  `accepted` binary(1) DEFAULT NULL,
  `adminComment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scanAgents`
--

DROP TABLE IF EXISTS `scanAgents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scanAgents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `description` text,
  `type` set('direct','api','mysql') NOT NULL DEFAULT '',
  `code` varchar(32) DEFAULT NULL,
  `last_access` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scanAgents`
--

LOCK TABLES `scanAgents` WRITE;
/*!40000 ALTER TABLE `scanAgents` DISABLE KEYS */;
INSERT INTO `scanAgents` VALUES (1,'localhost','Scanning from local machine','direct',NULL,NULL);
/*!40000 ALTER TABLE `scanAgents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text,
  `masterSection` int(11) DEFAULT '0',
  `permissions` varchar(1024) DEFAULT NULL,
  `strictMode` binary(1) NOT NULL DEFAULT '0',
  `subnetOrdering` varchar(16) DEFAULT NULL,
  `order` int(3) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `showVLAN` tinyint(1) NOT NULL DEFAULT '0',
  `showVRF` tinyint(1) NOT NULL DEFAULT '0',
  `DNS` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'Customers','Section for customers',0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}','0',NULL,NULL,NULL,0,0,NULL,0);
INSERT INTO `sections` VALUES (2,'IPv6','Section for IPv6 addresses',0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}','0',NULL,NULL,NULL,0,0,NULL,0);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_data`
--

DROP TABLE IF EXISTS `session_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_data` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `session_data` blob NOT NULL,
  `session_expire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_data`
--

LOCK TABLES `session_data` WRITE;
/*!40000 ALTER TABLE `session_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteTitle` varchar(64) DEFAULT NULL,
  `siteAdminName` varchar(64) DEFAULT NULL,
  `siteAdminMail` varchar(64) DEFAULT NULL,
  `siteDomain` varchar(32) DEFAULT NULL,
  `siteURL` varchar(64) DEFAULT NULL,
  `siteLoginText` varchar(128) DEFAULT NULL,
  `domainAuth` tinyint(1) DEFAULT NULL,
  `enableIPrequests` tinyint(1) DEFAULT NULL,
  `enableVRF` tinyint(1) DEFAULT '1',
  `enableDNSresolving` tinyint(1) DEFAULT NULL,
  `enableFirewallZones` tinyint(1) NOT NULL DEFAULT '0',
  `firewallZoneSettings` varchar(1024) NOT NULL DEFAULT '{"zoneLength":3,"ipType":{"0":"v4","1":"v6"},"separator":"_","indicator":{"0":"own","1":"customer"},"zoneGenerator":"2","zoneGeneratorType":{"0":"decimal","1":"hex","2":"text"},"deviceType":"3","padding":"on","strictMode":"on"}',
  `enablePowerDNS` tinyint(1) DEFAULT '0',
  `powerDNS` text,
  `enableMulticast` tinyint(1) DEFAULT '0',
  `enableNAT` tinyint(1) DEFAULT '0',
  `enableSNMP` tinyint(1) DEFAULT '0',
  `enableThreshold` tinyint(1) DEFAULT '0',
  `enableRACK` tinyint(1) DEFAULT '0',
  `link_field` varchar(32) DEFAULT '0',
  `version` varchar(5) DEFAULT NULL,
  `dbverified` binary(1) NOT NULL DEFAULT '0',
  `donate` tinyint(1) DEFAULT '0',
  `IPfilter` varchar(128) DEFAULT NULL,
  `vlanDuplicate` int(1) DEFAULT '0',
  `vlanMax` int(8) DEFAULT '4096',
  `subnetOrdering` varchar(16) DEFAULT 'subnet,asc',
  `visualLimit` int(2) NOT NULL DEFAULT '0',
  `autoSuggestNetwork` tinyint(1) NOT NULL DEFAULT '0',
  `permitUserVlanCreate` tinyint(1) NOT NULL DEFAULT '0',
  `pingStatus` varchar(12) NOT NULL DEFAULT '1800;3600',
  `defaultLang` int(3) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `vcheckDate` datetime DEFAULT NULL,
  `api` binary(1) NOT NULL DEFAULT '0',
  `enableChangelog` tinyint(1) NOT NULL DEFAULT '1',
  `scanPingPath` varchar(64) DEFAULT '/bin/ping',
  `scanFPingPath` varchar(64) DEFAULT '/bin/fping',
  `scanPingType` set('ping','pear','fping') NOT NULL DEFAULT 'ping',
  `scanMaxThreads` int(4) DEFAULT '128',
  `prettyLinks` set('Yes','No') NOT NULL DEFAULT 'No',
  `hiddenCustomFields` varchar(1024) DEFAULT NULL,
  `inactivityTimeout` int(5) NOT NULL DEFAULT '3600',
  `authmigrated` tinyint(4) NOT NULL DEFAULT '0',
  `tempShare` tinyint(1) DEFAULT '0',
  `tempAccess` text,
  `log` set('Database','syslog','both') NOT NULL DEFAULT 'Database',
  `subnetView` tinyint(4) NOT NULL DEFAULT '0',
  `enableLocations` tinyint(1) NOT NULL DEFAULT '1',
  `enablePSTN` tinyint(1) NOT NULL DEFAULT '1',
  `DHCP` varchar(256) DEFAULT '{"type":"kea","settings":{"file":"/etc/kea/kea.conf"}}',
  `enableDHCP` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'phpipam IP address management','Sysadmin','admin@domain.local','domain.local','http://yourpublicurl.com',NULL,0,0,0,0,0,'{\"zoneLength\":3,\"ipType\":{\"0\":\"v4\",\"1\":\"v6\"},\"separator\":\"_\",\"indicator\":{\"0\":\"own\",\"1\":\"customer\"},\"zoneGenerator\":\"2\",\"zoneGeneratorType\":{\"0\":\"decimal\",\"1\":\"hex\",\"2\":\"text\"},\"deviceType\":\"3\",\"padding\":\"on\",\"strictMode\":\"on\"}',0,NULL,0,0,0,0,0,'0','1.1','0',0,'mac;owner;state;switch;note;firewallAddressObject',1,4096,'subnet,asc',24,0,0,'1800;3600',NULL,NULL,NULL,'0',1,'/bin/ping','/bin/fping','ping',128,'No',NULL,3600,0,0,NULL,'Database',0,'No',1,1,'{\"type\":\"kea\",\"settings\":{\"file\":\"/etc/kea/kea.conf\"}}',0,0,0,'',0,0);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settingsMail`
--

DROP TABLE IF EXISTS `settingsMail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settingsMail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mtype` set('localhost','smtp') NOT NULL DEFAULT 'localhost',
  `msecure` set('none','ssl','tls') NOT NULL DEFAULT 'none',
  `mauth` set('yes','no') NOT NULL DEFAULT 'no',
  `mserver` varchar(128) DEFAULT NULL,
  `mport` int(5) DEFAULT '25',
  `muser` varchar(64) DEFAULT NULL,
  `mpass` varchar(64) DEFAULT NULL,
  `mAdminName` varchar(64) DEFAULT NULL,
  `mAdminMail` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settingsMail`
--

LOCK TABLES `settingsMail` WRITE;
/*!40000 ALTER TABLE `settingsMail` DISABLE KEYS */;
INSERT INTO `settingsMail` VALUES (1,'localhost','none','no',NULL,25,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `settingsMail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subnets`
--

DROP TABLE IF EXISTS `subnets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subnets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnet` varchar(255) DEFAULT NULL,
  `mask` varchar(3) DEFAULT NULL,
  `sectionId` int(11) DEFAULT NULL,
  `description` text,
  `firewallAddressObject` varchar(100) DEFAULT NULL,
  `vrfId` int(11) DEFAULT NULL,
  `masterSubnetId` int(11) NOT NULL DEFAULT '0',
  `allowRequests` tinyint(1) DEFAULT '0',
  `vlanId` int(11) DEFAULT NULL,
  `showName` tinyint(1) DEFAULT '0',
  `device` int(10) DEFAULT '0',
  `permissions` varchar(1024) DEFAULT NULL,
  `pingSubnet` tinyint(1) DEFAULT '0',
  `discoverSubnet` binary(1) DEFAULT '0',
  `DNSrecursive` tinyint(1) DEFAULT '0',
  `DNSrecords` tinyint(1) DEFAULT '0',
  `nameserverId` int(11) DEFAULT '0',
  `scanAgent` int(11) DEFAULT NULL,
  `isFolder` tinyint(1) DEFAULT '0',
  `isFull` tinyint(1) DEFAULT '0',
  `state` int(3) DEFAULT '2',
  `threshold` int(3) DEFAULT '0',
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `linked_subnet` int(11) DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subnets`
--

LOCK TABLES `subnets` WRITE;
/*!40000 ALTER TABLE `subnets` DISABLE KEYS */;
INSERT INTO `subnets` VALUES (1,'336395549904799703390415618052362076160','64',2,'Private subnet 1',NULL,0,0,1,1,1,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,0,0,2,0,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `subnets` VALUES (2,'168427520','16',1,'Business customers',NULL,0,0,1,0,1,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,0,0,2,0,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `subnets` VALUES (3,'168427776','24',1,'Customer 1',NULL,0,2,1,0,1,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,0,0,2,0,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `subnets` VALUES (4,'168428032','24',1,'Customer 2',NULL,0,2,1,0,1,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,0,0,2,0,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `subnets` VALUES (5,'0','',1,'My folder',NULL,0,0,0,0,0,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,1,0,2,0,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `subnets` VALUES (6,'172037632','24',1,'DHCP range',NULL,0,5,0,0,1,0,'{\\\"3\\\":\\\"1\\\",\\\"2\\\":\\\"2\\\"}',0,'0',0,0,0,NULL,0,0,2,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `subnets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userGroups`
--

DROP TABLE IF EXISTS `userGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userGroups` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_name` varchar(32) DEFAULT NULL,
  `g_desc` varchar(1024) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userGroups`
--

LOCK TABLES `userGroups` WRITE;
/*!40000 ALTER TABLE `userGroups` DISABLE KEYS */;
INSERT INTO `userGroups` VALUES (2,'Operators','default Operator group',NULL);
INSERT INTO `userGroups` VALUES (3,'Guests','default Guest group (viewers)',NULL);
/*!40000 ALTER TABLE `userGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `authMethod` int(2) DEFAULT '1',
  `password` char(128) DEFAULT NULL,
  `groups` varchar(1024) DEFAULT NULL,
  `role` text,
  `real_name` varchar(128) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `pdns` set('Yes','No') DEFAULT 'No',
  `domainUser` binary(1) DEFAULT '0',
  `widgets` varchar(1024) DEFAULT 'statistics;favourite_subnets;changelog;top10_hosts_v4',
  `lang` int(11) DEFAULT '9',
  `favourite_subnets` varchar(1024) DEFAULT NULL,
  `mailNotify` set('Yes','No') DEFAULT 'No',
  `mailChangelog` set('Yes','No') DEFAULT 'No',
  `passChange` set('Yes','No') NOT NULL DEFAULT 'No',
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lastLogin` timestamp NULL DEFAULT NULL,
  `lastActivity` timestamp NULL DEFAULT NULL,
  `compressOverride` set('default','Uncompress') NOT NULL DEFAULT 'default',
  `hideFreeRange` tinyint(1) DEFAULT '0',
  `token` varchar(24) DEFAULT NULL,
  `token_valid_until` datetime DEFAULT NULL,
  `pstn` int(1) DEFAULT '1',
  `menuType` set('Static','Dynamic') DEFAULT 'Dynamic',
  `editVlan` set('Yes','No') DEFAULT 'No',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin',1,NULL,'','Administrator','phpIPAM Admin','admin@domain.local','No','0','statistics;favourite_subnets;changelog;access_logs;error_logs;top10_hosts_v4',9,NULL,'No','No','Yes',NULL,NULL,NULL,'default',0,NULL,NULL,1,'Dynamic','No');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usersAuthMethod`
--

DROP TABLE IF EXISTS `usersAuthMethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usersAuthMethod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` set('local','HTTP','AD','LDAP','NetIQ','Radius','SAML2') NOT NULL DEFAULT 'local',
  `params` varchar(1024) DEFAULT NULL,
  `protected` set('Yes','No') NOT NULL DEFAULT 'Yes',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usersAuthMethod`
--

LOCK TABLES `usersAuthMethod` WRITE;
/*!40000 ALTER TABLE `usersAuthMethod` DISABLE KEYS */;
INSERT INTO `usersAuthMethod` VALUES (1,'local',NULL,'Yes','Local database');
INSERT INTO `usersAuthMethod` VALUES (2,'HTTP',NULL,'Yes','Apache authentication');
/*!40000 ALTER TABLE `usersAuthMethod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlanDomains`
--

DROP TABLE IF EXISTS `vlanDomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlanDomains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `description` text,
  `permissions` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlanDomains`
--

LOCK TABLES `vlanDomains` WRITE;
/*!40000 ALTER TABLE `vlanDomains` DISABLE KEYS */;
INSERT INTO `vlanDomains` VALUES (1,'default','default L2 domain',NULL);
/*!40000 ALTER TABLE `vlanDomains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlans`
--

DROP TABLE IF EXISTS `vlans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlans` (
  `vlanId` int(11) NOT NULL AUTO_INCREMENT,
  `domainId` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `number` int(4) DEFAULT NULL,
  `description` text,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vlanId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlans`
--

LOCK TABLES `vlans` WRITE;
/*!40000 ALTER TABLE `vlans` DISABLE KEYS */;
INSERT INTO `vlans` VALUES (1,1,'IPv6 private 1',2001,'IPv6 private 1 subnets',NULL);
INSERT INTO `vlans` VALUES (2,1,'Servers DMZ',4001,'DMZ public',NULL);
/*!40000 ALTER TABLE `vlans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrf`
--

DROP TABLE IF EXISTS `vrf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vrf` (
  `vrfId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `rd` varchar(32) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `sections` varchar(128) DEFAULT NULL,
  `editDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vrfId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrf`
--

LOCK TABLES `vrf` WRITE;
/*!40000 ALTER TABLE `vrf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vrf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `wtitle` varchar(64) NOT NULL DEFAULT '',
  `wdescription` varchar(1024) DEFAULT NULL,
  `wfile` varchar(64) NOT NULL DEFAULT '',
  `wparams` varchar(1024) DEFAULT NULL,
  `whref` set('yes','no') NOT NULL DEFAULT 'no',
  `wsize` set('4','6','8','12') NOT NULL DEFAULT '6,8',
  `wadminonly` set('yes','no') NOT NULL DEFAULT 'no',
  `wactive` set('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widgets`
--

LOCK TABLES `widgets` WRITE;
/*!40000 ALTER TABLE `widgets` DISABLE KEYS */;
INSERT INTO `widgets` VALUES (1,'Statistics','Shows some statistics on number of hosts, subnets','statistics',NULL,'no','4','no','yes');
INSERT INTO `widgets` VALUES (2,'Favourite subnets','Shows 5 favourite subnets','favourite_subnets',NULL,'yes','8','no','yes');
INSERT INTO `widgets` VALUES (3,'Top 10 IPv4 subnets by number of hosts','Shows graph of top 10 IPv4 subnets by number of hosts','top10_hosts_v4',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (4,'Top 10 IPv6 subnets by number of hosts','Shows graph of top 10 IPv6 subnets by number of hosts','top10_hosts_v6',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (5,'Top 10 IPv4 subnets by usage percentage','Shows graph of top 10 IPv4 subnets by usage percentage','top10_percentage',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (6,'Last 5 change log entries','Shows last 5 change log entries','changelog',NULL,'yes','12','no','yes');
INSERT INTO `widgets` VALUES (7,'Active IP addresses requests','Shows list of active IP address request','requests',NULL,'yes','6','yes','yes');
INSERT INTO `widgets` VALUES (8,'Last 5 informational logs','Shows list of last 5 informational logs','access_logs',NULL,'yes','6','yes','yes');
INSERT INTO `widgets` VALUES (9,'Last 5 warning / error logs','Shows list of last 5 warning and error logs','error_logs',NULL,'yes','6','yes','yes');
INSERT INTO `widgets` VALUES (10,'Tools menu','Shows quick access to tools menu','tools',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (11,'IP Calculator','Shows IP calculator as widget','ipcalc',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (12,'IP Request','IP Request widget','iprequest',NULL,'no','6','no','yes');
INSERT INTO `widgets` VALUES (13,'Threshold','Shows threshold usage for top 5 subnets','threshold',NULL,'yes','6','no','yes');
INSERT INTO `widgets` VALUES (14,'Inactive hosts','Shows list of inactive hosts for defined period','inactive-hosts','86400','yes','6','yes','yes');
INSERT INTO `widgets` VALUES (15,'Locations','Shows map of locations','locations',NULL,'yes','6','no','yes');
/*!40000 ALTER TABLE `widgets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-21 16:56:24
