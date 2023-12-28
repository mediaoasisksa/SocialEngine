-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_ratings`
--

DROP TABLE IF EXISTS `engine4_travel_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_travel_ratings` (
  `travel_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`travel_id`,`user_id`),
  KEY `INDEX` (`travel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_travel_travels` ADD `rating` FLOAT NOT NULL;
