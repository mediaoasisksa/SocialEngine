INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('sitecourse', 'Course Builder / Learning Management Plugin', 'Course Builder / Learning Management Plugin', '6.3.0', 1, 'extra');


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sitecourse', 'sitecourse', 'Course Builder / Learning Management Plugin', '', '{"route":"admin_default","module":"sitecourse","controller":"settings"}', 'core_admin_main_plugins', '', 999),

('sitecourse_admin_main_manage', 'sitecourse', 'Manage Courses', '', '{"route":"admin_default","module":"sitecourse","controller":"manage"}', 'sitecourse_admin_main', '', 4),
('sitecourse_admin_main_settings', 'sitecourse', 'Global Settings', '', '{"route":"admin_default","module":"sitecourse","controller":"settings"}', 'sitecourse_admin_main', '', 1),
('sitecourse_admin_main_level', 'sitecourse', 'Member Level Settings', '', '{"route":"admin_default","module":"sitecourse","controller":"level"}', 'sitecourse_admin_main', '', 2),
('sitecourse_admin_main_categories', 'sitecourse', 'Categories', '', '{"route":"admin_default","module":"sitecourse","controller":"settings", "action":"categories"}', 'sitecourse_admin_main', '', 3),
('sitecourse_admin_main_reviews', 'sitecourse', 'Manage Reviews', '', '{"route":"admin_default","module":"sitecourse","controller":"manage","action":"review"}', 'sitecourse_admin_main', '', 5),
('sitecourse_admin_main_transcations', 'sitecourse', 'Transactions', '', '{"route":"admin_default","module":"sitecourse","controller":"manage","action":"transactions"}', 'sitecourse_admin_main', '', 6),
('sitecourse_admin_main_reports', 'sitecourse', 'Reports', '', '{"route":"admin_default","module":"sitecourse","controller":"report","action":"index"}', 'sitecourse_admin_main', '', 7),
('sitecourse_admin_main_utilities', 'sitecourse', 'Video Utilities', '', '{"route":"admin_default","module":"sitecourse","controller":"settings","action":"utility"}', 'sitecourse_admin_main', '', 9),
('sitecourse_admin_main_faqs', 'sitecourse', 'FAQs', '', '{"route":"admin_default","module":"sitecourse","controller":"settings","action":"faq"}', 'sitecourse_admin_main', '', 10),
('sitecourse_admin_main_certificate', 'sitecourse', 'Certificate', '', '{"route":"admin_default","module":"sitecourse","controller":"settings","action":"certificate"}', 'sitecourse_admin_main', '', 8),

('sitecourse_main_browse', 'sitecourse', 'Browse Courses', '', '{"route":"sitecourse_general","icon":"fa fa-search"}', 'sitecourse_main', '', 1),
('sitecourse_main_manage', 'sitecourse', 'My Courses', 'Sitecourse_Plugin_Menus::canManageSitecourse', '{"route":"sitecourse_general","action":"manage","icon":"fa fa-user"}', 'sitecourse_main', '', 2),
('sitecourse_main_create', 'sitecourse', 'Create New Course', 'Sitecourse_Plugin_Menus::canCreateSitecourse', '{"route":"sitecourse_general","action":"create","icon":"fa fa-pencil-alt"}', 'sitecourse_main', '', 3),

('core_main_sitecourse', 'sitecourse', 'Courses', '', '{"route":"sitecourse_general","icon":"fa fa-pencil-alt"}', 'core_main', '', '4'),

('sitecourse_admin_main_general', 'sitecourse', 'General Settings', '', '{"route":"admin_default","module":"sitecourse","controller":"settings","action":"utility"}', 'sitecourse_admin_main_settings', '', 1),
('sitecourse_admin_main_video', 'sitecourse', 'Video Settings', '', '{"route":"admin_default","module":"sitecourse","controller":"settings","action":"video-settings"}', 'sitecourse_admin_main_settings', '', 2),
('sitecourse_admin_main_request', 'sitecourse', 'Approval Requests', '', '{"route":"admin_default","module":"sitecourse","controller":"manage","action":"request"}', 'sitecourse_admin_main_manage','',9),
('sitecourse_admin_main_index', 'sitecourse', 'Manage Courses', '', '{"route":"admin_default","module":"sitecourse","controller":"manage","action":"index"}', 'sitecourse_admin_main_manage','',8);



INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('sitecourse_approval', 'sitecourse', 'Your {item:$object} course is approved by the admin.', '0', '', '1'),
('sitecourse_disapproval', 'sitecourse', 'Your {item:$object} course is disapproved by the admin.', '0', '', '1'),
('sitecourse_review', 'sitecourse', '{item:$subject} has posted a review on your {item:$object} course.', '0', '', '1'),
('sitecourse_bestseller', 'sitecourse', 'Your {item:$object} course is marked as best seller.', '0', '', '1'),
('sitecourse_review_approval', 'sitecourse', 'Your review on {item:$object} has been approved.', '0', '', '1'),
('sitecourse_review_disapproval', 'sitecourse', 'Your review on {item:$object} has been disapproved.', '0', '', '1'),
('sitecourse_report', 'sitecourse', '{item:$subject} has reported your {item:$object}.', '0', '', '1'),
('sitecourse_reviewlike', 'sitecourse', '{item:$subject} has liked your review on {item:$object}.', '0', '', '1'),
('sitecourse_certificate', 'sitecourse', 'Your certificate for {item:$object} course has been issued.', '0', '', '1'),
('sitecourse_favourite', 'sitecourse', '{item:$subject} has added your {item:$object} to favorites.', '0', '', '1'),
('sitecourse_enrollment_disabled', 'sitecourse', 'Enrollments on your course {item:$object} have been banned. ', '0', '', '1'),
('sitecourse_enrollment_enabled', 'sitecourse', 'Enrollments on your course {item:$object} have been enabled. ', '0', '', '1'),
('sitecourse_purchase', 'sitecourse', '{item:$subject} has purchased your {item:$object} course.', '0', '', '1');


INSERT IGNORE INTO `engine4_core_jobtypes`(`title`,`type`,`module`,`plugin`,`enabled`,`priority`,`multi`) values
('Sitecourse Video Encode','sitecourse_encode','sitecourse','Sitecourse_Plugin_Job_Encode',1,75,1),
('Sitecourse Delete User', 'sitecourse_delete_user', 'sitecourse', 'Sitecourse_Plugin_Job_DeleteUser',1,75,1);


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('sitecourse_main', 'standard', 'Sitecourse Main Navigation Menu');



-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_courses`
--

DROP TABLE IF EXISTS `engine4_sitecourse_courses`;
CREATE TABLE `engine4_sitecourse_courses` (
  `course_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `subcategory_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `overview` text NOT NULL,
  `tags` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `difficulty_level` int NOT NULL,
  `photo_id` int NOT NULL,
  `signaturePhoto_id` int NOT NULL,
  `view_privacy` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `comment_privacy` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `duration` float NOT NULL,
  `draft` int NOT NULL,
  `course_benefits` text NOT NULL,
  `about_instructor` text NOT NULL,
  `prerequisites` text NOT NULL,
  `url` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `approved` int NOT NULL,
  `bestseller` tinyint NOT NULL DEFAULT '0',
  `enrolled_count` int NOT NULL,
  `request_count` int NOT NULL,
  `disapprove_reason` text COLLATE utf8_unicode_ci NOT NULL,
  `disable_enrollment` tinyint NOT NULL DEFAULT '0',
  `rating` float NOT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_topics`
--

