CREATE TABLE `upsmart_business` (
  `wordpress_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `incorporated` tinyint(1) unsigned NOT NULL,
  `url` varchar(150) NOT NULL,
  PRIMARY KEY (`wordpress_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;