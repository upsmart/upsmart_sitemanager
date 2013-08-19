CREATE TABLE `upsmart_products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wordpress_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `shortdesc` varchar(140) DEFAULT NULL,
  `longdesc` text,
  `photo` longblob,
  PRIMARY KEY (`product_id`),
  KEY `wordpress_id` (`wordpress_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;