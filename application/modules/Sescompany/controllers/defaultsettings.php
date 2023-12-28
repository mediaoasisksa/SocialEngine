<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$default_constants = array(
  'theme_color' => '1',
  'custom_theme_color' => '5',
  'company_theme_color' => '#CC0821',
	'company_main_width' => '1200px',
	'company_left_columns_width' => '240px',
	'company_right_columns_width' => '240px',
	'company_mobilehideleftrightcolumn' => '1',
	'company_body_background_color' => '#eeeeee',
	'company_font_color' => '#000',
	'company_font_color_light' => '#424242',
	'company_heading_color' => '#000',
	'company_links_color' => '#292929',
	'company_links_hover_color' => '#000',	
	//'company_content_heading_background_color' => '#f1f1f1',
	'company_content_background_color' => '#fff',
	'company_content_border_color' => '#E7E7E7',
	'company_form_label_color' => '#CC0821',
	'company_input_background_color' => '#fff',
	'company_input_font_color' => '#000',
	'company_input_border_color' => '#E7E7E7',
	'company_button_background_color' => '#CC0821',
	'company_button_background_color_hover' => '#CC0821',
	'company_button_border_color' => '#CC0821',
	'company_button_font_color' => '#fff',
	'company_button_font_hover_color' => '#fff',
	'company_comment_background_color' => '#f6f7f9',
	'company_header_background_color' => '#fff',
	'company_header_border_color' => '#eeeeee',
	'company_menu_logo_top_space' => '10px',
	//'company_mainmenu_background_color' => '#515151',
	//'company_mainmenu_background_color_hover' => '#363636',
	'company_mainmenu_links_color' => '#FFFFFF',
	'company_mainmenu_links_hover_color' => '#FFF',
	//'company_mainmenu_border_color' => '#666',
	'company_minimenu_links_color' => '#292929',
	'company_minimenu_links_hover_color' => '#CC0821',
	//'company_minimenu_border_color' => '#aaa',
	//'company_minimenu_icon' => 'minimenu-icons-white.png',
	'company_header_searchbox_background_color' => '#ebeeee',
	'company_header_searchbox_text_color' => '#767676',
	'company_header_searchbox_border_color' => '#E7E7E7',
	'company_footer_background_color' => '#222',
	'company_footer_border_color' => '#E7E7E7',
	'company_footer_text_color' => '#767676',
	'company_footer_links_color' => '#767676',
	'company_footer_links_hover_color' => '#FFFFFF',
	'company_body_background_image' =>'public/admin/blank.png',
	'company_header_type' => '2',
	'company_body_fontfamily' => '"Roboto"',
	'company_body_fontsize'  =>  '14px',
	'company_heading_fontfamily' =>  '"Roboto Condensed"',
	'company_heading_fontsize' =>  '17px',
	'company_mainmenu_fontfamily' =>  '"Roboto Condensed"',
	'company_mainmenu_fontsize' =>  '17px',
	'company_tab_fontfamily' =>  '"Roboto Condensed"',
	'company_tab_fontsize' =>  '15px',
);
Engine_Api::_()->sescompany()->readWriteXML('', '', $default_constants);

//Header Default Work
$content_id = $this->widgetCheck(array('widget_name' => 'sescompany.header', 'page_id' => '1'));

$minimenu = $this->widgetCheck(array('widget_name' => 'core.menu-mini', 'page_id' => '1'));
$menulogo = $this->widgetCheck(array('widget_name' => 'core.menu-logo', 'page_id' => '1'));
$mainmenu = $this->widgetCheck(array('widget_name' => 'core.menu-main', 'page_id' => '1'));
$minisearch = $this->widgetCheck(array('widget_name' => 'core.search-mini', 'page_id' => '1'));

$parent_content_id = $db->select()
        ->from('engine4_core_content', 'content_id')
        ->where('type = ?', 'container')
        ->where('page_id = ?', '1')
        ->where('name = ?', 'main')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (empty($content_id)) {
  if($minimenu)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$minimenu.'";');
  if($menulogo)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$menulogo.'";');
  if($mainmenu)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$mainmenu.'";');
  if($minisearch)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$minisearch.'";');
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.header',
      'page_id' => 1,
      'parent_content_id' => $parent_content_id,
      'order' => 20,
  ));
}

//Footer Default Work
$footerContent_id = $this->widgetCheck(array('widget_name' => 'sescompany.menu-footer', 'page_id' => '2'));
$footerMenu = $this->widgetCheck(array('widget_name' => 'core.menu-footer', 'page_id' => '2'));

$footerSocialSite = $this->widgetCheck(array('widget_name' => 'core.menu-social-sites', 'page_id' => '2'));
$footerLoginPopup = $this->widgetCheck(array('widget_name' => 'user.login-or-signup-popup', 'page_id' => '2'));

$parent_content_id = $db->select()
        ->from('engine4_core_content', 'content_id')
        ->where('type = ?', 'container')
        ->where('page_id = ?', '2')
        ->where('name = ?', 'main')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (empty($footerContent_id)) {

  if($footerMenu)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$footerMenu.'";');
    
  if($footerSocialSite)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$footerSocialSite.'";');
    
  if($footerLoginPopup)
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = "'.$footerLoginPopup.'";');
    
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.menu-footer',
      'page_id' => 2,
      'parent_content_id' => $parent_content_id,
      'order' => 10,
  ));
}

