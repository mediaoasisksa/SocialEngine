/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my.sql 10150 2014-03-27 15:59:48Z andres $
 * @author     John
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_poll_polls`
--

DROP TABLE IF EXISTS `engine4_poll_polls`;
CREATE TABLE IF NOT EXISTS `engine4_poll_polls` (
  `poll_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `is_closed` tinyint(1) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `vote_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) NOT NULL default '1',
  `closed` tinyint(1) NOT NULL default '0',
  `view_privacy` VARCHAR(24) NOT NULL default 'everyone',
  `networks` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`poll_id`),
  KEY `user_id` (`user_id`),
  KEY `is_closed` (`is_closed`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_poll_options`
--

DROP TABLE IF EXISTS `engine4_poll_options`;
CREATE TABLE IF NOT EXISTS `engine4_poll_options` (
  `poll_option_id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL,
  `poll_option` text NOT NULL,
  `votes` smallint(4) unsigned NOT NULL,
  PRIMARY KEY  (`poll_option_id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_poll_categories`
--

DROP TABLE IF EXISTS `engine4_poll_categories`;
CREATE TABLE `engine4_poll_categories` (
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
-- Dumping data for table `engine4_poll_categories`
--

INSERT IGNORE INTO `engine4_poll_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Celebrity'),
(2, 1, 'Historical'),
(3, 1, 'Humor'),
(5, 1, 'Personality'),
(6, 1, 'Political'),
(7, 1, 'Random'),
(8, 1, 'Sports'),
(9, 1, 'Travel');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_poll_votes`
--

DROP TABLE IF EXISTS `engine4_poll_votes`;
CREATE TABLE IF NOT EXISTS `engine4_poll_votes` (
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `poll_option_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  KEY `poll_option_id` (`poll_option_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Poll Privacy', 'poll_maintenance_rebuild_privacy', 'poll', 'Poll_Plugin_Job_Maintenance_RebuildPrivacy', 50);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('poll_main', 'standard', 'Poll Main Navigation Menu'),
('poll_quick', 'standard', 'Poll Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_poll', 'poll', 'Polls', '', '{"route":"poll_general","action":"browse","icon":"fa fa-chart-bar"}', 'core_main', '', 5),
('core_sitemap_poll', 'poll', 'Polls', '', '{"route":"poll_general","action":"browse"}', 'core_sitemap', '', 5),

('poll_main_browse', 'poll', 'Browse Polls', 'Poll_Plugin_Menus::canViewPolls', '{"route":"poll_general","action":"browse","icon":"fa fa-search"}', 'poll_main', '', 1),
('poll_main_manage', 'poll', 'My Polls', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"manage","icon":"fa fa-user"}', 'poll_main', '', 2),
('poll_main_create', 'poll', 'Create New Poll', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"create","icon":"fa fa-plus"}', 'poll_main', '', 3),

('poll_quick_create', 'poll', 'Create New Poll', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"create","class":"buttonlink icon_poll_new"}', 'poll_quick', '', 1),

('core_admin_main_plugins_poll', 'poll', 'Polls', '', '{"route":"admin_default","module":"poll","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('poll_admin_main_manage', 'poll', 'Manage Polls', '', '{"route":"admin_default","module":"poll","controller":"manage"}', 'poll_admin_main', '', 1),
('poll_admin_main_settings', 'poll', 'Global Settings', '', '{"route":"admin_default","module":"poll","controller":"settings"}', 'poll_admin_main', '', 2),
('poll_admin_main_level', 'poll', 'Member Level Settings', '', '{"route":"admin_default","module":"poll","controller":"settings","action":"level"}', 'poll_admin_main', '', 3),
('poll_admin_main_categories', 'poll', 'Categories', '', '{"route":"admin_default","module":"poll","controller":"settings", "action":"categories"}', 'poll_admin_main', '', 4),
('authorization_admin_level_poll', 'poll', 'Polls', '', '{"route":"admin_default","module":"poll","controller":"settings","action":"level"}', 'authorization_admin_level', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('poll', 'Polls', 'Polls', '4.8.11', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name` , `value`) VALUES
('poll.maxoptions', '15'),
('poll.showpiechart', '0'),
('poll.canchangevote', '1');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`,  `body`,  `enabled`,  `displayable`,  `attachable`,  `commentable`,  `shareable`, `is_generated`) VALUES
('poll_new', 'poll', '{item:$subject} created a new poll:', '1', '5', '1', '3', '1', 1),
('comment_poll', 'poll', '{item:$subject} commented on {item:$owner}''s {item:$object:poll}.', 1, 1, 1, 3, 3, 1),
('like_poll', 'poll', '{item:$subject} liked {item:$owner}''s {item:$object:poll}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'vote' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'vote' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
-- indexing for table `engine4_poll_polls`
--

ALTER TABLE `engine4_poll_polls` ADD INDEX(`view_privacy`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`view_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`like_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`vote_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`search`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`closed`);

ALTER TABLE `engine4_poll_polls` ADD `parent_type` VARCHAR(64) NOT NULL AFTER `user_id`, ADD `parent_id` INT(11) UNSIGNED NOT NULL AFTER `parent_type`;
ALTER TABLE `engine4_poll_polls` ADD INDEX( `parent_type`, `parent_id`); 



-- INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `editable`, `is_generated`) VALUES
-- ('poll_cover_photo_update', 'poll', '{item:$subject} has updated {item:$object} cover photo.', 1, 5, 1, 4, 1, 0, 1);

ALTER TABLE `engine4_poll_polls` ADD `coverphoto` INT ( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD INDEX(`coverphoto`);

-- coverphotoupload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'coverphotoupload' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

DROP TABLE IF EXISTS `engine4_poll_albums` ;
CREATE TABLE `engine4_poll_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  `coverphotoparams` VARCHAR ( 265 ) NULL,
  `type` VARCHAR ( 265 ) NULL,
   PRIMARY KEY (`album_id`),
   KEY (`poll_id`),
   KEY (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


DROP TABLE IF EXISTS `engine4_poll_photos`;
CREATE TABLE `engine4_poll_photos` (
  `photo_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`photo_id`),
  KEY (`album_id`),
  KEY (`poll_id`),
  KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_poll_ratings`
--

DROP TABLE IF EXISTS `engine4_poll_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_poll_ratings` (
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`poll_id`,`user_id`),
  KEY `INDEX` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_poll_polls` ADD `rating` FLOAT NOT NULL;
ALTER TABLE `engine4_poll_polls` ADD `category_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("poll_rating", "poll", '{item:$subject} has rated your poll {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_poll_rating", "poll", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
