
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my.sql 10111 2013-10-31 05:05:49Z andres $
 * @author	   John
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_employments`
--

DROP TABLE IF EXISTS `engine4_employment_employments`;
CREATE TABLE `engine4_employment_employments` (
  `employment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `body` longtext NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) NOT NULL default '1',
  `closed` tinyint(1) NOT NULL default '0',
  `view_privacy` VARCHAR(24) NOT NULL default 'everyone',
  `networks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`employment_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_albums`
--

DROP TABLE IF EXISTS `engine4_employment_albums`;
CREATE TABLE `engine4_employment_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `employment_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`album_id`),
  KEY `employment_id` (`employment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_photos`
--

DROP TABLE IF EXISTS `engine4_employment_photos`;
CREATE TABLE `engine4_employment_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `employment_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `like_count` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `employment_id` (`employment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_categories`
--

DROP TABLE IF EXISTS `engine4_employment_categories`;
CREATE TABLE `engine4_employment_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_employment_categories`
--

INSERT IGNORE INTO `engine4_employment_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Administrative'),
(2, 1, 'Banking & Finance'),
(3, 1, 'Computer & IT'),
(5, 1, 'Education'),
(6, 1, 'Healthcare'),
(7, 1, 'Marketing'),
(8, 1, 'Personal'),
(9, 1, 'Sales'),
(10, 1, 'Transportation'),
(11, 1, 'Travel'),
(12, 1, 'Volunteer'),
(13, 1, 'Web Developer');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_fields_maps`
--

DROP TABLE IF EXISTS `engine4_employment_fields_maps`;
CREATE TABLE `engine4_employment_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_employment_fields_maps`
--

INSERT IGNORE INTO `engine4_employment_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 2, 2),
(0, 0, 3, 3),
(0, 0, 4, 4)
;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_fields_meta`
--

DROP TABLE IF EXISTS `engine4_employment_fields_meta`;
CREATE TABLE `engine4_employment_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `show` tinyint(1) unsigned NOT NULL default '1',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,
  `icon` TEXT NULL DEFAULT NULL,
  /*`unit` varchar(32) COLLATE utf8_unicode_ci NOT NULL,*/

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_employment_fields_fields`
--

INSERT IGNORE INTO `engine4_employment_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`, `icon`) VALUES
(2, 'currency', 'Salary', '', 'salary', 0, 1, 1, 1, 999, '{\"unit\":\"USD\"}', NULL, NULL, NULL, NULL, NULL),
(3, 'location', 'Location', '', 'location', 0, 1, 1, 1, 999, '', NULL, NULL, NULL, NULL, NULL),
(4, 'select', 'Job Type', '', 'job', 0, 1, 1, 1, 999, '[]', NULL, NULL, NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_fields_options`
--

DROP TABLE IF EXISTS `engine4_employment_fields_options`;
CREATE TABLE `engine4_employment_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_employment_fields_options`
--

INSERT IGNORE INTO `engine4_employment_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 4, 'Full-time', 999),
(2, 4, 'Part-time', 999),
(3, 4, 'Contract', 999),
(4, 4, 'Temporary', 999),
(5, 4, 'Internship', 999);
-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_fields_values`
--

DROP TABLE IF EXISTS `engine4_employment_fields_values`;
CREATE TABLE `engine4_employment_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_fields_search`
--

