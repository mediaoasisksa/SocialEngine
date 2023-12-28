INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('customtheme', 'customtheme', 'customtheme', '4.0.0', 1, 'extra') ;

--
-- Table structure for table `engine4_core_banners`
--

DROP TABLE IF EXISTS `engine4_customtheme_banners`;
CREATE TABLE IF NOT EXISTS `engine4_customtheme_banners` (
  `banner_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
  `title` varchar(64) NULL,
  `body` varchar(255) NULL,
  `photo_id` int(11) unsigned NULL default '0',
  `params` text NULL,
  `custom` tinyint(1) NULL default '0',
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'core_admin_main_plugins_customtheme', 'customtheme', 'Banner Block', '', '{"route":"admin_default","module":"customtheme","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, 999),
('customtheme_admin_main_banners', 'customtheme', 'Banners', '', '{"route":"admin_default","module":"customtheme","controller":"banners", "action":"index"}', 'customtheme_admin_main', '', 1, 0, 3),
( 'customtheme_admin_main_settings', 'customtheme', 'Global Settings', '', '{"route":"admin_default","module":"customtheme","controller":"settings"}', 'customtheme_admin_main', '', 1, 0, 2);

--
-- Dumping data for table `engine4_customtheme_banners`
--

INSERT IGNORE INTO `engine4_customtheme_banners` (`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('custom_1', 'customtheme', NULL, NULL, 99997, '{"uri":"videos"}', 1),
('custom_2', 'customtheme', NULL, NULL, 99998, '{"uri":"http://www.mediaoasis.net/app/stores/products/browse-category/web-and-mobile-app/171"}', 1),
('custom_3', 'customtheme', NULL, NULL, 99999, '{"uri":"videos/create"}', 1);

--
-- Dumping data for table `engine4_storage_files`
--

INSERT IGNORE INTO `engine4_storage_files` (`file_id`, `parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `creation_date`, `modified_date`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
(99997, NULL, NULL, 'banner', 1, 1, '2019-12-01 08:45:02', '2019-12-01 08:45:02', 1, 'public/banner/e0/63/6b5a7627af4e7aefa9a8f00ed282c5f4.png', 'png', 'video-img_m.png', 'image', 'png', 67585, '3b916d25ab7356eb221be43aeaba9c14'),
(99998, NULL, NULL, 'banner', 2, 1, '2019-12-01 08:45:14', '2019-12-01 08:45:14', 1, 'public/banner/e1/63/fc57e693a462817965ff818ea40b4292.png', 'png', 'store-img_m.png', 'image', 'png', 28495, '230b08545d1e0183b79f3a966728943a'),
(99999, NULL, NULL, 'banner', 3, 1, '2019-12-01 08:45:28', '2019-12-01 08:45:28', 1, 'public/banner/e2/63/c09525b2c9b0155d635a3a56b7506ca1.png', 'png', 'ads-post_m.png', 'image', 'png', 11496, '44934582d3aa26324437cc5c1aa0fea9');