//Update Mini Menu
$db->update('engine4_core_menuitems', array('order' => 5), array('name = ?' => 'core_mini_profile'));
$db->update('engine4_core_menuitems', array('order' => 8), array('name = ?' => 'core_mini_notification'));
$db->update('engine4_core_menuitems', array('order' => 7), array('name = ?' => 'core_mini_messages'));
$db->update('engine4_core_menuitems', array('order' => 6), array('name = ?' => 'core_mini_friends'));
$db->update('engine4_core_menuitems', array('order' => 4), array('name = ?' => 'core_mini_settings'));
$db->update('engine4_core_menuitems', array('order' => 3), array('name = ?' => 'core_mini_admin'));
$db->update('engine4_core_menuitems', array('order' => 2), array('name = ?' => 'core_mini_auth'));
$db->update('engine4_core_menuitems', array('order' => 1), array('name = ?' => 'core_mini_signup'));
$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "0" WHERE `engine4_core_menuitems`.`name` = "core_mini_update";');


$column_exist = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'file_id'")->fetch();
if (empty($column_exist)) {
  $db->query("ALTER TABLE `engine4_core_menuitems` ADD `file_id` INT( 11 ) NOT NULL;");
}
$icon_type = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'icon_type'")->fetch();
if (empty($icon_type)) {
  $db->query("ALTER TABLE `engine4_core_menuitems` ADD `icon_type` TINYINT(1) NOT NULL DEFAULT '0';");
}
$font_icon = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'font_icon'")->fetch();
if (empty($font_icon)) {
  $db->query("ALTER TABLE `engine4_core_menuitems` ADD `font_icon` VARCHAR(255) NOT NULL;");
}

$db->query('DROP TABLE IF EXISTS `engine4_sescompany_managesearchoptions`;');
$db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_managesearchoptions` (
  `managesearchoption_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_id` INT(11) DEFAULT "0",
  `enabled` tinyint(1) NOT NULL DEFAULT "1",
  `order` int(11) NOT NULL,
  PRIMARY KEY (`managesearchoption_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');

$db->query('UPDATE `engine4_core_menuitems` SET `label` = "Manage Fonts" WHERE `engine4_core_menuitems`.`name` = "sescompany_admin_main_typography";');

//Login Page
$select = new Zend_Db_Select($db);
$page_login_id = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'user_auth_login')
        ->query()
        ->fetchColumn();
if ($page_login_id) {
  $db->query("UPDATE  `engine4_core_content` SET  `name` =  'sescompany.login' WHERE  `engine4_core_content`.`name` ='core.content' AND `engine4_core_content`.`page_id` ='".$page_login_id."' LIMIT 1");
}

$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('company.body.background.image', 'public/admin/blank.png'),
('company.left.columns.width', '240px'),
('company.main.width', '1200px'),
('company.mobilehideleftrightcolumn', '1'),
('company.right.columns.width', '240px'),
('sescompany.chooselandingdesign', '1'),
('sescompany.contheading', 'Blogs'),
('sescompany.contlimit', '4'),
('sescompany.contpopularitycriteria', 'creation_date'),
('sescompany.enable.footer', '1'),
('sescompany.footabtheading', 'About Us'),
('sescompany.footeraboutusdes', 'Lorem Ipsum Is Simply Dummy Text Of The Printing And Typesetting Industry.'),
('sescompany.googlefonts', '1'),
('sescompany.header.fixed', '0'),
('sescompany.header.loggedin.options.0', 'search'),
('sescompany.header.loggedin.options.1', 'miniMenu'),
('sescompany.header.loggedin.options.2', 'mainMenu'),
('sescompany.header.loggedin.options.3', 'logo'),
('sescompany.header.nonloggedin.options.0', 'search'),
('sescompany.header.nonloggedin.options.1', 'miniMenu'),
('sescompany.header.nonloggedin.options.2', 'mainMenu'),
('sescompany.header.nonloggedin.options.3', 'logo'),
('sescompany.heshowextraemailicon', 'fa-envelope-o'),
('sescompany.heshowextraemailnumber', 'info@business.com'),
('sescompany.heshowextralinks', '1'),
('sescompany.heshowextraphoneicon', 'fa-phone'),
('sescompany.heshowextraphonenumber', '123456789'),
('sescompany.la1aboutshow', '1'),
('sescompany.la1abtbgimage1', 'public/admin/background_img2.jpg'),
('sescompany.la1abtbgimage2', 'public/admin/bg_f.jpg'),
('sescompany.la1abtheading', 'Introduction'),
('sescompany.la1abtvideourl', 'https://youtu.be/juIJGBxj-4w'),
('sescompany.la1clientsbgimage', 'public/admin/background_img1.jpg'),
('sescompany.la1clientsheading', 'Our Clients'),
('sescompany.la1clientsshow', '1'),
('sescompany.la1cntbgimage', 'public/admin/drft.jpg'),
('sescompany.la1contentssshow', '1'),
('sescompany.la1countersheading', 'Statistics'),
('sescompany.la1countershow', '1'),
('sescompany.la1featuresheading', 'Highlighted Features'),
('sescompany.la1featuresshow', '1'),
('sescompany.la1fetbgimage', 'public/admin/whychooseus_bg.jpg'),
('sescompany.la1testimonialsheading', 'What People are saying!'),
('sescompany.la1testimonialssshow', '1'),
('sescompany.la2contactsbgimage', 'public/admin/contact_bg.jpg'),
('sescompany.la2contactsdescription', '<h3>HEAD OFFICE</h3>\r\n<p>70 abc road,India&nbsp;<br>Phone: 2122454485&nbsp;<br>Fax: 2122454485&nbsp;<br>Zip Code:20692&nbsp;<br>Email: support@mail.com</p>\r\n<h3>CUSTOMER CARE</h3>\r\n<p>1800-1234-5678</p>\r\n<h3>VISIT US</h3>\r\n<p>www.abc.com</p>'),
('sescompany.la2contactsheading', 'Contact Us'),
('sescompany.la2contactslocation', 'AZ, USA'),
('sescompany.la2contactsmainimage', 'public/admin/contact_main_img.jpg'),
('sescompany.la2contactsshow', '1'),
('sescompany.la2photosheading', 'Photo Gallery'),
('sescompany.la2photoslimit', '8'),
('sescompany.la2photosshow', '1'),
('sescompany.la2teamsbgimage', 'public/admin/team_bg.jpg'),
('sescompany.la2teamsheading', 'Our Team'),
('sescompany.la2teamsshow', '1'),
('sescompany.limit', '7'),
('sescompany.loginbackgroundimage', 'public/admin/login-bg.jpg'),
('sescompany.logo', 'public/admin/logo_business.png'),
('sescompany.mngcontentsbgimage', 'public/admin/Our-Clients.jpg'),
('sescompany.moretext', 'More'),
('sescompany.popup.day', '1'),
('sescompany.popup.enable', '1'),
('sescompany.popupfixed', '0'),
('sescompany.popupsign', '1'),
('sescompany.quicklinksenable', '1'),
('sescompany.responsive.layout', '1'),
('sescompany.rightsidenavigation', '1'),
('sescompany.search.limit', '8'),
('sescompany.searchleftoption', '0'),
('sescompany.showcopyrights', '1'),
('sescompany.showlanguage', '1'),
('sescompany.showsocialmedia', '1'),
('sescompany.sliderdescription', '<p><span style=\"font-family: times\\ new\\ roman, times, serif; font-size: 12pt; color: #000000;\"><span style=\"display: inline !important; float: none; font-variant: normal; letter-spacing: normal; text-align: left; text-decoration: none; text-indent: 0px; text-transform: none; -webkit-text-stroke-width: 0px; white-space: normal; word-spacing: 0px;\">The Most impressive template to get you going!&nbsp; Ideal combination of Multi purpose and Robust </span><span style=\"display: inline !important; float: none; font-variant: normal; letter-spacing: normal; text-align: left; text-decoration: none; text-indent: 0px; text-transform: none; -webkit-text-stroke-width: 0px; white-space: normal; word-spacing: 0px;\"> theme for your business platform. Easy to customize and even easier to use. Especially, the template is design to be user-friendly and flexible.</span></span></p>'),
('sescompany.sliderfacebooklink', 'socialenginesolutions'),
('sescompany.slidergooglelink', 'https://www.google.com'),
('sescompany.sliderheading', 'Multi-Responsive Business Theme'),
('sescompany.slidermorebtnlink', 'https://www.socialenginesolutions.com/'),
('sescompany.slidermorebtntext', 'View More'),
('sescompany.slidersharelink', '1'),
('sescompany.slidertwitterlink', 'https://www.twitter.com'),
('sescompany.socialmediaheading', 'Social Media');");


