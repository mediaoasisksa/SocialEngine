<?php

//folder name or directory name.
$module_name = 'sescompany';

//product title and module title.
$module_title = 'The Company & Business - Responsive Multi-Purpose Theme';

if (!$this->getRequest()->isPost()) {
  return;
}

if (!$form->isValid($this->getRequest()->getPost())) {
  return;
}

if ($this->getRequest()->isPost()) {

  $postdata = array();
  //domain name
  $postdata['domain_name'] = $_SERVER['HTTP_HOST'];
  //license key
  $postdata['licenseKey'] = @base64_encode($_POST['sescompany_licensekey']);
  $postdata['module_title'] = @base64_encode($module_title);

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "http://www.socialenginesolutions.com/licensecheck.php");
  curl_setopt($ch, CURLOPT_POST, 1);

  // in real life you should use something like:
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

  // receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $server_output = curl_exec($ch);

  $error = 0;
  if (curl_error($ch)) {
    $error = 1;
  }
  curl_close($ch);

  //here we can set some variable for checking in plugin files.
  if (1) {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.pluginactivated')) {
    
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      
      $db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
      ("sescompany_extra_menu", "standard", "SES - Company Theme - Footer Extra Links Menus", 5);');
      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
      ("sescompany_admin_main_menus", "sescompany", "Manage Header", "", \'{"route":"admin_default","module":"sescompany","controller":"manage", "action":"header-settings"}\', "sescompany_admin_main", "", 3),
      ("sescompany_admin_main_footer", "sescompany", "Manage Footer", "", \'{"route":"admin_default","module":"sescompany","controller":"manage", "action":"footer-settings"}\', "sescompany_admin_main", "", 4),
      ("sescompany_admin_main_landingpagesettings", "sescompany", "Manage Landing Page", "", \'{"route":"admin_default","module":"sescompany","controller":"settings", "action":"landing-page-setting"}\', "sescompany_admin_main", "", 2),
      ("sescompany_admin_main_styling", "sescompany", "Color Schemes", "", \'{"route":"admin_default","module":"sescompany","controller":"settings", "action":"styling"}\', "sescompany_admin_main", "", 6),
      ("sescompany_admin_main_typography", "sescompany", "Typography", "", \'{"route":"admin_default","module":"sescompany","controller":"settings", "action":"typography"}\', "sescompany_admin_main", "", 5),
      ("sescompany_admin_main_customcss", "sescompany", "Custom CSS", "", \'{"route":"admin_default","module":"sescompany","controller":"custom-theme", "action":"index"}\', "sescompany_admin_main", "", 7),
      ("core_mini_notification", "user", "Notifications", "", \'{"route":"default","module":"sescompany","controller":"notifications","action":"pulldown"}\', "core_mini", "", 999),
      ("core_mini_friends", "user", "Friend Requests", "", \'{"route":"default","module":"sescompany","controller":"index","action":"friend-request"}\', "core_mini", "", 999);');
      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
      ("sescompany_admin_main_landingpagesetting", "sescompany", "Landing Page Settings", "", \'{"route":"admin_default","module":"sescompany","controller":"settings", "action":"landing-page-setting"}\', "sescompany_admin_main_landingpagesettings", "", 1),
      ("sescompany_admin_main_landingpagetestimonials", "sescompany", "Testimonials", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-testimonials"}\', "sescompany_admin_main_landingpagesettings", "", 2),
      ("sescompany_admin_main_landingpageclients", "sescompany", "Clients", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-clients"}\', "sescompany_admin_main_landingpagesettings", "", 3),
      ("sescompany_admin_main_landingpagefeatures", "sescompany", "Highlighted Features", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-features"}\', "sescompany_admin_main_landingpagesettings", "", 4),
      ("sescompany_admin_main_landingpageslides", "sescompany", "Slides", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-slides"}\', "sescompany_admin_main_landingpagesettings", "", 5),
      ("sescompany_admin_main_landingpagecounters", "sescompany", "Rolling Statistics", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-counters"}\', "sescompany_admin_main_landingpagesettings", "", 6),
      ("sescompany_admin_main_landingpageabouts", "sescompany", "Introduction Video & Features", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-abouts"}\', "sescompany_admin_main_landingpagesettings", "", 7),
      ("sescompany_admin_main_manageteams", "sescompany", "Team", "", \'{"route":"admin_default","module":"sescompany","controller":"manage-teams"}\', "sescompany_admin_main_landingpagesettings", "", 8);');
      
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_testimonials`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_testimonials` (
        `testimonial_id` int(11) unsigned NOT NULL auto_increment,
        `description` text,
        `owner_name` varchar(255) DEFAULT NULL,
        `designation` varchar(255) DEFAULT NULL,
        `file_id` INT(11) DEFAULT "0",
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`testimonial_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_clients`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_clients` (
        `client_id` int(11) unsigned NOT NULL auto_increment,
        `client_name` varchar(255) DEFAULT NULL,
        `client_link` varchar(255) DEFAULT NULL,
        `file_id` INT(11) DEFAULT "0",
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`client_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_features`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_features` (
        `feature_id` int(11) unsigned NOT NULL auto_increment,
        `feature_name` varchar(255) DEFAULT NULL,
        `description` text,
        `file_id` INT(11) DEFAULT "0",
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`feature_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_slides`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_slides` (
        `slide_id` int(11) unsigned NOT NULL auto_increment,
        `file_id` INT(11) DEFAULT "0",
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`slide_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_counters`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_counters` (
        `counter_id` int(11) unsigned NOT NULL auto_increment,
        `counter_name` varchar(255) DEFAULT NULL,
        `counter_value` varchar(255) DEFAULT NULL,
        `file_id` INT(11) DEFAULT "0",
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`counter_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_abouts`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_abouts` (
        `about_id` int(11) unsigned NOT NULL auto_increment,
        `about_name` varchar(255) DEFAULT NULL,
        `description` text,
        `font_icon` varchar(255) DEFAULT NULL,
        `file_id` INT(11) DEFAULT "0",
        `readmore_button_name` varchar(255) DEFAULT NULL,
        `readmore_button_link` varchar(255) DEFAULT NULL,
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`about_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      
      $db->query('DROP TABLE IF EXISTS `engine4_sescompany_teams`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sescompany_teams` (
        `team_id` int(11) unsigned NOT NULL auto_increment,
        `name` varchar(255) DEFAULT NULL,
        `designation` varchar(255) DEFAULT NULL,
        `file_id` INT(11) DEFAULT "0",
        `quote` varchar(255)  DEFAULT NULL,
        `description` text,
        `phone` varchar(255)  DEFAULT NULL,
        `email` varchar(255)  DEFAULT NULL,
        `address` varchar(255)  DEFAULT NULL,
        `facebook` varchar(255)  DEFAULT NULL,
        `twitter` varchar(255)  DEFAULT NULL,
        `linkdin` varchar(255)  DEFAULT NULL,
        `googleplus` varchar(255)  DEFAULT NULL,
        `enabled` TINYINT(1) NOT NULL DEFAULT "1",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` tinyint(10) NOT NULL DEFAULT "0",
        PRIMARY KEY (`team_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
    
      
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_messages_recipients LIKE 'company_read'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_messages_recipients` ADD `company_read` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
      
      $table_exist_notifications = $db->query('SHOW TABLES LIKE \'engine4_activity_notifications\'')->fetch();
      if (!empty($table_exist_notifications)) {
        $company_read = $db->query('SHOW COLUMNS FROM engine4_activity_notifications LIKE \'company_read\'')->fetch();
        if (empty($company_read)) {
          $db->query('ALTER TABLE `engine4_activity_notifications` ADD `company_read` TINYINT(1) NOT NULL DEFAULT "0";');
        }
      }

      $column_exist = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'view_notification'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_activity_notifications` ADD `view_notification` TINYINT( 1 ) NOT NULL;");
      }

      $column_exist = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'file_id'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_core_menuitems` ADD `file_id` INT( 11 ) NOT NULL;");
      }

      $table = $db->query("SHOW TABLES LIKE 'engine4_sescompany_slideimages'")->fetch();
      if (!empty($table)) {
        $column_exist = $db->query("SHOW COLUMNS FROM engine4_sescompany_slideimages LIKE 'image_url'")->fetch();
        if (empty($column_exist)) {
          $db->query("ALTER TABLE `engine4_sescompany_slideimages` ADD `image_url` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        }
      }
      
      include_once APPLICATION_PATH . "/application/modules/Sescompany/controllers/defaultsettings.php";
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.pluginactivated', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.licensekey', $_POST['sescompany_licensekey']);
    }
    $domain_name = @base64_encode(str_replace(array('http://','https://','www.'),array('','',''),$_SERVER['HTTP_HOST']));
		$licensekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.licensekey');
		$licensekey = @base64_encode($licensekey);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.sesdomainauth', $domain_name);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.seslkeyauth', $licensekey);
		$error = 1;
  } else {
    $error = $this->view->translate('Please enter correct License key for this product.');
    $error = Zend_Registry::get('Zend_Translate')->_($error);
    $form->getDecorator('errors')->setOption('escape', false);
    $form->addError($error);
    $error = 0;
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.licensekey', $_POST['sescompany_licensekey']);
    return;
    $this->_helper->redirector->gotoRoute(array());
  }
}

?>