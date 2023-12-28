INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('poll_admin_main_categories', 'poll', 'Categories', '', '{"route":"admin_default","module":"poll","controller":"settings", "action":"categories"}', 'poll_admin_main', '', 4);

ALTER TABLE `engine4_poll_polls` ADD `category_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';

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
