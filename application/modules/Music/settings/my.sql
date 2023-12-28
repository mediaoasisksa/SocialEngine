
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my.sql 10267 2014-06-10 00:55:28Z lucas $
 * @author     Steve
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_playlists`
--

DROP TABLE IF EXISTS `engine4_music_playlists`;
CREATE TABLE IF NOT EXISTS `engine4_music_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `owner_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `profile` tinyint(1) NOT NULL default '0',
  `special` enum('wall', 'message') default NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `play_count` int(11) unsigned NOT NULL default '0',
  `view_privacy` VARCHAR(24) NOT NULL default 'everyone',
  `networks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`playlist_id`),
  KEY `creation_date` (`creation_date`),
  KEY `play_count` (`play_count`),
  KEY `owner_id` (`owner_type`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_playlist_songs`
--

DROP TABLE IF EXISTS `engine4_music_playlist_songs`;
CREATE TABLE IF NOT EXISTS `engine4_music_playlist_songs` (
  `song_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `play_count` int(11) unsigned NOT NULL default '0',
  `order` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`song_id`),
  KEY (`playlist_id`,`file_id`),
  KEY `play_count` (`play_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Music Privacy', 'music_maintenance_rebuild_privacy', 'music', 'Music_Plugin_Job_Maintenance_RebuildPrivacy', 50);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('music_main', 'standard', 'Music Main Navigation Menu'),
('music_quick', 'standard', 'Music Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_music', 'music', 'Music', '', '{"route":"music_general","action":"browse","icon":"fa fa-music"}', 'core_main', '', 100),
('core_sitemap_music', 'music', 'Music', '', '{"route":"music_general","action":"browse"}', 'core_sitemap', '', 100),

('music_main_browse', 'music', 'Browse Music', 'Music_Plugin_Menus::canViewPlaylists', '{"route":"music_general","action":"browse","icon":"fa fa-search"}', 'music_main', '', 1),
('music_main_manage', 'music', 'My Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"manage","icon":"fa fa-user"}', 'music_main', '', 2),
('music_main_create', 'music', 'Upload Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"create","icon":"fa fa-upload"}', 'music_main', '', 3),

('music_quick_create', 'music', 'Upload Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"create","class":"buttonlink icon_music_new"}', 'music_quick', '', 1),

('core_admin_main_plugins_music', 'music', 'Music', '', '{"route":"admin_default","module":"music","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('music_admin_main_manage', 'music', 'Manage Music', '', '{"route":"admin_default","module":"music","controller":"manage"}', 'music_admin_main', '', 1),
('music_admin_main_settings', 'music', 'Global Settings', '', '{"route":"admin_default","module":"music","controller":"settings"}', 'music_admin_main', '', 2),
('music_admin_main_level', 'music', 'Member Level Settings', '', '{"route":"admin_default","module":"music","controller":"level"}', 'music_admin_main', '', 3),
('music_admin_main_categories', 'music', 'Categories', '', '{"route":"admin_default","module":"music","controller":"settings", "action":"categories"}', 'music_admin_main', '', 4),
('authorization_admin_level_music', 'music', 'Music', '', '{"route":"admin_default","module":"music","controller":"level","action":"index"}', 'authorization_admin_level', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('music', 'Music', 'Music', '4.8.9', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('music.playlistsperpage', 10);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_tasks`
--

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Music Cleanup', 'music', 'Music_Plugin_Task_Cleanup',	43200);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('music_playlist_new', 'music', '{item:$subject} created a new playlist: {item:$object}', '1', '5', '1', '3', '1', 1),
('comment_music_playlist',   'music', '{item:$subject} commented on {item:$owner}''s {item:$object:playlist}.', 1, 1, 1, 3, 3, 1),
('like_music_playlist', 'music', '{item:$subject} liked {item:$owner}''s {item:$object:playlist}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
-- indexing for table `engine4_music_playlists`
--
ALTER TABLE `engine4_music_playlists` ADD INDEX(`search`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`profile`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`special`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`modified_date`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`view_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`like_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`view_privacy`);

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
ALTER TABLE `engine4_music_playlists` ADD `category_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_music_playlists` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_music_playlists` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("music_rating", "music", '{item:$subject} has rated your music {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_music_rating", "music", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
