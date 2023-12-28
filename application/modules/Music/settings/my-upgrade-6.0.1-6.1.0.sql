INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('music_admin_main_categories', 'music', 'Categories', '', '{"route":"admin_default","module":"music","controller":"settings", "action":"categories"}', 'music_admin_main', '', 4);

ALTER TABLE `engine4_music_playlists` ADD `category_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_music_playlists` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_music_playlists` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_categories`
--

DROP TABLE IF EXISTS `engine4_music_categories`;
CREATE TABLE `engine4_music_categories` (
  `category_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  `subcat_id` INT(11) NOT NULL DEFAULT '0',
  `subsubcat_id` INT(11) NOT NULL DEFAULT '0',
  `order` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`, `category_name`),
  KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_music_categories`
--

INSERT IGNORE INTO `engine4_music_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Classical'),
(2, 1, 'Country'),
(3, 1, 'Folk'),
(4, 1, 'Hip Hop'),
(5, 1, 'Indie'),
(6, 1, 'Jazz'),
(7, 1, 'Pop'),
(8, 1, 'Rock'),
(9, 1, 'Rhythm and Blues'),
(10, 1, 'Soul');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_ratings`
--

DROP TABLE IF EXISTS `engine4_music_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_music_ratings` (
  `playlist_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`playlist_id`,`user_id`),
  KEY `INDEX` (`playlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_music_playlists` ADD `rating` FLOAT NOT NULL;
