
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: my.sql 10111 2013-10-31 05:05:49Z andres $
 * @author	   John
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_travels`
--

DROP TABLE IF EXISTS `engine4_travel_travels`;
CREATE TABLE `engine4_travel_travels` (
  `travel_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`travel_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_albums`
--

DROP TABLE IF EXISTS `engine4_travel_albums`;
CREATE TABLE `engine4_travel_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `travel_id` int(11) unsigned NOT NULL,
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
  KEY `travel_id` (`travel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_photos`
--

DROP TABLE IF EXISTS `engine4_travel_photos`;
CREATE TABLE `engine4_travel_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `travel_id` int(11) unsigned NOT NULL,
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
  KEY `travel_id` (`travel_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_categories`
--

DROP TABLE IF EXISTS `engine4_travel_categories`;
CREATE TABLE `engine4_travel_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_travel_categories`
--

INSERT IGNORE INTO `engine4_travel_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Cruises'),
(2, 1, 'Lodging'),
(3, 1, 'Packages & Tours'),
(4, 1, 'Transportation'),
(5, 1, 'Other');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_fields_maps`
--

DROP TABLE IF EXISTS `engine4_travel_fields_maps`;
CREATE TABLE `engine4_travel_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_travel_fields_maps`
--

INSERT IGNORE INTO `engine4_travel_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 2, 2),
(0, 0, 3, 3),
(0, 0, 4, 4),
(0, 0, 5, 5),
(0, 0, 6, 6)
;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_fields_meta`
--

DROP TABLE IF EXISTS `engine4_travel_fields_meta`;
CREATE TABLE `engine4_travel_fields_meta` (
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
-- Dumping data for table `engine4_travel_fields_fields`
--

INSERT IGNORE INTO `engine4_travel_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`, `icon`) VALUES
(2, 'currency', 'Price', '', 'price', 0, 1, 1, 1, 999, '{\"unit\":\"USD\"}', NULL, NULL, NULL, NULL, NULL),
(3, 'location', 'Location', '', 'location', 0, 1, 1, 1, 999, '', NULL, NULL, NULL, NULL, NULL),
(4, 'heading', 'Lodging', '', '', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', NULL, NULL),
(5, 'text', 'Bedrooms', '', '', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', '', ''),
(6, 'text', 'Bathrooms', '', '', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', '', '');

--
-- Table structure for table `engine4_travel_fields_options`
--

DROP TABLE IF EXISTS `engine4_travel_fields_options`;
CREATE TABLE `engine4_travel_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_fields_values`
--

DROP TABLE IF EXISTS `engine4_travel_fields_values`;
CREATE TABLE `engine4_travel_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_travel_fields_search`
--

DROP TABLE IF EXISTS `engine4_travel_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_travel_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double NULL,
  `location` varchar(255) NULL,
  `field_5` varchar(255) DEFAULT NULL,
  `field_6` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`),
  KEY `field_5` (`field_5`),
  KEY `field_6` (`field_6`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Travel Privacy', 'travel_maintenance_rebuild_privacy', 'travel', 'Travel_Plugin_Job_Maintenance_RebuildPrivacy', 50);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('travel_main', 'standard', 'Travel Main Navigation Menu'),
('travel_quick', 'standard', 'Travel Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_travel', 'travel', 'Travel', '', '{"route":"travel_general","icon":"fas fa-suitcase"}', 'core_main', '', 4),
('core_sitemap_travel', 'travel', 'Travel', '', '{"route":"travel_general"}', 'core_sitemap', '', 4),

('travel_main_browse', 'travel', 'Browse Listings', 'Travel_Plugin_Menus::canViewTravels', '{"route":"travel_general","icon":"fa fa-search"}', 'travel_main', '', 1),
('travel_main_manage', 'travel', 'My Listings', 'Travel_Plugin_Menus::canCreateTravels', '{"route":"travel_general","action":"manage","icon":"fa fa-user"}', 'travel_main', '', 2),
('travel_main_create', 'travel', 'Post a New Listing', 'Travel_Plugin_Menus::canCreateTravels', '{"route":"travel_general","action":"create","icon":"fa fa-plus"}', 'travel_main', '', 3),

('travel_quick_create', 'travel', 'Post a New Listing', 'Travel_Plugin_Menus::canCreateTravels', '{"route":"travel_general","action":"create","class":"buttonlink icon_travel_new"}', 'travel_quick', '', 1),

('core_admin_main_plugins_travel', 'travel', 'Travel', '', '{"route":"admin_default","module":"travel","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('travel_admin_main_manage', 'travel', 'View Travel', '', '{"route":"admin_default","module":"travel","controller":"manage"}', 'travel_admin_main', '', 1),
('travel_admin_main_settings', 'travel', 'Global Settings', '', '{"route":"admin_default","module":"travel","controller":"settings"}', 'travel_admin_main', '', 2),
('travel_admin_main_level', 'travel', 'Member Level Settings', '', '{"route":"admin_default","module":"travel","controller":"level"}', 'travel_admin_main', '', 3),
('travel_admin_main_fields', 'travel', 'Travel Questions', '', '{"route":"admin_default","module":"travel","controller":"fields"}', 'travel_admin_main', '', 4),
('travel_admin_main_categories', 'travel', 'Categories', '', '{"route":"admin_default","module":"travel","controller":"settings","action":"categories"}', 'travel_admin_main', '', 5),
('authorization_admin_level_travel', 'travel', 'Travel', '', '{"route":"admin_default","module":"travel","controller":"level","action":"index"}', 'authorization_admin_level', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('travel', 'Travel', 'Travel', '4.8.11', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('travel.currency', '$');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('travel_new', 'travel', '{item:$subject} posted a new travel listing:', 1, 5, 1, 3, 1, 1),
('comment_travel', 'travel', '{item:$subject} commented on {item:$owner}''s {item:$object:travel listing}.', 1, 1, 1, 3, 3, 0),
('like_travel', 'travel', '{item:$subject} liked {item:$owner}''s {item:$object:travel listing}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'css' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'css' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'travel' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
-- indexing for table `engine4_travel_travels`
--
ALTER TABLE `engine4_travel_travels` ADD INDEX(`modified_date`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`creation_date`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`like_count`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`comment_count`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`view_count`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`view_privacy`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`closed`);

ALTER TABLE `engine4_travel_travels` ADD INDEX(`category_id`);

ALTER TABLE `engine4_travel_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_travels` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_travels` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

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


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("travel_rating", "travel", '{item:$subject} has rated your travel {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_travel_rating", "travel", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
