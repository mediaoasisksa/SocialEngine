<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    install.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Installer extends Engine_Package_Installer_Module {
    function onPreinstall() {
        $getErrorMsg = $this->_getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'siteapi';
        $PLUGIN_TITLE = 'Siteapi';
        $PLUGIN_VERSION = '4.9.4p6';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'SocialEngine REST API Plugin';
        $PRODUCT_TITLE = 'SocialEngine REST API Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.9p13';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }

        parent::onPreinstall();
    }

    public function onInstall() {
        $db = $this->getDb();
        $this->_addactivityFeedSettingTabs();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')->where('name = ?', 'siteapi');
        $is_Mod = $select->query()->fetchObject();
        if (!empty($is_Mod)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteapi_admin_tip_messages", "siteapi", "Tip Messages", NULL, \'{"route":"admin_default","module":"siteapi","controller":"settings", "action":"tip-messages"}\', "siteapi_admin_main", NULL, 1, 0, 2);');
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_menuitems')
                ->where('name = ?', 'siteapi_admin_maping');
        $rowExists = $select->query()->fetchObject();
        if (!isset($rowExists) || empty($rowExists)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteapi_admin_maping", "siteapi", "Sync Profile Fields", NULL, \'{"route":"admin_default","module":"siteapi","controller":"profile-maps-contact", "action":"manage"}\', "siteapi_admin_main", NULL, 1, 0, 3);');
        }

        // ADD SHOW COLUMN IN NOTIFICATION TABLE
        $isActivityCommentTableExist = $db->query("SHOW TABLES LIKE 'engine4_activity_comments'")->fetch();
        if (!empty($isActivityCommentTableExist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'body'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_activity_comments` CHANGE `body` `body` BLOB NOT NUL");
            }
        }

        // ADD SHOW COLUMN IN NOTIFICATION TABLE
        $isCommentTableExist = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($isCommentTableExist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'body'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_core_comments` CHANGE `body` `body` BLOB NOT NUL");
            }
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_menuitems')
                ->where('name = ?', 'siteandroidapp_admin_api_sitereview_views')
                ->where('plugin = ?', 'Siteapi_Plugin_Menus::mltMapping');
        $rowExists = $select->query()->fetchObject();
        if (isset($rowExists) && !empty($rowExists)) {
            $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Siteandroidapp_Plugin_Menus::mltMapping' WHERE `engine4_core_menuitems`.`name` = 'siteandroidapp_admin_api_sitereview_views'");
        }


        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_menuitems')
                ->where('name = ?', 'siteiosapp_admin_api_sitereview_views')
                ->where('plugin = ?', 'Siteapi_Plugin_Menus::mltMapping');
        $rowExists = $select->query()->fetchObject();
        if (isset($rowExists) && !empty($rowExists)) {
            $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Siteiosapp_Plugin_Menus::mltMapping' WHERE `engine4_core_menuitems`.`name` = 'siteiosapp_admin_api_sitereview_views'");
        }

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteapi';");

        $db->query("ALTER TABLE `engine4_activity_actions` CHANGE `body` `body` BLOB NULL DEFAULT NULL;");

        $this->runQueries('4.8.9', '4.8.9p3');

        // ADD SHOW COLUMN IN NOTIFICATION TABLE
        $isNotificationTableExist = $db->query("SHOW TABLES LIKE 'engine4_activity_notifications'")->fetch();
        if (!empty($isNotificationTableExist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'show'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_activity_notifications` ADD `show` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }

        // ADD VIEW COLUMN IN MESSAGE TABLE
        $isMessageTableExist = $db->query("SHOW TABLES LIKE 'engine4_messages_recipients'")->fetch();
        if (!empty($isMessageTableExist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_messages_recipients LIKE 'inbox_view'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_messages_recipients` ADD `inbox_view` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }


        parent::onInstall();
    }

    function onDisable() {
        $db = $this->getDb();
        $db->query('UPDATE `engine4_core_modules` SET `enabled` = "0" WHERE `engine4_core_modules`.`name` = "Siteandroidapp" LIMIT 1 ;');
        $db->query('UPDATE `engine4_core_modules` SET `enabled` = "0" WHERE `engine4_core_modules`.`name` = "Siteiosapp" LIMIT 1 ;');

        parent::onDisable();
    }

    private function _addactivityFeedSettingTabs() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteiosapp')
                ->where('enabled = ?', 1);
        $isIosModuleEnabled = $select->query()->fetchObject();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteandroidapp')
                ->where('enabled = ?', 1);
        $isAndroidModuleEnabled = $select->query()->fetchObject();
        if (!empty($isIosModuleEnabled) || !empty($isAndroidModuleEnabled)) {
            $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteapi_admin_api_feed_settings\' LIMIT 1')->fetch();
            if (empty($isRowExist)) {
                $db->query('INSERT INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, "siteapi_admin_api_feed_settings", "siteapi", "Activity Feed Settings", NULL, \'{"route":"admin_default","module":"siteapi","controller":"settings","action":"siteapi-feed-settings"}\', "siteapi_admin_main", NULL, "1", "0", "1")');
            }
        }
    }

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'advancedactivity' => '4.9.1',
            'sitetagcheckin' => '4.8.9p1',
            'facebooksefeed' => '4.8.9',
            'sitemobile' => '4.8.9p3',
            'sitemember' => '4.9.1',
            'siteusercoverphoto' => '4.9.1',
            'sitepagereview' => '4.9.1',
            'sitehashtag' => '4.9.0',
            'sitegroup' => '4.9.1',
            'sitereaction' => '4.9.1'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

//				$isModSupport = strcasecmp($getModVersion->version, $value);
                $running_version = $getModVersion->version;
                $product_version = $value;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }

                if (!empty($shouldUpgrade)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

    public function runQueries($from, $to) {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')->where('name = ?', 'siteapi');
        $object = $select->query()->fetchObject();
        if (isset($object->version) &&
                (($object->version == '4.8.9') ||
                ($object->version == '4.8.9p1') ||
                ($object->version == '4.8.9p2'))) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteapi_admin_api_menus", "siteapi", "APP Dashboard Menus", NULL, \'{"route":"admin_default","module":"siteapi","controller":"menus", "action":"manage"}\', "siteapi_admin_main", NULL, 1, 0, 2);');

            $db->query('CREATE TABLE IF NOT EXISTS `engine4_siteapi_menus` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `dashboard_label` varchar(128) NOT NULL,
  `header_label` varchar(128) DEFAULT NULL,
  `module` varchar(32) DEFAULT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `url` varchar(264) DEFAULT NULL,
  `type` varchar(32) NOT NULL DEFAULT "menu",
  `status` tinyint(4) NOT NULL DEFAULT "1",
  `default` tinyint(4) NOT NULL DEFAULT "0",
  `order` smallint(6) NOT NULL DEFAULT "999",
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;');

            $db->query('INSERT IGNORE INTO `engine4_siteapi_menus` (`menu_id`, `name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `type`, `status`, `default`, `order`) VALUES
(1, "home", "Home", "", "core", "", "", "menu", 1, 1, 1),
(2, "core_main_global_search", "Search", "", "core", "", "", "menu", 1, 1, 2),
(3, NULL, "Favorites", "", NULL, "", "", "category", 1, 1, 3),
(4, "core_mini_messages", "Messages", "", "messages", "", "", "menu", 1, 1, 4),
(5, "core_mini_notification", "Notifications", "", "activity", "", "", "menu", 1, 1, 5),
(6, "core_mini_friend_request", "Friend Requests", "", "user", "", "", "menu", 1, 1, 6),
(7, NULL, "APPS", "", NULL, "", "", "category", 1, 1, 7),
(8, "core_main_user", "Members", "", "user", "", "", "menu", 1, 1, 8),
(9, "core_main_album", "Albums", "", "album", "", "", "menu", 1, 1, 9),
(10, "core_main_video", "Videos", "", "video", "", "", "menu", 1, 1, 10),
(11, "core_main_blog", "Blogs", "", "blog", "", "", "menu", 1, 1, 11),
(12, "core_main_classified", "Classifieds", "", "classified", "", "", "menu", 1, 1, 12),
(13, "core_main_group", "Groups", "", "group", "", "", "menu", 1, 1, 13),
(14, "core_main_event", "Events", "", "event", "", "", "menu", 1, 1, 14),
(15, "core_main_music", "Music", "", "music", "", "", "menu", 1, 1, 15),
(16, NULL, "Account Settings", "", NULL, "", "", "category", 1, 1, 16),
(17, "user_settings", "Settings", "", "user", "", "", "menu", 1, 1, 17),
(18, "contact_us", "Contact Us", "", "core", "", "", "menu", 1, 1, 18),
(19, "privacy_policy", "Privacy Policy", "", "core", "", "", "menu", 1, 1, 19),
(20, "terms_of_service", "Terms Of Service", "", "core", "", "", "menu", 1, 1, 20),
(21, NULL, "Help & Settings", "", NULL, "", "", "category", 1, 1, 21),
(22, "signout", "Sign Out", "", "user", "", "", "menu", 1, 1, 22);');
        }
    }

}

?>
