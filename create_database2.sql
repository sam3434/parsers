
CREATE TABLE IF NOT EXISTS `aliexpress_data` (
  `company_name` varchar(255) NOT NULL,
  `street_address` varchar(512) NOT NULL,
  `zip` varchar(12) NOT NULL,
  `country_region` varchar(128) NOT NULL,
  `province_state` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `telephone` varchar(64) NOT NULL,
  `mobile phone` varchar(64) NOT NULL,
  `fax` varchar(64) NOT NULL,
  `website` varchar(182) NOT NULL,
  `name` varchar(128) NOT NULL,
  `position` varchar(256) NOT NULL,
  PRIMARY KEY (`website`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `last_parsed_aliexpress` (
  `last_parsed_id` int NOT NULL DEFAULT -1  
) ENGINE=MyIsam  DEFAULT CHARSET=utf8 ;

INSERT INTO `last_parsed_aliexpress` VALUES(-1);