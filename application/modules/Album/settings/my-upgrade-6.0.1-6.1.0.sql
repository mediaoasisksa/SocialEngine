-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_ratings`
--

DROP TABLE IF EXISTS `engine4_album_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_album_ratings` (
  `album_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  `type` VARCHAR(16) NOT NULL DEFAULT 'album',
  PRIMARY KEY  (`album_id`,`user_id`, `type`),
  KEY `INDEX` (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
ALTER TABLE `engine4_album_albums` ADD `rating` FLOAT NOT NULL;
ALTER TABLE `engine4_album_photos` ADD `rating` FLOAT NOT NULL;