DROP TABLE IF EXISTS `engine4_sitecourse_topics`;
CREATE TABLE `engine4_sitecourse_topics` (
  `topic_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `course_id` int NOT NULL,
  `order` int NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_lessons`
--

DROP TABLE IF EXISTS `engine4_sitecourse_lessons`;
CREATE TABLE `engine4_sitecourse_lessons` (
  `lesson_id` int NOT NULL AUTO_INCREMENT,
  `topic_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type`  varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `text`  text NOT NULL,
  `course_id` int NOT NULL,
  `order` int NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_categories`
--

DROP TABLE IF EXISTS `engine4_sitecourse_categories`;
CREATE TABLE `engine4_sitecourse_categories` (
  `category_id` int(11) NOT NULL auto_increment,
  `category_name` varchar(128) NOT NULL,
  `cat_dependency` int NOT NULL,
  `course_count` int NOT NULL DEFAULT '0',
  `cat_order` int Not NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_reviews`
--

DROP TABLE IF EXISTS `engine4_sitecourse_reviews`;
CREATE TABLE `engine4_sitecourse_reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `review_title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `review` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `creation_date` datetime NOT NULL,
  `status` int NOT NULL,
  `like_count` int NOT NULL,
  `dislike_count` int NOT NULL,
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Table structure for table `engine4_sitecourse_favourites`
--

DROP TABLE IF EXISTS `engine4_sitecourse_favourites`;
CREATE TABLE `engine4_sitecourse_favourites` (
  `favourite_id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int NOT NULL,
  PRIMARY KEY (`favourite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Table structure for table `engine4_sitecourse_announcement`
--

DROP TABLE IF EXISTS `engine4_sitecourse_announcements`;
CREATE TABLE `engine4_sitecourse_announcements` (
  `announcement_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `course_id` int NOT NULL,
  `body` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `enable` tinyint NOT NULL,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_reviewlikes`
--

DROP TABLE IF EXISTS `engine4_sitecourse_reviewlikes`;
CREATE TABLE `engine4_sitecourse_reviewlikes` (
  `like_id` int NOT NULL AUTO_INCREMENT,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `value` int NOT NULL,
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `engine4_sitecourse_lessoncompletes`
--

DROP TABLE IF EXISTS `engine4_sitecourse_completedlessons`;
CREATE TABLE `engine4_sitecourse_completedlessons` (
  `completedlesson_id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `lesson_id` int NOT NULL,
  `topic_id` int NOT NULL,
  PRIMARY KEY (`completedlesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `engine4_sitecourse_completecourses`
--

DROP TABLE IF EXISTS `engine4_sitecourse_completedcourses`;
CREATE TABLE `engine4_sitecourse_completedcourses` (
  `completedcourse_id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `completion_date` date NOT NULL,
  `certificate_issued` tinyint NOT NULL,
  PRIMARY KEY (`completedcourse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;


--
-- Dumping data for table `engine4_sitecourse_categories`
--

INSERT IGNORE INTO `engine4_sitecourse_categories`VALUES
(1,"Technical",0,0,1),
(2,"IT & Software",0,0,2),  
(3,"Business",0,0,3),
(4,"Finance",0,0,4),
(5,"Cloud Computing",0,0,5),
(6,"Personal Development",0,0,6),
(7,"Design",0,0,7),
(8,"Marketing",0,0,8),
(9,"Science",0,0,9),
(10,"Communication",0,0,10),
(11,"Health",0,0,11),
(12,"Others",0,0,12);



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_sitecourse_course_status', 'sitecourse', '[host],[email],[title],[status],[object_link]'),
('notify_sitecourse_course_newest','sitecourse','[host],[email],[title],[status],[object_link]'),
('notify_sitecourse_course_bestseller','sitecourse','[host],[email],[title],[object_link]'),
('notify_sitecourse_course_toprated','sitecourse','[host],[email],[title],[object_link]'),
('notify_sitecourse_course_review','sitecourse','[host],[email],[user_name],[title],[review_title],[review_description],[review_link]'),
('notify_sitecourse_course_purchase','sitecourse','[host],[email],[user_name],[title],[object_link]'),
('notify_sitecourse_course_certificate','sitecourse','[host],[email],[title],[object_link]'),
('notify_sitecourse_course_report','sitecourse','[host],[email],[title],[object_link]');

--
-- Table structure for table `engine4_sitecourse_videos`
--

DROP TABLE IF EXISTS `engine4_sitecourse_videos`;
CREATE TABLE `engine4_sitecourse_videos` (
  `video_id` int NOT NULL AUTO_INCREMENT,
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `file_id` int UNSIGNED NOT NULL,
  `duration` int UNSIGNED NOT NULL,
  `synchronized` int DEFAULT '0',
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_sitecourse_videos`
--

DROP TABLE IF EXISTS `engine4_sitecourse_reports`;
CREATE TABLE `engine4_sitecourse_reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `reporter_id` int DEFAULT NULL,
  `creation_date` date NOT NULL,
  `reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_gateways`
--


DROP TABLE IF EXISTS `engine4_sitecourse_gateways`;
CREATE TABLE IF NOT EXISTS `engine4_sitecourse_gateways` (
  `gateway_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NULL,
  `enabled` tinyint(1) unsigned NOT NULL default '0',
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `config` mediumblob NULL,
  `test_mode` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`gateway_id`),
  UNIQUE KEY `course_id` (`course_id`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_orders`
--

DROP TABLE IF EXISTS `engine4_sitecourse_orders`;
CREATE TABLE `engine4_sitecourse_orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `course_id` int UNSIGNED NOT NULL,
  `creation_date` datetime NOT NULL,
  `order_status` tinyint NOT NULL DEFAULT '0',
  `sub_total` float UNSIGNED NOT NULL,
  `grand_total` float UNSIGNED NOT NULL,
  `payment_status` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tax_amount` float UNSIGNED NOT NULL,
  `commission_type` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 => Fixed, 1 => Percentage',
  `commission_value` float UNSIGNED NOT NULL,
  `commission_rate` float UNSIGNED NOT NULL,
  `cheque_id` int UNSIGNED NOT NULL DEFAULT '0',
  `gateway_id` int NOT NULL,
  `gateway_profile_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `eventbill_id` int UNSIGNED NOT NULL DEFAULT '0',
  `payment_request_id` int NOT NULL DEFAULT '0',
  `ip_address` varbinary(16) NOT NULL,
  `non_payment_seller_reason` tinyint NOT NULL DEFAULT '0',
  `non_payment_seller_message` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `non_payment_admin_reason` tinyint NOT NULL DEFAULT '0',
  `non_payment_admin_message` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `direct_payment` tinyint NOT NULL DEFAULT '0' COMMENT '0 => Order Payment to Site Admin, 1 => Order Payment to Seller',
  `coupon_detail` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `is_private_order` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => Send activity feed, 1 => Not send activity feed',
  `payout_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `refund_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gateway_type` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `engine4_sitecourse_ordertickets`
--
DROP TABLE IF EXISTS `engine4_sitecourse_ordercourses`;
CREATE TABLE `engine4_sitecourse_ordercourses` (
  `ordercourse_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int UNSIGNED NOT NULL,
  `course_id` int UNSIGNED NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`ordercourse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_sitecourse_buyerdetails`
--
DROP TABLE IF EXISTS `engine4_sitecourse_buyerdetails`;
CREATE TABLE `engine4_sitecourse_buyerdetails` (
  `buyerdetail_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int UNSIGNED NOT NULL,
  `course_id` int UNSIGNED NOT NULL,
  `first_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `buyer_id` int NOT NULL,
  PRIMARY KEY (`buyerdetail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecourse_transactions`
--
DROP TABLE IF EXISTS `engine4_sitecourse_transactions`;
CREATE TABLE `engine4_sitecourse_transactions` (
  `transaction_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `sender_type` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 => user, 1 => admin, 2 => seller',
  `gateway_id` tinyint UNSIGNED NOT NULL COMMENT '1 => PayPal',
  `date` datetime NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `payment_order_id` int NOT NULL,
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `gateway_parent_transaction_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float UNSIGNED NOT NULL,
  `currency` char(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cheque_id` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `engine4_sitecourse_transactions`
--
ALTER TABLE `engine4_sitecourse_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `gateway_id` (`gateway_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `engine4_sitecourse_transactions`
--
ALTER TABLE `engine4_sitecourse_transactions`
  MODIFY `transaction_id` int UNSIGNED NOT NULL AUTO_INCREMENT;




-- --------------------------------------------------------

--
-- Default Structure for certificate
--


INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
('sitecourse.format.bodyhtmldefault', '<div style=\"position: relative; width: 100%; max-width: 65%; margin: 50px auto; display: flex; border-radius: 4px; box-shadow: rgba(0, 0, 0, 0.2) 0 0 15px 0; text-align: center;\">\r\n<div style=\"position: relative; margin: 0px auto; background-color: #ffffff; width: 100%; padding: 50px 0px; box-sizing: border-box; text-align: center;\"><img style=\"position: absolute; left: 0; right: 0; top: 0; bottom: 0; width: 100%; margin: 0 auto; object-fit: cover; height: 100%;\" src=\"[Background_Image]\" alt=\"\">\r\n<div style=\"padding: 15px; position: relative; width: 100%; height: auto; margin: 0 0 15px; max-width: max-content; box-sizing: border-box; display: flex; align-items: center; justify-content: center; float: right;\"><img style=\"width: 100%; position: relative;\" src=\"[Company_Logo]\"></div>\r\n<div style=\"position: relative; margin: 0; height: 100%; display: inline-block; text-align: center;\">\r\n<h3 style=\"margin: 0; font-size: 3.2rem; color: #242424; font-family: \'Merienda\', cursive; position: relative; line-height: normal; padding: 0;\">Certificate of Completion</h3>\r\n<p style=\"line-height: normal; font-size: 1.6rem; font-family: \'Merienda\', cursive; color: #242424; margin: 0;\">This is to certify that</p>\r\n<h3 style=\"font-family: \'Merienda\', cursive; font-weight: normal; color: #242424; font-size: 2.4rem; margin: 0; line-height: normal; padding: 0;\">[Student_Name]</h3>\r\n<div style=\"display: flex; justify-content: center; align-items: center; width: 100%; margin: 0 auto 15px; max-width: 75%;\">\r\n<p style=\"line-height: normal; font-size: 1.6rem; font-family: \'Merienda\', cursive; color: #242424; margin: 0; text-align: center;\">has successfully completed [Hours] total hours of [Course_Name] Course on</p>\r\n</div>\r\n<div style=\"display: flex; justify-content: center; align-items: center; width: 100%;\">\r\n<p style=\"line-height: normal; font-size: 1.6rem; font-family: \'Merienda\', cursive; color: #242424; margin: 0; font-weight: normal;\">[Date]</p>\r\n</div>\r\n<div style=\"text-align: center; display: flex; padding: 50px 15px 15px; box-sizing: border-box; width: 100%; justify-content: space-between; align-items: center;\">\r\n<div>\r\n<h5 style=\"margin: 0 auto 0; font-size: 1.2rem; font-family: \'Merienda\', cursive; line-height: normal;\">[Creator_Name]</h5>\r\n<p style=\"line-height: normal; font-size: 0.8rem; font-family: \'Merienda\', cursive; color: #242424; margin: 0 auto 0;\">Course Instructor</p>\r\n</div>\r\n<div><img style=\"width: 180px; position: relative;\" src=\"[Signature]\"></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>');

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('course_new', 'sitecourse', '{item:$subject} create a new course entry:', 1, 5, 1, 3, 1, 1);


--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sitecourse.allow.announcements', '1'),
('sitecourse.allow.editcategory', '1'),
('sitecourse.allow.report', '1'),
('sitecourse.allow.tags', '1'),
('sitecourse.bestseller.threshold', '5'),
('sitecourse.latest.threshold', '5'),
('sitecourse.mostrated.threshold', '5'),
('sitecourse.UrlP', 'courses'),
('sitecourse.UrlS', 'course');






-- ALL
-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","parent_member","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
-- ADMIN, SUPERADMIN
-- create, view, approved, rating, certification, approval reminders, max enrolment, max courses, review deletion, auto approve review
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'approve' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'reviews_ratings' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'auto_review_approve' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'review_deletion' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'certification' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'max_courses' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'max_enrollment' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'approval_reminders' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('superadmin', 'admin');


-- MODERATOR , DEFAULT
-- create, delete, approved, rating, certification, approval reminders, max enrolment, max courses, review deletion, auto approve review
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'approve' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'reviews_ratings' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'auto_review_approve' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'review_deletion' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'certification' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'max_courses' as `name`,
    10 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'max_enrollment' as `name`,
    20 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'approval_reminders' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user');


-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecourse_course' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
