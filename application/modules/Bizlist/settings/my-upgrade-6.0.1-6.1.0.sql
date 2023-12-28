-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_ratings`
--

DROP TABLE IF EXISTS `engine4_bizlist_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_bizlist_ratings` (
  `bizlist_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`bizlist_id`,`user_id`),
  KEY `INDEX` (`bizlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_bizlist_bizlists` ADD `rating` FLOAT NOT NULL;
