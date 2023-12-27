ALTER TABLE `engine4_group_topics` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_group_ratings`
--

DROP TABLE IF EXISTS `engine4_group_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_group_ratings` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`group_id`,`user_id`),
  KEY `INDEX` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_group_groups` ADD `rating` FLOAT NOT NULL;

