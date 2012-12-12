CREATE TABLE `upsmart_profile` (
  `wordpress_id` int(11) NOT NULL,
  `mission` longtext,
  `about` longtext,
  `history` longtext,
  PRIMARY KEY (`wordpress_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;