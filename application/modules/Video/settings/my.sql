
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my.sql 10269 2014-06-20 19:53:00Z mfeineman $
 * @author		 John
 */

-- --------------------------------------------------------

--
-- Table structure for table `engine4_video_categories`
--

DROP TABLE IF EXISTS `engine4_video_categories`;
CREATE TABLE IF NOT EXISTS `engine4_video_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_video_categories`
--

INSERT INTO `engine4_video_categories` (`user_id`, `category_name`) VALUES
(0, 'Autos & Vehicles'),
(0, 'Comedy'),
(0, 'Education'),
(0, 'Entertainment'),
(0, 'Film & Animation'),
(0, 'Gaming'),
(0, 'Howto & Style'),
(0, 'Music'),
(0, 'News & Politics'),
(0, 'Nonprofits & Activism'),
(0, 'People & Blogs'),
(0, 'Pets & Animals'),
(0, 'Science & Technology'),
(0, 'Sports'),
(0, 'Travel & Events');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_video_ratings`
--

DROP TABLE IF EXISTS `engine4_video_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_video_ratings` (
  `video_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`video_id`,`user_id`),
  KEY `INDEX` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_video_videos`
--

DROP TABLE IF EXISTS `engine4_video_videos`;
CREATE TABLE IF NOT EXISTS `engine4_video_videos` (
  `video_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL,
  `parent_id` int(11) unsigned default NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `code` text COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) unsigned default NULL,
  `rating` FLOAT NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) NOT NULL,
  `file_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `duration` int(9) unsigned NOT NULL,
  `rotation` smallint unsigned NOT NULL DEFAULT '0',
  `view_privacy` VARCHAR(24) NOT NULL default 'everyone',
  `networks` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`video_id`),
  KEY `owner_id` (`owner_id`,`owner_type`),
  KEY `search` (`search`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`view_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `enabled`, `multi`, `priority`) VALUES
('Video Encode', 'video_encode', 'video', 'Video_Plugin_Job_Encode', 1, 2, 75),
('Rebuild Video Privacy', 'video_maintenance_rebuild_privacy', 'video', 'Video_Plugin_Job_Maintenance_RebuildPrivacy', 1, 1, 50);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_video_processed', 'video', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_video_processed_failed', 'video', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('video_main', 'standard', 'Video Main Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_video', 'video', 'Videos', '', '{"route":"video_general","icon":"fa fa-video"}', 'core_main', '', 7),
('core_sitemap_video', 'video', 'Videos', '', '{"route":"video_general"}', 'core_sitemap', '', 7),
('core_admin_main_plugins_video', 'video', 'Videos', '', '{"route":"admin_default","module":"video","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('video_main_browse', 'video', 'Browse Videos', '', '{"route":"video_general","icon":"fa fa-search"}', 'video_main', '', 1),
('video_main_manage', 'video', 'My Videos', 'Video_Plugin_Menus', '{"route":"video_general","action":"manage","icon":"fa fa-user"}', 'video_main', '', 2),
('video_main_create', 'video', 'Post New Video', 'Video_Plugin_Menus', '{"route":"video_general","action":"create","icon":"fa fa-plus"}', 'video_main', '', 3),

('video_quick_create', 'video', 'Post New Video', 'Video_Plugin_Menus::canCreateVideos', '{"route":"video_general","action":"create","class":"buttonlink icon_video_new"}', 'video_quick', '', 1),

('video_admin_main_manage', 'video', 'Manage Videos', '', '{"route":"admin_default","module":"video","controller":"manage"}', 'video_admin_main', '', 1),
('video_admin_main_utility', 'video', 'Video Utilities', '', '{"route":"admin_default","module":"video","controller":"settings","action":"utility"}', 'video_admin_main', '', 2),
('video_admin_main_settings', 'video', 'Global Settings', '', '{"route":"admin_default","module":"video","controller":"settings"}', 'video_admin_main', '', 3),
('video_admin_main_level', 'video', 'Member Level Settings', '', '{"route":"admin_default","module":"video","controller":"settings","action":"level"}', 'video_admin_main', '', 4),
('video_admin_main_categories', 'video', 'Categories', '', '{"route":"admin_default","module":"video","controller":"settings","action":"categories"}', 'video_admin_main', '', 5),

('authorization_admin_level_video', 'video', 'Videos', '', '{"route":"admin_default","module":"video","controller":"settings","action":"level"}', 'authorization_admin_level', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('video', 'Videos', 'Videos', '4.8.13', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('video.ffmpeg.path', ''),
('video.jobs', 2),
('video.embeds', 1);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`,  `body`,  `enabled`,  `displayable`,  `attachable`,  `commentable`,  `shareable`, `is_generated`) VALUES
('video_new', 'video', '{item:$subject} posted a new video:', '1', '5', '1', '3', '1', 0),
('comment_video', 'video', '{item:$subject} commented on {item:$owner}''s {item:$object:video}.', 1, 1, 1, 3, 3, 0),
('like_video', 'video', '{item:$subject} liked {item:$owner}''s {item:$object:video}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('video_processed', 'video', 'Your {item:$object:video} is ready to be viewed.', 0, ''),
('video_processed_failed', 'video', 'Your {item:$object:video} has failed to process.', 0, '');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'max' as `name`,
    3 as `value`,
    '20' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN
-- create, edit, delete, view, comment, upload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, edit, delete, view, comment, upload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
--
-- indexing for table `engine4_video_videos`
--
ALTER TABLE `engine4_video_videos` ADD INDEX(`parent_type`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`parent_id`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`like_count`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`type`);

ALTER TABLE `engine4_video_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_videos` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_videos` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("video_rating", "video", '{item:$subject} has rated your video {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_video_rating", "video", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
