CREATE DATABASE IF NOT EXISTS `alibaba`;

USE `alibaba`;

CREATE TABLE IF NOT EXISTS `alibaba_data` (
  `company_name` varchar(255) NOT NULL,
  `operational_address` varchar(512) NOT NULL,
  `zip` varchar(12) NOT NULL,
  `country_region` varchar(128) NOT NULL,
  `province_state` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `address` varchar(348) NOT NULL,
  `telephone` varchar(64) NOT NULL,
  `mobile phone` varchar(64) NOT NULL,
  `fax` varchar(64) NOT NULL,
  `website` varchar(256) NOT NULL,
  `url` varchar(182) NOT NULL,
  `name` varchar(128) NOT NULL,
  `job title` varchar(256) NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `parsed_urls` (
  `parsed` varchar(286) NOT NULL,
  PRIMARY KEY (`parsed`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8 ;

