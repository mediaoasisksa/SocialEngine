<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecore_Installer extends Engine_Package_Installer_Module
{

  public function onPreinstall()
  {
    $packagesDirPath = APPLICATION_PATH . DS . "application" . DS . "packages";
    foreach( scandir($packagesDirPath) as $file ) {
      if( strpos($file, 'module-seaocore-') === false ) {
        continue;
      }
      $version = str_replace(array('module-seaocore-', '.json'), '', $file);
      $checkVersion = $this->checkVersion($version, '4.10.3p18');
      if( empty($checkVersion) ) {
        @unlink($packagesDirPath . DS . $file);
      }
    }
    try {
      $packageManager = new Engine_Package_Manager(array(
        "basePath" => APPLICATION_PATH,
        "db" => $this->getDb(),
      ));
      $packagePath = APPLICATION_PATH . "/application/modules/Sitecore/settings/packages/";
      $files = array('module-seaocore-4.10.3p17.json');
      foreach( $files as $file ) {
        $packageFile = $packagePath . $file;
        $package = new Engine_Package_Manifest($packageFile, array(
          'basePath' => APPLICATION_PATH,
        ));
        $manifestFile = APPLICATION_PATH . DS . $package->getPath() . DS . "settings/manifest.php";
        $version = $this->_getVersionTarget();
        if( file_exists($manifestFile) ) {
          $manifestData = include $manifestFile;
          $version = $manifestData['package']['version'];
        }
        $package->setVersion($version);
        $operation = new Engine_Package_Manager_Operation_Install($packageManager, $package);
        $result = $packageManager->execute($operation, 'preinstall');
        if( !empty($result['errors']) ) {
          foreach( $result['errors'] as $error ) {
            $this->_error($error);
          }
          break;
        }
        $result = $packageManager->execute($operation, 'install');
        if( !empty($result['errors']) ) {
          foreach( $result['errors'] as $error ) {
            $this->_error($error);
          }
          break;
        }
        $result = $packageManager->execute($operation, 'postinstall');
        if( !empty($result['errors']) ) {
          foreach( $result['errors'] as $error ) {
            $this->_error($error);
          }
          break;
        }
      }
    } catch( Exception $e ) {
      die(" Exception " . $e);
    }
    parent::onPreinstall();
  }

  function onInstall()
  {

    $db = $this->getDb();

    $column_params_exist = $db->query("SHOW COLUMNS FROM engine4_storage_files LIKE 'params'")->fetch();
    if( empty($column_params_exist) ) {
      $db->query("ALTER TABLE `engine4_storage_files` ADD `params` TEXT NULL DEFAULT NULL");
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'handler'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `handler` `handler` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
      if (!empty($table_exist)) {
        $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
        if (empty($widgetAdminColumn)) {
          $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
      }
    }
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type <= 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationsettings LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type <= 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notificationsettings` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'menu'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type <= 64 ) {
        $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `menu` `menu` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type <= 64 ) {
        $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actionsettings LIKE 'type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_activity_actionsettings` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'name'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 64 ) {
        $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `name` `name` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_core_modules FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_core_modules LIKE 'title'")->fetch();
    if( !empty($type_array) ) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if( $length_type < 128 ) {
        $db->query("ALTER TABLE `engine4_core_modules` CHANGE `title` `title` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CHANGE IN CORE SETTING TABLE
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_core_settings'")->fetch();
    if( !empty($table_exist) ) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_core_settings LIKE 'value'")->fetch();
      if( !empty($column_exist) ) {
        $db->query("ALTER TABLE `engine4_core_settings` CHANGE `value` `value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
      }
    }

    //START CODE FOR INSERTING THE LIGHBOX WIDGET IN THE HEADER
    $select = new Zend_Db_Select($db);
    $page_id = $select
      ->from('engine4_core_pages', array('page_id'))
      ->where('name = ?', 'header')
      ->query()
      ->fetchColumn();

    if( !empty($page_id) ) {
      $select = new Zend_Db_Select($db);
      $parent_content_id = $select
        ->from('engine4_core_content', array('content_id'))
        ->where('page_id =?', $page_id)
        ->where('type =?', 'container')
        ->where('name =?', 'main')
        ->query()
        ->fetchColumn();

      $select = new Zend_Db_Select($db);
      $content_id = $select
        ->from('engine4_core_content', array('content_id'))
        ->where('page_id = ?', $page_id)
        ->where('name =?', 'seaocore.seaocores-lightbox')
        ->query()
        ->fetchColumn();
      if( empty($content_id) ) {
        $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				  ($page_id, 'widget', 'seaocore.seaocores-lightbox', '999', $parent_content_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
      }
    }
    //END CODE FOR INSERTING THE LIGHBOX WIDGET IN THE HEADER    
    //START FOR UPDATE SEAO IN THE MENU OF THE ADMIN PANL.
    $modArray = array('advancedactivity', 'birthday', 'list', 'sitelike', 'advancedslideshow', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'sitealbum', 'siteslideshow', 'sitetagcheckin', 'sitereviewlistingtype', 'document', 'recipe', 'sitemailtemplates', 'sitemobile', 'sitereview', 'sitevideoview', 'eventdocument', 'peopleyoumayknow', 'sitefaq', 'sitetutorial', 'userconnection', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitepagemember', 'sitepageurl', 'sitepageoffer', 'sitepagebadge', 'sitepagelikebox', 'sitepageinvite', 'sitepageadmincontact', 'sitepageform', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', '
sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitebusinessmember', 'sitebusinessurl');

    foreach( $modArray as $value ) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')
        ->where('name = ?', "$value")
        ->where('enabled = ?', 1);
      $isModEnabled = $select->query()->fetchObject();
      if( !empty($isModEnabled) ) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_menuitems', array('label'))
          ->where('name = ?', "core_admin_main_plugins_$value")
          ->where('label NOT LIKE "%SEAO - %"')
          ->where('enabled = ?', 1);
        $getLabel = $select->query()->fetchObject();
        if( !empty($getLabel) ) {
          $label = $getLabel->label;
          $db->query("UPDATE  `engine4_core_menuitems` SET  `label` =  'SEAO - $label' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_$value';");
        }
      }
    }

    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Discount Coupons' WHERE  `engine4_core_menuitems`.`name` ='core_admin_plugins_sitecoupon';");

    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Userconnection' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_Userconn';");


    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - E-commerce Store (Magento Integration)' WHERE  `engine4_core_menuitems`.`name` ='core_siteestore_api';");

    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Pokes' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_pokesettings';");

    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Suggestions' WHERE  `engine4_core_menuitems`.`name` ='module_suggestion';");

    $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - SocialEngineAddOns Core' WHERE  `engine4_core_menuitems`.`name` ='core_admin_plugins_Seaocore';");

    //END FOR UPDATE SEAO IN THE MENU OF THE ADMIN PANL.
    $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_main_integrated'");

    $select = new Zend_Db_Select($db);
    $seaocoreVersion = $select
      ->from('engine4_core_modules', 'version')
      ->where('name = ?', 'seaocore')
      ->query()
      ->fetchColumn();
    $select = new Zend_Db_Select($db);
    $coreVersion = $select
      ->from('engine4_core_modules', 'version')
      ->where('name = ?', 'core')
      ->query()
      ->fetchColumn();

    if( $this->checkVersion('4.9.0', $seaocoreVersion) == 1 && $this->checkVersion('4.9.0', $coreVersion) == 1 ) {

      $column_editable = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'editable'")->fetch();
      if( !empty($column_editable) ) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` DROP COLUMN editable");
      }
      $column_modified = $db->query("SHOW COLUMNS FROM engine4_activity_actions LIKE 'modified_date'")->fetch();
      if( !empty($column_modified) ) {
        $db->query("ALTER TABLE `engine4_activity_actions` DROP COLUMN modified_date");
      }
    }

    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'document');
    $is_document_object = $select->query()->fetchObject();
    if( !empty($is_document_object) ) {
      $documentmodulesTable = $db->query('SHOW TABLES LIKE \'engine4_document_modules\'')->fetch();
      if( empty($documentmodulesTable) ) {
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_document_modules` (
  `module_id` int(64) NOT NULL AUTO_INCREMENT,
  `item_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `integrated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `item_type` (`item_type`),
  KEY `item_module` (`item_module`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
      }
    }

    parent::onInstall();
    if( $this->checkVersion('4.10.3p18', $seaocoreVersion) == 1 ) {
      $column_params_exist = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'show'")->fetch();
      if( !empty($column_params_exist) ) {
        $db->query("INSERT IGNORE INTO `engine4_seaocore_notifications` (`notification_id`, `show`) SELECT `notification_id`, `show` FROM `engine4_activity_notifications`");
      }
    }
  }

  public function onEnable()
  {
    $this->_seaocoreInstallOperation('enable');
    parent::onEnable();
  }

  public function onDisable()
  {
    if (!$this->hasSeaocorePackage()) {
      $this->_seaocoreInstallOperation('disable');
    }
    parent::onDisable();
  }

  protected function _seaocoreInstallOperation($operationType)
  {
    try {
      // Execute these for disableing Sitereaction, Sitetagcheckin & Sitehashtag plugins 
      $packageManager = new Engine_Package_Manager(array(
        "basePath" => APPLICATION_PATH,
        "db" => $this->getDb(),
      ));
      $packagePath = APPLICATION_PATH . "/application/modules/Sitecore/settings/packages/";
      $files = array('module-seaocore-4.10.3p17.json');
      foreach( $files as $file ) {

        $packageFile = $packagePath . $file;
        $package = new Engine_Package_Manifest($packageFile, array(
          'basePath' => APPLICATION_PATH,
        ));
        $version = $this->_getVersionDatabase();
        $manifestFile = APPLICATION_PATH . DS . $package->getPath() . DS . "settings/manifest.php";
        if( file_exists($manifestFile) ) {
          $manifestData = include $manifestFile;
          $version = $manifestData['package']['version'];
        }
        $package->setVersion($version);
        $operation = new Engine_Package_Manager_Operation_Install($packageManager, $package);
        $result = $packageManager->execute($operation, $operationType);
        if( !empty($result['errors']) ) {
          foreach( $result['errors'] as $error ) {
            $this->_error($error);
          }
        }
      }
    } catch( Exception $e ) {
      die(" Exception " . $e);
    }
  }

  private function hasSeaocorePackage()
  {
    $packagesDirPath = APPLICATION_PATH . DS . "application" . DS . "packages";
    $hasSeaocore = false;
    foreach( scandir($packagesDirPath) as $file ) {
      if( strpos($file, 'module-seaocore-') === false ) {
        continue;
      }
      $version = str_replace(array('module-seaocore-', '.json'), '', $file);
      $checkVersion = $this->checkVersion($version, '4.10.3p17');
      if( $checkVersion == 1 ) {
        $hasSeaocore = true;
        break;
      }
    }
    return $hasSeaocore;
  }

  private function checkVersion($databaseVersion, $checkDependancyVersion)
  {
    $f = $databaseVersion;
    $s = $checkDependancyVersion;
    if( strcasecmp($f, $s) == 0 )
      return -1;
    $fArr = explode(".", $f);
    $sArr = explode('.', $s);
    if( count($fArr) <= count($sArr) )
      $count = count($fArr);
    else
      $count = count($sArr);

    for( $i = 0; $i < $count; $i++ ) {
      $fValue = $fArr[$i];
      $sValue = $sArr[$i];
      if( is_numeric($fValue) && is_numeric($sValue) ) {
        if( $fValue > $sValue )
          return 1;
        elseif( $fValue < $sValue )
          return 0;
        else {
          if( ($i + 1) == $count ) {
            return -1;
          } else
            continue;
        }
      }
      elseif( is_string($fValue) && is_numeric($sValue) ) {
        $fsArr = explode("p", $fValue);

        if( $fsArr[0] > $sValue )
          return 1;
        elseif( $fsArr[0] < $sValue )
          return 0;
        else {
          return 1;
        }
      } elseif( is_numeric($fValue) && is_string($sValue) ) {
        $ssArr = explode("p", $sValue);

        if( $fValue > $ssArr[0] )
          return 1;
        elseif( $fValue < $ssArr[0] )
          return 0;
        else {
          return 0;
        }
      } elseif( is_string($fValue) && is_string($sValue) ) {
        $fsArr = explode("p", $fValue);
        $ssArr = explode("p", $sValue);
        if( $fsArr[0] > $ssArr[0] )
          return 1;
        elseif( $fsArr[0] < $ssArr[0] )
          return 0;
        else {
          if( $fsArr[1] > $ssArr[1] )
            return 1;
          elseif( $fsArr[1] < $ssArr[1] )
            return 0;
          else {
            return -1;
          }
        }
      }
    }
  }

}
?>
