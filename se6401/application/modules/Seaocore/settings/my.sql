/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.seaocores.com/license/
 * @version    $Id: my.sql 2010-11-18 9:40:21Z Seaocores $
 * @author     Seaocores
 */

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_plugins_Seaocore', 'seaocore', 'SEAO - SocialApps.tech Basic Plugin', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 0),
('seaocore_admin_main_infotooltip', 'seaocore', 'Info Tooltip Settings', 'Seaocore_Plugin_Menus',
'{"route":"admin_default","module":"seaocore","controller":"infotooltip"}', 'seaocore_admin_main', '', 3),
('seaocore_admin_plugin_keys', 'seaocore', 'Configuration of Keys', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"save-keys"}', 'seaocore_admin_main', NULL, '15');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('seaocore.display.lightbox',1),
('seaocore.lightbox.option.display',''),
('seaocore.tag.type', 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('seaocore_admin_main_lightbox', 'seaocore', 'Photos Lightbox Viewer', 'Seaocore_Plugin_Menus', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"lightbox"}', 'seaocore_admin_main', '', 1, 0, 4),
( 'seaocore_admin_helpInvite', 'seaocore', 'Invite Services', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings","action":"help-invite"}', 'seaocore_admin_main', NULL, '1', '0', '6'),
( 'seaocore_admin_map', 'seaocore', 'Locations & Maps', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings","action":"map"}', 'seaocore_admin_main', NULL, '1', '0', '7'),
( 'seaocore_admin_settings', 'seaocore', 'General Settings', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings"}', 'seaocore_admin_main', NULL, '1', '0', '8');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.tooltip.bgcolor', '#FFFFFF');

-- --------------------------------------------------------

UPDATE  `engine4_core_menuitems` SET  `label` =  'SocialEngineAddOns-Old Version' WHERE  `engine4_core_menuitems`.`name` =  'core_admin_plugins_Socialengineaddon';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_locationitems`
--

DROP TABLE IF EXISTS `engine4_seaocore_locationitems`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_locationitems` (
  `locationitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `location` text COLLATE utf8_unicode_ci,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `formatted_address` text COLLATE utf8_unicode_ci,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zoom` int(11) NOT NULL,
  PRIMARY KEY (`locationitem_id`),
  UNIQUE KEY `resource_id` (`resource_id`,`resource_type`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------


/* This query was removed for changes in 4.2.8 */

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
VALUES ('share', 'activity', '{item:$subject} shared {item:$object}''s {var:$type}. {body:body:$body}', 1, 5, 1, 1, 0, 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)
VALUES ('shared', 'activity', '{item:$subject} has shared your {item:$object:$label}.', 0, '', 1);

DROP TABLE IF EXISTS `engine4_seaocore_locationcontents`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_locationcontents` (
  `locationcontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,   
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `formatted_address` text COLLATE utf8_unicode_ci,
  `country` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`locationcontent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_invites`
--

DROP TABLE IF EXISTS `engine4_seaocore_invites`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_invites` (
  `invite_id` int(11) unsigned NOT NULL auto_increment,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `creation_time` DATETIME NOT NULL,
  `recipient_id` int(11) unsigned NOT NULL,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `inviter_id` int(11) NOT NULL,
  `occurrence_id` int(11) NOT NULL,
  PRIMARY KEY  (`invite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES ('SEAO - Background Inviter', 'seaocore', 'Seaocore_Plugin_Task_Invite', 15);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_tabs`
--

DROP TABLE IF EXISTS `engine4_seaocore_tabs`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_tabs` (
  `tab_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `order` int(3) NOT NULL DEFAULT '0',
  `limit` int(3) NOT NULL,
  `show` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tab_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_searchformsetting`
--

DROP TABLE IF EXISTS `engine4_seaocore_searchformsetting`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_searchformsetting` (
  `searchformsetting_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL DEFAULT '0',
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`searchformsetting_id`),
  UNIQUE KEY `PLUGIN_NAME` (`module`,`name`),
  KEY  `module` (`module`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocores`
--
DROP TABLE IF EXISTS `engine4_seaocores`;
CREATE TABLE IF NOT EXISTS `engine4_seaocores` (
  `seaocores_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `version` varchar(32) NOT NULL,
  `is_installed` tinyint(1) NOT NULL,
  `category` varchar(64) NOT NULL,
  `ptype` varchar(20) NOT NULL,
  `is_activate` int(11) NOT NULL DEFAULT '0',
  `enviroment` varchar(50) NOT NULL DEFAULT 'development',
  PRIMARY KEY (`seaocores_id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_locations`
--
DROP TABLE IF EXISTS `engine4_seaocore_locations`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `formatted_address` text,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zoom` int(11) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_bannedpageurls`
--
DROP TABLE IF EXISTS `engine4_seaocore_bannedpageurls`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_bannedpageurls` (
  `bannedpageurl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bannedpageurl_id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES
  ('sitestaticpage'),('static'),('music'),('polls'),('blogs'),('videos'),	('classifieds'),('albums'),('events'),	('groups'),('group'),
  ('forums'),('invite'),('recipeitems'),('ads'),	('likes'),('documents'),('sitepage'),
  ('sitepagepoll'),('sitepageoffer'),('sitepagevideo'),('sitepagedocument'),('sitepagenote'),
  ('sitepageevent'),('sitepagemusic'),('sitepageinvite'),('sitepagereview'),('sitepagebadge'),
  ('sitepageform'),('sitepagealbum'),('sitepagediscussion'),('sitebusiness'),
  ('sitebusinesspoll'),('sitebusinessoffer'),('sitebusinessvideo'),('sitebusinessdocument'),('sitebusinessnote'),
  ('sitebusinessevent'),('sitebusinessmusic'),('sitebusinessinvite'),('sitebusinessreview'),('sitebusinessbadge'),
  ('sitebusinessform'),('sitebusinessalbum'),('sitebusinessdiscussion'),('sitegroup'),
  ('sitegrouppoll'),('sitegroupoffer'),('sitegroupvideo'),('sitegroupdocument'),('sitegroupnote'),
  ('sitegroupevent'),('sitegroupmusic'),('sitegroupinvite'),('sitegroupreview'),('sitegroupbadge'),
  ('sitegroupform'),('sitegroupalbum'),('sitegroupdiscussion'),('sitestore'),
  ('sitestorepoll'),('sitestoreoffer'),('sitestorevideo'),('sitestoredocument'),('sitestorenote'),
  ('sitestoreevent'),('sitestoremusic'),('sitestoreinvite'),('sitestorereview'),('sitestorebadge'),
  ('sitestoreform'),('sitestorealbum'),('sitestorediscussion'),('recipe'),('sitelike'),('suggestion'),('advanceslideshow'),('feedback'),('grouppoll'),('groupdocumnet'),('sitealbum'),('siteslideshow'),('userconnection'),('communityad'),('list'),('article'),
  ('listing'),('store'),('page-videos'),('pageitem'),('pageitems'),('page-events'),('page-documents'),('page-offers'),('page-notes'),('page-invites'),('page-form'),('page-music'),
  ('page-reviews'),('businessitem'),('businessitems'),('business-events'),('business-documents'),('business-offers'),('business-notes'),('business-invites'),('business-form'),('business-music'),
  ('business-reviews'),('group-videos'),('groupitem'),('groupitems'),('group-events'),('group-documents'),('group-offers'),('group-notes'),('group-invites'),('group-form'),('group-music'),('group-reviews'),('store-videos'),('storeitem'),('storeitems'),('store-events'),
  ('store-documents'),('store-offers'),('store-notes'),('store-invites'),('store-form'),('store-music'),('store-reviews'),('listingitems'),('market'),('document'),('pdf'),('pokes'),('facebook'),('album'),('photo'),('files'),('file'),('page'),
  ('store'),('backup'),('question'),('answer'),('questions'),('answers'),('newsfeed'),('birthday'),('wall'),('profiletype'),('memberlevel'),('members'),('member'),('memberlevel'),
  ('level'),('slideshow'),('seo'),('xml'),('cmspages'),('favoritepages'),('help'),('rss'),
  ('stories'),('story'),('visits'),('points'),('vote'),('advanced'),('listingitem');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_userinfo`
--
DROP TABLE IF EXISTS `engine4_seaocore_userinfo`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_userinfo` (
  `userinfo_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) NOT NULL,
  `rating_avg` float NOT NULL,
  `rating_users` float NOT NULL,
  `review_count` int(11) NOT NULL,
  PRIMARY KEY (`userinfo_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;  


DROP TABLE IF EXISTS `engine4_seaocore_notifications`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_notifications` (
  `notification_id` int(11) NOT NULL,
  `show` TINYINT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
