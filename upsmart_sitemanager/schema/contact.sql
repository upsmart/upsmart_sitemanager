CREATE TABLE `upsmart_contact` (
  `wordpress_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`wordpress_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;