DROP TABLE IF EXISTS `engine4_employment_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_employment_fields_search` (
  `item_id` int(11) NOT NULL,
  `salary` double NULL,
  `location` varchar(255) NULL,
  `currency` float DEFAULT NULL,
  `field_4` enum('1','2','3','4','5') DEFAULT NULL,
  PRIMARY KEY  (`item_id`),
  KEY `salary` (`salary`),
  KEY `location` (`location`),
  KEY `currency` (`currency`),
  KEY `field_4` (`field_4`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Employment Privacy', 'employment_maintenance_rebuild_privacy', 'employment', 'Employment_Plugin_Job_Maintenance_RebuildPrivacy', 50);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('employment_main', 'standard', 'Employment Main Navigation Menu'),
('employment_quick', 'standard', 'Employment Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_employment', 'employment', 'Employment', '', '{"route":"employment_general","icon":"fas fa-briefcase"}', 'core_main', '', 4),
('core_sitemap_employment', 'employment', 'Employment', '', '{"route":"employment_general"}', 'core_sitemap', '', 4),

('employment_main_browse', 'employment', 'Browse Listings', 'Employment_Plugin_Menus::canViewEmployments', '{"route":"employment_general","icon":"fa fa-search"}', 'employment_main', '', 1),
('employment_main_manage', 'employment', 'My Listings', 'Employment_Plugin_Menus::canCreateEmployments', '{"route":"employment_general","action":"manage","icon":"fa fa-user"}', 'employment_main', '', 2),
('employment_main_create', 'employment', 'Post a New Listing', 'Employment_Plugin_Menus::canCreateEmployments', '{"route":"employment_general","action":"create","icon":"fa fa-plus"}', 'employment_main', '', 3),

('employment_quick_create', 'employment', 'Post a New Listing', 'Employment_Plugin_Menus::canCreateEmployments', '{"route":"employment_general","action":"create","class":"buttonlink icon_employment_new"}', 'employment_quick', '', 1),

('core_admin_main_plugins_employment', 'employment', 'Employment', '', '{"route":"admin_default","module":"employment","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('employment_admin_main_manage', 'employment', 'View Employment Listings', '', '{"route":"admin_default","module":"employment","controller":"manage"}', 'employment_admin_main', '', 1),
('employment_admin_main_settings', 'employment', 'Global Settings', '', '{"route":"admin_default","module":"employment","controller":"settings"}', 'employment_admin_main', '', 2),
('employment_admin_main_level', 'employment', 'Member Level Settings', '', '{"route":"admin_default","module":"employment","controller":"level"}', 'employment_admin_main', '', 3),
('employment_admin_main_fields', 'employment', 'Employment Questions', '', '{"route":"admin_default","module":"employment","controller":"fields"}', 'employment_admin_main', '', 4),
('employment_admin_main_categories', 'employment', 'Categories', '', '{"route":"admin_default","module":"employment","controller":"settings","action":"categories"}', 'employment_admin_main', '', 5),

('authorization_admin_level_employment', 'employment', 'Employment', '', '{"route":"admin_default","module":"employment","controller":"level","action":"index"}', 'authorization_admin_level', '', 999)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('employment', 'Employment', 'Employment Listings', '4.8.11', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('employment.currency', '$');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('employment_new', 'employment', '{item:$subject} posted a new employment listing:', 1, 5, 1, 3, 1, 1),
('comment_employment', 'employment', '{item:$subject} commented on {item:$owner}''s {item:$object:employment listing}.', 1, 1, 1, 3, 3, 0),
('like_employment', 'employment', '{item:$subject} liked {item:$owner}''s {item:$object:employment listing}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'css' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'css' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'employment' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
-- indexing for table `engine4_employment_employments`
--
ALTER TABLE `engine4_employment_employments` ADD INDEX(`modified_date`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`creation_date`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`like_count`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`comment_count`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`view_count`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`view_privacy`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`closed`);

ALTER TABLE `engine4_employment_employments` ADD INDEX(`category_id`);


ALTER TABLE `engine4_employment_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_employment_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_employment_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_employment_employments` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_employment_employments` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';


-- --------------------------------------------------------

--
-- Table structure for table `engine4_employment_ratings`
--

DROP TABLE IF EXISTS `engine4_employment_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_employment_ratings` (
  `employment_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`employment_id`,`user_id`),
  KEY `INDEX` (`employment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `engine4_employment_employments` ADD `rating` FLOAT NOT NULL;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("employment_rating", "employment", '{item:$subject} has rated your employment {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_employment_rating", "employment", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
