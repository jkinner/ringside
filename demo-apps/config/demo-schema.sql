
DROP TABLE IF EXISTS `suggestions`;

CREATE TABLE `suggestions` (
  `sid` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `owneruid` int(10) unsigned NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `topic` varchar(80) NOT NULL,
  `suggestion` varchar(80) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=100010 DEFAULT CHARSET=utf8;

DROP DATABASE IF EXISTS `footprint`;
CREATE DATABASE `footprint` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `footprint`;

CREATE TABLE IF NOT EXISTS `footprints` (
  `from` int(11) NOT NULL default '0',
  `to` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
