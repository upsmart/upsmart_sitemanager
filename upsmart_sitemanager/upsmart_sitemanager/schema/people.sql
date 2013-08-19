CREATE TABLE `upsmart_people` (
  `person_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wordpress_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `bio` tinytext,
  `photo` longtext,
  PRIMARY KEY (`person_id`),
  KEY `wordpress_id` (`wordpress_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;