//Testimonials
$db->query("INSERT IGNORE INTO `engine4_sescompany_testimonials` (`testimonial_id`, `description`, `owner_name`, `designation`, `file_id`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, '“You made it so simple. My new site is so much faster and easier to work with than my old site. I just choose the page, make the change and click save. Thanks, guys!”\r\n', 'Sophia Setia', 'Executive head ', 15, 1, '2018-03-23 11:01:15', '2018-03-23 11:13:09', 0),
(2, ' “You made it so simple. My new site is so much faster and easier to work with than my old site. I just choose the page, make the change and click save. Thanks, guys!”\r\n', 'Rambo Tesa', 'Manager ', 120, 1, '2018-03-23 11:01:59', '2018-03-26 10:58:11', 0),
(3, '“Our takeout just went through the roof because we could be taking five orders at one time, instead of just one order at a time.”\r\n', 'Liam Nic', ' Software engineer   ', 17, 1, '2018-03-23 11:02:27', '2018-03-23 11:13:32', 0);");


$db->query("INSERT IGNORE INTO `engine4_sescompany_clients` (`client_id`, `client_name`, `client_link`, `file_id`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, 'Client4', 'https://www.socialenginesolutions.com/', 44, 1, '2018-03-23 11:16:12', '2018-03-23 12:02:01', 0),
(2, 'Client3', 'https://www.socialenginesolutions.com/', 45, 1, '2018-03-23 11:16:27', '2018-03-23 12:02:20', 0),
(3, 'Client2', 'https://www.socialenginesolutions.com/', 46, 1, '2018-03-23 11:16:40', '2018-03-23 12:02:34', 0),
(4, 'Client1', 'https://www.socialenginesolutions.com/', 47, 1, '2018-03-23 11:16:58', '2018-03-23 12:03:10', 0);");

$db->query("INSERT IGNORE INTO `engine4_sescompany_features` (`feature_id`, `feature_name`, `description`, `file_id`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, '24/7 Support', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 37, 1, '2018-03-23 11:55:29', '2018-03-23 11:55:29', 0),
(2, 'Crafty', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n', 38, 1, '2018-03-23 11:55:56', '2018-03-23 11:55:57', 0),
(3, 'Equipped', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n', 39, 1, '2018-03-23 11:56:17', '2018-03-23 11:56:17', 0),
(4, 'Personalised', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n', 40, 1, '2018-03-23 11:56:50', '2018-03-23 11:56:50', 0),
(5, 'Supreme Quality', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n', 141, 1, '2018-03-23 11:57:12', '2018-03-27 11:44:10', 0),
(6, 'Well documented', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n', 42, 1, '2018-03-23 11:57:30', '2018-03-23 11:57:30', 0);");

$db->query("INSERT IGNORE INTO `engine4_sescompany_slides` (`slide_id`, `file_id`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, 57, 1, '2018-03-26 05:55:36', '2018-03-26 05:55:36', 0),
(2, 58, 1, '2018-03-26 05:57:32', '2018-03-26 05:57:32', 0),
(3, 59, 1, '2018-03-26 05:59:15', '2018-03-26 05:59:15', 0),
(4, 60, 1, '2018-03-26 06:00:00', '2018-03-26 06:00:00', 0),
(5, 61, 1, '2018-03-26 06:00:50', '2018-03-26 06:00:50', 0);");

$db->query("INSERT IGNORE INTO `engine4_sescompany_counters` (`counter_id`, `counter_name`, `counter_value`, `file_id`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, 'Products', '33', 138, 1, '2018-03-23 10:45:03', '2018-03-27 07:57:04', 0),
(2, 'Client Over Years', '364', 137, 1, '2018-03-23 10:45:21', '2018-03-27 07:56:47', 0),
(3, 'Projects Delivered', '200', 139, 1, '2018-03-23 10:45:37', '2018-03-27 07:57:28', 0),
(4, 'Million Dollars', '20', 140, 1, '2018-03-23 10:45:57', '2018-03-27 07:57:51', 0);");

$db->query("INSERT IGNORE INTO `engine4_sescompany_abouts` (`about_id`, `about_name`, `description`, `font_icon`, `file_id`, `readmore_button_name`, `readmore_button_link`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, 'High Quality Theme', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\n', 'fa-star', 62, 'Read more', 'https://www.socialenginesolutions.com/', 1, '2018-03-23 10:52:52', '2018-03-26 06:46:34', 0),
(2, 'Fully Responsive', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\n', 'fa-laptop', 63, 'Read more', 'https://www.socialenginesolutions.com/', 1, '2018-03-23 10:54:17', '2018-03-26 06:47:01', 0),
(3, 'Modern features', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\n', ' fa-thumbs-up', 64, 'Read more', 'https://www.socialenginesolutions.com/', 1, '2018-03-23 10:55:14', '2018-03-26 06:47:34', 0),
(4, ' Future Proof', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\n', 'fa-shield', 65, 'Read more', 'https://www.socialenginesolutions.com/', 1, '2018-03-23 10:56:07', '2018-03-26 06:48:14', 0);");

$db->query("INSERT IGNORE INTO `engine4_sescompany_teams` (`team_id`, `name`, `designation`, `file_id`, `quote`, `description`, `phone`, `email`, `address`, `facebook`, `twitter`, `linkdin`, `googleplus`, `enabled`, `creation_date`, `modified_date`, `order`) VALUES
(1, 'Sneha', 'Manager', 18, '', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '9856321452', 'abc@gmail.com', 'Sector 78', 'www.facebook.com', 'www.twitter.com', 'www.linkdin.com', 'www.google.com', 1, '2018-03-23 11:21:20', '2018-03-23 11:21:20', 0),
(2, 'Lorel', 'Analyst', 19, '', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '9999999963', 'abc@gmail.com', 'Sector 78', 'www.facebook.com', 'www.twitter.com', 'www.linkdin.com', 'www.google.com', 1, '2018-03-23 11:22:23', '2018-03-23 11:22:23', 0),
(3, 'Roniie', 'Freelancer', 20, '', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '9856321452', 'abc@gmail.com', 'Sector 78', 'www.facebook.com', 'www.twitter.com', 'www.linkdin.com', 'www.google.com', 1, '2018-03-23 11:23:20', '2018-03-23 11:23:20', 0),
(4, 'Ronald Trum', 'Associate', 90, '', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '9999999963', 'abc@gmail.com', '', 'www.facebook.com', 'www.twitter.com', 'www.linkdin.com', 'www.google.com', 1, '2018-03-26 07:12:03', '2018-03-26 07:12:33', 0);");



//Testimonials
$testimonials = Engine_Api::_()->getDbtable('testimonials', 'sescompany')->getTestimonials();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'tetsimonials' . DIRECTORY_SEPARATOR;
foreach($testimonials as $result) {
  $file = $PathFile . 'user'.$result->getIdentity() . '.jpg';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

//Clients
$clients = Engine_Api::_()->getDbtable('clients', 'sescompany')->getClients();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR;

foreach($clients as $result) {
  $file = $PathFile . $result->getIdentity() . '.png';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

//Features
$features = Engine_Api::_()->getDbtable('features', 'sescompany')->getFeatures();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR;
foreach($features as $result) {
  $file = $PathFile . $result->getIdentity() . '.png';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

$slides = Engine_Api::_()->getDbtable('slides', 'sescompany')->getSlides();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'slider_images' . DIRECTORY_SEPARATOR;
foreach($slides as $result) {
  $file = $PathFile . $result->getIdentity() . '.jpg';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

$counters = Engine_Api::_()->getDbtable('counters', 'sescompany')->getCounters();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'statistics' . DIRECTORY_SEPARATOR;
foreach($counters as $result) {
  $file = $PathFile . $result->getIdentity() . '.png';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

$teams = Engine_Api::_()->getDbtable('teams', 'sescompany')->getTeams();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'team' . DIRECTORY_SEPARATOR;
foreach($teams as $result) {
  $file = $PathFile . 'member'.$result->getIdentity() . '.jpg';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

$abouts = Engine_Api::_()->getDbtable('abouts', 'sescompany')->getAbouts();
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'about_us' . DIRECTORY_SEPARATOR;
foreach($abouts as $result) {
  $file = $PathFile . 'introduction_img'.$result->getIdentity() . '.jpg';
  if(!empty($file)) {
    $file_ext = pathinfo($file);
    $file_ext = $file_ext['extension'];

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $storageObject = $storage->createFile(@$file, array(
      'parent_id' => $result->getIdentity(),
      'parent_type' => $result->getType(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
    ));
    // Remove temporary file
    @unlink($file['tmp_name']);
    $result->file_id = $storageObject->file_id;
    $result->save();
  }
}

//Abouts US
$pageId = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('url = ?', 'company-about')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$pageId ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => '',
    'displayname' => 'About Us',
    'url' => 'company-about',
    'title' => 'About Us',
    'description' => '',
    'custom' => 1,
  ));
  $pageId = $db->lastInsertId();
  $db->query('UPDATE `engine4_core_pages` SET `name` = NULL WHERE `engine4_core_pages`.`page_id` = "'.$pageId.'";');

  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $pageId,
    'order' => 1,
  ));
  $topId = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $pageId,
    'order' => 2,
  ));
  $mainId = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $topId,
  ));
  $topMiddleId = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $mainId,
    'order' => 2,
  ));
  $mainMiddleId = $db->lastInsertId();
  
  
  // Insert banner
  $db->insert('engine4_core_banners', array(
    'name' => 'company',
    'module' => 'core',
    'title' => 'About Company',
    'body' => '',
    'photo_id' => 0,
    'params' => '{"label":"","uri":""}',
    'custom' => 0
  ));
  $bannerId = $db->lastInsertId();

  if( $bannerId ) {
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.banner',
      'page_id' => $pageId,
      'parent_content_id' => $topMiddleId,
      'params' => '{"name":"sescompany.banner","banner_id":"'.$bannerId.'","height":"300","fullwidth":"1","title":"","nomobile":"0"}',
      'order' => 1,
    ));
  }

  // Insert menu
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 2,
    'params' => '{"title":"","adminTitle":"History Overview ","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_about_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">History Overview<\/span><\/p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut laoreet leo. Integer non dapibus justo. Curabitur malesuada tristique tortor a feugiat. Donec id felis ac nibh porttitor egestas. Duis tortor erat, finibus sed luctus nec, eleifend in elit. Sed ac ligula sed est iaculis egestas eu in neque. Etiam ut mi posuere, vehicula tellus ac, tincidunt nulla. Sed quis vulputate erat. Aenean ut ipsum feugiat neque convallis bibendum. Sed id laoreet dolor. Fusce dignissim vulputate molestie. Proin malesuada metus sit amet neque tincidunt suscipit. Maecenas id varius massa. Mauris condimentum at urna at pharetra.<\/p>\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 3,
    'params' => '{"title":"","adminTitle":"Latest Deals","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_about_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Latest Deals<\/span><\/p>\r\n<div class=\"_left\">\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"_img\"><img style=\"width: 100%;\" src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/pexels-photo-541526.jpeg\"><\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.html-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 4,
    'params' => '{"title":"","adminTitle":"","data":"<style type=\"text\/css\">\r\n.layout_core_html_block{\r\n\tbackground:none !important;\r\n\tpadding:0 !important;\r\n\tmargin:0 !important;\r\n}\r\n.sescompany_about_cont p{\r\n\tmargin-bottom:15px;\r\n}\r\n.sescompany_about_cont ._left{\r\n\tfloat:left;\r\n\twidth:60%;\r\n\tpadding-right:40px;\r\n}\r\n.sescompany_about_cont ._img{\r\n\tfloat:left;\r\n\twidth:40%;\r\n}\r\n@media only screen and (max-width: 768px){\r\n\t.sescompany_about_cont ._left,\r\n\t.sescompany_about_cont ._img{width:100%;padding:10px 0;}\r\n}\r\n<\/style>","nomobile":"0","name":"core.html-block"}',
  ));
}

//Cases
$pageId = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('url = ?', 'company-cases')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$pageId ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => '',
    'displayname' => 'Cases',
    'url' => 'company-cases',
    'title' => 'Cases',
    'description' => '',
    'custom' => 1,
  ));
  $pageId = $db->lastInsertId();
  $db->query('UPDATE `engine4_core_pages` SET `name` = NULL WHERE `engine4_core_pages`.`page_id` = "'.$pageId.'";');

  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $pageId,
    'order' => 1,
  ));
  $topId = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $pageId,
    'order' => 2,
  ));
  $mainId = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $topId,
  ));
  $topMiddleId = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $mainId,
    'order' => 2,
  ));
  $mainMiddleId = $db->lastInsertId();
  
  // Insert banner
  $db->insert('engine4_core_banners', array(
    'name' => 'company',
    'module' => 'core',
    'title' => 'Cases',
    'body' => '',
    'photo_id' => 0,
    'params' => '{"label":"","uri":""}',
    'custom' => 0
  ));
  $bannerId = $db->lastInsertId();

  if( $bannerId ) {
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.banner',
      'page_id' => $pageId,
      'parent_content_id' => $topMiddleId,
      'params' => '{"name":"sescompany.banner","banner_id":"'.$bannerId.'","height":"300","fullwidth":"1","title":"","nomobile":"0"}',
      'order' => 1,
    ));
  }

  // Insert menu
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 2,
    'params' => '{"title":"","adminTitle":"Case 1","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_cases_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Case 1<\/span><\/p>\r\n<div class=\"_left\">\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<p>Morbi tristique interdum mauris sed finibus. Proin porttitor hendrerit interdum. Phasellus vulputate ullamcorper condimentum. Maecenas a mauris turpis. Pellentesque eget venenatis tellus, sed lobortis tellus. Nunc eget tristique mauris. Cras nec eleifend orci. Praesent eu ante at massa efficitur faucibus. Nullam consectetur tellus in felis maximus, nec elementum eros consectetur. In ante erat, ornare cursus lectus a, feugiat lobortis leo. Nullam nec lacinia libero, vel sagittis diam. Integer eget ipsum et turpis pulvinar tristique eget quis arcu. Phasellus commodo varius magna, aliquet luctus velit maximus ut. Sed porttitor at massa a pretium.<\/p>\r\n<\/div>\r\n<div class=\"_img\"><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/ProjectManagement.jpg\"><\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 3,
    'params' => '{"title":"","adminTitle":"Case 2","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_cases_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Case&nbsp;2<\/span><\/p>\r\n<div class=\"_img\"><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/consulting.jpg\"><\/div>\r\n<div class=\"_left\" style=\"padding: 0 0 0 40px;\">\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.html-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 4,
    'params' => '{"title":"","adminTitle":"Style","data":"<style type=\"text\/css\">\r\n.layout_core_html_block{\r\n\tbackground:none !important;\r\n\tpadding:0 !important;\r\n\tmargin:0 !important;\r\n}\r\n.sescompany_cases_cont p{\r\n\tmargin-bottom:15px;\r\n}\r\n.sescompany_cases_cont ._left{\r\n\tfloat:left;\r\n\twidth:60%;\r\n\tpadding-right:40px;\r\n}\r\n.sescompany_cases_cont ._img{\r\n\tfloat:left;\r\n\twidth:40%;\r\n}\r\n.sescompany_cases_cont ._img img{\r\n\twidth:100%;\r\n}\r\n@media only screen and (max-width: 768px){\r\n\t.sescompany_cases_cont ._left,\r\n\t.sescompany_cases_cont ._img{width:100%;padding:10px 0;}\r\n}\r\n<\/style>","nomobile":"0","name":"core.html-block"}',
  ));
}

