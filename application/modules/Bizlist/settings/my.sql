/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my.sql 10111 2013-10-31 05:05:49Z andres $
 * @author	   John
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_bizlists`
--

DROP TABLE IF EXISTS `engine4_bizlist_bizlists`;
CREATE TABLE `engine4_bizlist_bizlists` (
  `bizlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`bizlist_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_albums`
--

DROP TABLE IF EXISTS `engine4_bizlist_albums`;
CREATE TABLE `engine4_bizlist_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bizlist_id` int(11) unsigned NOT NULL,
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
  KEY `bizlist_id` (`bizlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_photos`
--

DROP TABLE IF EXISTS `engine4_bizlist_photos`;
CREATE TABLE `engine4_bizlist_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `bizlist_id` int(11) unsigned NOT NULL,
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
  KEY `bizlist_id` (`bizlist_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_categories`
--

DROP TABLE IF EXISTS `engine4_bizlist_categories`;
CREATE TABLE `engine4_bizlist_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_bizlist_categories`
--

INSERT IGNORE INTO `engine4_bizlist_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Automotive'),
(2, 1, 'Beauty & Spas'),
(3, 1, 'Financial'),
(5, 1, 'Food'),
(6, 1, 'Health & Medical'),
(7, 1, 'Home Services'),
(8, 1, 'Pets'),
(9, 1, 'Professional Services'),
(10, 1, 'Shopping'),
(11, 1, 'Sports'),
(12, 1, 'Technology'),
(13, 1, 'Other');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_fields_maps`
--

DROP TABLE IF EXISTS `engine4_bizlist_fields_maps`;
CREATE TABLE `engine4_bizlist_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_bizlist_fields_maps`
--

INSERT IGNORE INTO `engine4_bizlist_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 3, 1),
(0, 0, 4, 3),
(0, 0, 5, 2),
(0, 0, 6, 4),
(0, 0, 7, 6),
(0, 0, 8, 5),
(0, 0, 9, 7);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_fields_meta`
--

DROP TABLE IF EXISTS `engine4_bizlist_fields_meta`;
CREATE TABLE `engine4_bizlist_fields_meta` (
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
-- Dumping data for table `engine4_bizlist_fields_fields`
--

INSERT INTO `engine4_bizlist_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`, `icon`) VALUES
(3, 'location', 'Location', '', 'location', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-map-marker'),
(4, 'text', 'Hours of Operation', '', '', 0, 1, 0, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-clock'),
(5, 'website', 'Website', '', 'website', 0, 1, 0, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-globe'),
(6, 'text', 'Phone', '', '', 0, 1, 0, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-phone'),
(7, 'textarea', 'Services Offered', '', '', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-hands-helping'),
(8, 'heading', 'Business Details', '', '', 0, 1, 0, 1, 999, '[]', NULL, NULL, '', NULL, NULL),
(9, 'textarea', 'Amenities', '', '', 0, 1, 1, 1, 999, '[]', NULL, NULL, '', '', 'fas fa-concierge-bell');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_fields_options`
--

DROP TABLE IF EXISTS `engine4_bizlist_fields_options`;
CREATE TABLE `engine4_bizlist_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_fields_values`
--

DROP TABLE IF EXISTS `engine4_bizlist_fields_values`;
CREATE TABLE `engine4_bizlist_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_bizlist_fields_search`
--

DROP TABLE IF EXISTS `engine4_bizlist_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_bizlist_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double NULL,
  `location` varchar(255) NULL,
  PRIMARY KEY  (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_jobtypes`
--

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Business Privacy', 'bizlist_maintenance_rebuild_privacy', 'bizlist', 'Bizlist_Plugin_Job_Maintenance_RebuildPrivacy', 50);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('bizlist_main', 'standard', 'Business Main Navigation Menu'),
('bizlist_quick', 'standard', 'Business Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_bizlist', 'bizlist', 'Businesses', '', '{"route":"bizlist_general","icon":"fas fa-briefcase"}', 'core_main', '', 4),
('core_sitemap_bizlist', 'bizlist', 'Businesses', '', '{"route":"bizlist_general"}', 'core_sitemap', '', 4),

