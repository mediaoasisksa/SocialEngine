ALTER TABLE `engine4_event_topics` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
-- --------------------------------------------------------

--
-- Table structure for table `engine4_event_ratings`
--

DROP TABLE IF EXISTS `engine4_event_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_event_ratings` (
  `event_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`event_id`,`user_id`),
  KEY `INDEX` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_event_events` ADD `rating` FLOAT NOT NULL;