//What we do 
$pageId = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('url = ?', 'company-whatwedo')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$pageId ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => '',
    'displayname' => 'What We Do',
    'url' => 'company-whatwedo',
    'title' => 'What We Do',
    'description' => '',
    'custom' => 1,
  ));
  $pageId = $db->lastInsertId();
  $db->query('UPDATE `engine4_core_pages` SET `name` = NULL WHERE `engine4_core_pages`.`page_id` = "'.$pageId.'";');

  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $pageId,
    'order' => 1,
  ));
  $topId = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $pageId,
    'order' => 2,
  ));
  $mainId = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $topId,
  ));
  $topMiddleId = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $mainId,
    'order' => 2,
  ));
  $mainMiddleId = $db->lastInsertId();
  
  // middle column
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.container-tabs',
    'parent_content_id' => $mainMiddleId,
    'order' => 3,
    'params' => '{"max":"6"}',
  ));
  $tabId = $db->lastInsertId('engine4_core_content');
  
  // Insert banner
  $db->insert('engine4_core_banners', array(
    'name' => 'company',
    'module' => 'core',
    'title' => 'What We Do',
    'body' => '',
    'photo_id' => 0,
    'params' => '{"label":"","uri":""}',
    'custom' => 0
  ));
  $bannerId = $db->lastInsertId();

  if( $bannerId ) {
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.banner',
      'page_id' => $pageId,
      'parent_content_id' => $topMiddleId,
      'params' => '{"name":"sescompany.banner","banner_id":"'.$bannerId.'","height":"300","fullwidth":"1","title":"","nomobile":"0"}',
      'order' => 1,
    ));
  }

  // Insert menu
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 2,
    'params' => '{"title":"","adminTitle":"What WE DO ","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_whatwedo_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">WHAT WE DO<\/span><\/p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut laoreet leo. Integer non dapibus justo. Curabitur malesuada tristique tortor a feugiat. Donec id felis ac nibh porttitor egestas. Duis tortor erat, finibus sed luctus nec, eleifend in elit. Sed ac ligula sed est iaculis egestas eu in neque. Etiam ut mi posuere, vehicula tellus ac, tincidunt nulla. Sed quis vulputate erat. Aenean ut ipsum feugiat neque convallis bibendum. Sed id laoreet dolor. Fusce dignissim vulputate molestie. Proin malesuada metus sit amet neque tincidunt suscipit. Maecenas id varius massa. Mauris condimentum at urna at pharetra.<\/p>\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 1,
    'params' => '{"title":"Consulting Company","adminTitle":"Consulting Company","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_whatwedo_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Consulting Company<\/span><\/p>\r\n<div class=\"_left\">\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"_img\"><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/consulting.jpg\"><\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 2,
    'params' => '{"title":"Project Management","adminTitle":"Project Management","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_whatwedo_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Project Management<\/span><\/p>\r\n<div class=\"_left\">\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<p>Morbi tristique interdum mauris sed finibus. Proin porttitor hendrerit interdum. Phasellus vulputate ullamcorper condimentum. Maecenas a mauris turpis. Pellentesque eget venenatis tellus, sed lobortis tellus. Nunc eget tristique mauris. Cras nec eleifend orci. Praesent eu ante at massa efficitur faucibus. Nullam consectetur tellus in felis maximus, nec elementum eros consectetur. In ante erat, ornare cursus lectus a, feugiat lobortis leo. Nullam nec lacinia libero, vel sagittis diam. Integer eget ipsum et turpis pulvinar tristique eget quis arcu. Phasellus commodo varius magna, aliquet luctus velit maximus ut. Sed porttitor at massa a pretium.<\/p>\r\n<\/div>\r\n<div class=\"_img\"><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/ProjectManagement.jpg\"><\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 3,
    'params' => '{"title":"Corporate Management","adminTitle":"Corporate Management","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_whatwedo_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Corporate Management<\/span><\/p>\r\n<div class=\"_left\">\r\n<p>Morbi tristique interdum mauris sed finibus. Proin porttitor hendrerit interdum. Phasellus vulputate ullamcorper condimentum. Maecenas a mauris turpis. Pellentesque eget venenatis tellus, sed lobortis tellus. Nunc eget tristique mauris. Cras nec eleifend orci. Praesent eu ante at massa efficitur faucibus. Nullam consectetur tellus in felis maximus, nec elementum eros consectetur. In ante erat, ornare cursus lectus a, feugiat lobortis leo. Nullam nec lacinia libero, vel sagittis diam. Integer eget ipsum et turpis pulvinar tristique eget quis arcu. Phasellus commodo varius magna, aliquet luctus velit maximus ut. Sed porttitor at massa a pretium.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"_img\"><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/CorporateManagement.jpg\"><\/div>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.html-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 10,
    'params' => '{"title":"","adminTitle":"Style","data":"<style type=\"text\/css\">\r\n.layout_core_html_block{\r\n\tbackground:none !important;\r\n\tpadding:0 !important;\r\n\tmargin:0 !important;\r\n}\r\n.sescompany_whatwedo_cont p{\r\n\tmargin-bottom:15px;\r\n}\r\n.sescompany_whatwedo_cont ._left{\r\n\tfloat:left;\r\n\twidth:60%;\r\n\tpadding-right:40px;\r\n}\r\n.sescompany_whatwedo_cont ._img{\r\n\tfloat:left;\r\n\twidth:40%;\r\n}\r\n.sescompany_whatwedo_cont ._img img{\r\n\twidth:100%;\r\n}\r\n@media only screen and (max-width: 768px){\r\n\t.sescompany_whatwedo_cont ._left,\r\n\t.sescompany_whatwedo_cont ._img{width:100%;padding:10px 0;}\r\n}\r\n<\/style>","nomobile":"0","name":"core.html-block"}',
  ));
}