('bizlist_main_browse', 'bizlist', 'Browse Listings', 'Bizlist_Plugin_Menus::canViewBizlists', '{"route":"bizlist_general","icon":"fa fa-search"}', 'bizlist_main', '', 1),
('bizlist_main_manage', 'bizlist', 'My Listings', 'Bizlist_Plugin_Menus::canCreateBizlists', '{"route":"bizlist_general","action":"manage","icon":"fa fa-user"}', 'bizlist_main', '', 2),
('bizlist_main_create', 'bizlist', 'Post a New Listing', 'Bizlist_Plugin_Menus::canCreateBizlists', '{"route":"bizlist_general","action":"create","icon":"fa fa-plus"}', 'bizlist_main', '', 3),

('bizlist_quick_create', 'bizlist', 'Post a New Listing', 'Bizlist_Plugin_Menus::canCreateBizlists', '{"route":"bizlist_general","action":"create","class":"buttonlink icon_bizlist_new"}', 'bizlist_quick', '', 1),

('core_admin_main_plugins_bizlist', 'bizlist', 'Businesses', '', '{"route":"admin_default","module":"bizlist","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('bizlist_admin_main_manage', 'bizlist', 'View Businesses', '', '{"route":"admin_default","module":"bizlist","controller":"manage"}', 'bizlist_admin_main', '', 1),
('bizlist_admin_main_settings', 'bizlist', 'Global Settings', '', '{"route":"admin_default","module":"bizlist","controller":"settings"}', 'bizlist_admin_main', '', 2),
('bizlist_admin_main_level', 'bizlist', 'Member Level Settings', '', '{"route":"admin_default","module":"bizlist","controller":"level"}', 'bizlist_admin_main', '', 3),
('bizlist_admin_main_fields', 'bizlist', 'Business Questions', '', '{"route":"admin_default","module":"bizlist","controller":"fields"}', 'bizlist_admin_main', '', 4),
('bizlist_admin_main_categories', 'bizlist', 'Categories', '', '{"route":"admin_default","module":"bizlist","controller":"settings","action":"categories"}', 'bizlist_admin_main', '', 5),

('authorization_admin_level_bizlist', 'bizlist', 'Businesses', '', '{"route":"admin_default","module":"bizlist","controller":"level","action":"index"}', 'authorization_admin_level', '', 999),
('mobi_browse_bizlist', 'bizlist', 'Businesses', '', '{"route":"bizlist_general"}', 'mobi_browse', '', 4);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('bizlist', 'Businesses', 'Businesses', '4.8.11', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('bizlist.currency', '$');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('bizlist_new', 'bizlist', '{item:$subject} posted a new business listing:', 1, 5, 1, 3, 1, 1),
('comment_bizlist', 'bizlist', '{item:$subject} commented on {item:$owner}''s {item:$object:bizlist listing}.', 1, 1, 1, 3, 3, 0),
('like_bizlist', 'bizlist', '{item:$subject} liked {item:$owner}''s {item:$object:bizlist listing}.', 1, 1, 1, 3, 3, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'css' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'css' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'bizlist' as `type`,
    'allow_network' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
-- indexing for table `engine4_bizlist_bizlists`
--
ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`modified_date`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`creation_date`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`like_count`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`comment_count`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`view_count`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`view_privacy`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`closed`);

ALTER TABLE `engine4_bizlist_bizlists` ADD INDEX(`category_id`);


ALTER TABLE `engine4_bizlist_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_bizlist_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_bizlist_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_bizlist_bizlists` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_bizlist_bizlists` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

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

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("bizlist_rating", "bizlist", '{item:$subject} has rated your business {item:$object}.', 0, "");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("notify_bizlist_rating", "bizlist", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");