//Projects
$pageId = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('url = ?', 'company-Project')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$pageId ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => '',
    'displayname' => 'Project',
    'url' => 'company-Project',
    'title' => 'Project',
    'description' => '',
    'custom' => 1,
  ));
  $pageId = $db->lastInsertId();
  $db->query('UPDATE `engine4_core_pages` SET `name` = NULL WHERE `engine4_core_pages`.`page_id` = "'.$pageId.'";');

  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $pageId,
    'order' => 1,
  ));
  $topId = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $pageId,
    'order' => 2,
  ));
  $mainId = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $topId,
  ));
  $topMiddleId = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $pageId,
    'parent_content_id' => $mainId,
    'order' => 2,
  ));
  $mainMiddleId = $db->lastInsertId();
  
  // middle column
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.container-tabs',
    'parent_content_id' => $mainMiddleId,
    'order' => 3,
    'params' => '{"max":"6"}',
  ));
  $tabId = $db->lastInsertId('engine4_core_content');
  
  // Insert banner
  $db->insert('engine4_core_banners', array(
    'name' => 'company',
    'module' => 'core',
    'title' => 'Project',
    'body' => '',
    'photo_id' => 0,
    'params' => '{"label":"","uri":""}',
    'custom' => 0
  ));
  $bannerId = $db->lastInsertId();

  if( $bannerId ) {
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sescompany.banner',
      'page_id' => $pageId,
      'parent_content_id' => $topMiddleId,
      'params' => '{"name":"sescompany.banner","banner_id":"'.$bannerId.'","height":"300","fullwidth":"1","title":"","nomobile":"0"}',
      'order' => 1,
    ));
  }

  
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 1,
    'params' => '{"title":"Our History","adminTitle":"Our History","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_project_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">What History says<\/span><\/p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut laoreet leo. Integer non dapibus justo. Curabitur malesuada tristique tortor a feugiat. Donec id felis ac nibh porttitor egestas. Duis tortor erat, finibus sed luctus nec, eleifend in elit. Sed ac ligula sed est iaculis egestas eu in neque. Etiam ut mi posuere, vehicula tellus ac, tincidunt nulla. Sed quis vulputate erat. Aenean ut ipsum feugiat neque convallis bibendum. Sed id laoreet dolor. Fusce dignissim vulputate molestie. Proin malesuada metus sit amet neque tincidunt suscipit. Maecenas id varius massa. Mauris condimentum at urna at pharetra.<\/p>\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"sescompany_project_img\">\r\n<p><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/pexels-photo-669615.jpeg\"><\/p>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 2,
    'params' => '{"title":"What we have done","adminTitle":"What we have done","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_project_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">What We Have Done<\/span><\/p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut laoreet leo. Integer non dapibus justo. Curabitur malesuada tristique tortor a feugiat. Donec id felis ac nibh porttitor egestas. Duis tortor erat, finibus sed luctus nec, eleifend in elit. Sed ac ligula sed est iaculis egestas eu in neque. Etiam ut mi posuere, vehicula tellus ac, tincidunt nulla. Sed quis vulputate erat. Aenean ut ipsum feugiat neque convallis bibendum. Sed id laoreet dolor. Fusce dignissim vulputate molestie. Proin malesuada metus sit amet neque tincidunt suscipit. Maecenas id varius massa. Mauris condimentum at urna at pharetra.<\/p>\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"sescompany_project_img\">\r\n<p><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/pexels-photo-257897.jpeg\"><\/p>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $pageId,
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'parent_content_id' => $tabId,
    'order' => 3,
    'params' => '{"title":"Next Steps","adminTitle":"Next Steps","data":"<div class=\"sesbasic_clearfix sescompany_project sesbasic_bxs\">\r\n<div class=\"sescompany_project_cont\">\r\n<p><span style=\"color: #000000; font-size: 17pt;\">Next Steps<\/span><\/p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut laoreet leo. Integer non dapibus justo. Curabitur malesuada tristique tortor a feugiat. Donec id felis ac nibh porttitor egestas. Duis tortor erat, finibus sed luctus nec, eleifend in elit. Sed ac ligula sed est iaculis egestas eu in neque. Etiam ut mi posuere, vehicula tellus ac, tincidunt nulla. Sed quis vulputate erat. Aenean ut ipsum feugiat neque convallis bibendum. Sed id laoreet dolor. Fusce dignissim vulputate molestie. Proin malesuada metus sit amet neque tincidunt suscipit. Maecenas id varius massa. Mauris condimentum at urna at pharetra.<\/p>\r\n<p>Nunc ultrices imperdiet est, in consequat metus tristique id. In dictum ante elit, a feugiat nisi viverra vitae. Duis varius sit amet lorem quis mattis. Integer pretium sem turpis, auctor ultrices eros pretium in. Aenean nunc dui, dignissim et congue nec, accumsan sed erat. Phasellus in nunc posuere, rhoncus lorem quis, volutpat risus. Mauris nec lacus non metus tristique dignissim eget volutpat ipsum. Aliquam dapibus, ante ac tempor tempor, turpis arcu tempus mi, non laoreet arcu ligula sed erat. In accumsan lobortis nulla, in euismod ex interdum sed. Integer pharetra metus nisl, porttitor placerat arcu scelerisque eu. Duis lobortis feugiat neque a aliquam. Etiam accumsan purus elit, nec tincidunt mauris bibendum sed. Suspendisse placerat massa eros, nec ultrices odio bibendum vel. Integer sem justo, finibus eu scelerisque et, sagittis eget turpis. Aliquam non velit sit amet odio gravida varius quis id justo.<\/p>\r\n<p>Mauris accumsan quam id lacus ultricies auctor. Proin tincidunt quis risus ac tincidunt. Integer molestie lacus quis ultricies pharetra. Nulla semper erat congue mauris tempor, non porta tortor luctus. Proin magna nisi, pellentesque sit amet quam id, auctor bibendum metus. Mauris nec enim eget nisi varius tempor vitae sit amet elit. Vestibulum id tincidunt elit. Pellentesque aliquam, massa at semper fermentum, nisi tellus egestas odio, sed condimentum mauris velit vitae lacus. Integer vel volutpat quam. Proin eleifend arcu eu elit aliquet finibus. Aliquam nec lorem cursus, porta magna ut, mollis augue. Vestibulum vel dapibus libero.<\/p>\r\n<\/div>\r\n<div class=\"sescompany_project_img\">\r\n<p><img src=\"http:\/\/companydemo.socialenginesolutions.com\/public\/admin\/pexels-photo-669615.jpeg\"><\/p>\r\n<\/div>\r\n<\/div>","name":"core.rich-text-block","nomobile":"0"}',
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.html-block',
    'page_id' => $pageId,
    'parent_content_id' => $mainMiddleId,
    'order' => 10,
    'params' => '{"title":"","adminTitle":"","data":"<style type=\"text\/css\">\r\n.layout_core_html_block{\r\n\tbackground:none !important;\r\n\tpadding:0 !important;\r\n\tmargin:0 !important;\r\n}\r\n.sescompany_project_cont{\r\n\twidth:70%;\r\n\tpadding-right:40px;\r\n\tfloat:left;\r\n}\r\n.sescompany_project_cont p{\r\n\tmargin-bottom:15px;\r\n}\r\n.sescompany_project_img{\r\n\twidth:30%;\r\n\tfloat:left;\r\n}\r\n.sescompany_project_img img{width:100%;}\r\n@media only screen and (max-width: 768px){\r\n\t.sescompany_project_cont, .sescompany_project_img{\r\n\t\twidth:100%;\r\n\t\tpadding:0;\r\n\t\tmargin:10px 0;\r\n\t}\r\n}\r\n<\/style>","nomobile":"0","name":"core.html-block"}',
  ));
}

//Menus Work
$menuId = $db->select()
  ->from('engine4_core_menuitems', 'id')
  ->order('id DESC')
  ->limit(1)
  ->query()
  ->fetchColumn();
$menuId = $menuId + 10;
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`, `file_id`, `icon_type`, `font_icon`) VALUES
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "Blogs", "", \'{\"uri\":\"#\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "sescompany_extra_menu", "", 1, 1, 999, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "IOS App Link", "", \'{\"uri\":\"#\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "sescompany_extra_menu", "", 1, 1, 999, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "Android App Link", "", \'{\"uri\":\"#\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "sescompany_extra_menu", "", 1, 1, 999, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "Project", "", \'{\"uri\":\"pages\\/company-project\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "core_main", "", 1, 1, 2, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "About Us", "", \'{\"uri\":\"pages\\/company-about\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "core_main", "", 1, 1, 3, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "What We Do", "", \'{\"uri\":\"pages\\/company-whatwedo\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}\', "core_main", "", 1, 1, 4, 0, 0, ""),
("'.$menuId++.'", "custom_'.$menuId++.'", "core", "Cases", "", \'{\"uri\":\"pages\\/company-cases\"}\', "core_main", "", 1, 1, 5, 0, 0, "");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sescompany_admin_main_managebanners", "sescompany", "Manage Banners", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-banner","action":"index"}\', "sescompany_admin_main", "", 7);');

$db->query('DROP TABLE IF EXISTS `engine4_sescompany_banners`;');
$db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_banners` (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `banner_name` VARCHAR(255)  NULL ,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT "1",
  PRIMARY KEY (`banner_id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;'
);
			
$db->query('DROP TABLE IF EXISTS `engine4_sescompany_bannerslides`;');
$db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_bannerslides` (
  `bannerslide_id` int(11) unsigned NOT NULL auto_increment,
  `banner_id` int(11) DEFAULT NULL, 
  `title` varchar(255) DEFAULT NULL,
  `title_button_color` varchar(255) DEFAULT NULL,
  `description` text,
  `description_button_color` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_id` INT(11) DEFAULT "0",
  `status` ENUM("1","2","3") NOT NULL DEFAULT "1",
  `extra_button_linkopen` TINYINT(1) NOT NULL DEFAULT "0",
  `extra_button` tinyint(1) DEFAULT "0",
  `extra_button_text` varchar(255) DEFAULT NULL,
  `extra_button_link` varchar(255) DEFAULT NULL,
  `order` tinyint(10) NOT NULL DEFAULT "0",
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT "1",
  PRIMARY KEY (`bannerslide_id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
');