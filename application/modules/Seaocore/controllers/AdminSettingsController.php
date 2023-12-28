<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminSettingsController extends Core_Controller_Action_Admin {
  
  public function helpInviteAction () {
    
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_helpInvite');
  }
  
  public function mapGuidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_map');
  }
  
  public function indexAction() {
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    $this->view->se410Check = Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.10.0');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_settings');
    $this->view->form = $form = new Seaocore_Form_Admin_Settings();
    if ($this->getRequest()->isPost()) {
        $values = $_POST;
        $settings = Engine_Api::_()->getApi('settings', 'core');      
        foreach ($values as $key => $value) {
          if($settings->hasSetting($key))
          $settings->removeSetting($key);
          $settings->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }
    
  }
  
  public function mapAction () {
  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_map');

    $this->view->form = $form = new Seaocore_Form_Admin_Map();
    if (!$this->getRequest()->isPost()) { return; }
    if (!$form->isValid($this->getRequest()->getPost())) {  return; }
    
    $values = $_POST; 
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1))->toarray();
    if($values['seaocore_locationspecific'] && !$locations) {
			$error = $this->view->translate('You have not created Location from "Manage Locations" tab .');
			$error = Zend_Registry::get('Zend_Translate')->_($error);
			$form->getDecorator('errors')->setOption('escape', false);
			$form->addError($error);
			return;
    }
    
    
    $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
    if (!$values['seaocore_locationspecific'] && isset($_POST['seaocore_locationdefault']) && !empty($_POST['seaocore_locationdefault']) && $locationDefault != $_POST['seaocore_locationdefault']) {
        
        $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $_POST['seaocore_locationdefault']));
        
        if (empty($getSEALocation)) {
            $latLng = $this->setDefaultMapCenterPoint('', $_POST['seaocore_locationdefault'], 1);
            $latitude = (float) $latLng['latitude'];
            $longitude = (float) $latLng['longitude'];
        } else {
            $latitude = (float) $getSEALocation->latitude;
            $longitude = (float) $getSEALocation->longitude;
        }

        if (empty($latitude) || empty($longitude)) {
            $error = $this->view->translate('"Default Location for searching" is not valid!');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        } else {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.locationdefaultlatitude', $latitude);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.locationdefaultlongitude', $longitude);
        }
    }
    
    foreach ($values as $key => $value) {
      if($settings->hasSetting($key))
      $settings->removeSetting($key);
      $settings->setSetting($key, $value);
    }
    
    if($values['seaocore_locationspecific']) {
        $locationRow = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($values['seaocore_locationdefaultspecific']);
        if($locationRow) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.locationdefault', $locationRow->location); 
            Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.locationdefaultlatitude', $locationRow->latitude);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.locationdefaultlongitude', $locationRow->longitude);
        }
    }    
    
    //CLEAR THE MENUS CACHE
    Engine_Api::_()->seaocore()->clearMenuCache();
    
    $form->addNotice('Your changes have been saved.');
  }
  
    //ACTION FOR SET THE DEFAULT MAP CENTER POINT
    public function setDefaultMapCenterPoint($oldLocation, $newLocation, $returnLatLng = 0) {

        if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'SocialEngineAddOns Core'));
            if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                $latitude = $locationResults['latitude'];
                $longitude = $locationResults['longitude'];
            }

            if ($returnLatLng) {
                return array('latitude' => $latitude, 'longitude' => $longitude);
            }
        }
    }  

      //ACTION TO SAVE THE SOCIAL KEYS OF SITE
  public function saveKeysAction()
  {  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_plugin_keys');
    $installedModuleArray = array();
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelogin')) {
       $installedModuleArray[] = 'sitelogin';
    }
    $this->view->form = $form = new Seaocore_Form_Admin_ConfigureKeys(array('installedModules' => $installedModuleArray));

    if (!$this->getRequest()->isPost()) {
        return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
        return;
    }
    $keys = $_POST;
    $form->populate($keys);
     
    if(!empty($keys['seaocore_google_map_key'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.google.map.key', $keys['seaocore_google_map_key']);
    }

    if(!empty($keys['video_youtube_apikey'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('video.youtube.apikey', $keys['video_youtube_apikey']);
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevide.youtube.apikey', $keys['video_youtube_apikey']);
      }
    } 

    if(!empty($keys['recaptchapublic']) && !empty($keys['recaptchaprivate'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.spam.recaptchapublic', $keys['recaptchapublic']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.spam.recaptchaprivate', $keys['recaptchaprivate']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.spam.recaptchaenabled', 1);
    }

    if(!empty($keys['core_facebook_appid']) && !empty($keys['core_facebook_secret'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.facebook.appid', $keys['core_facebook_appid']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.facebook.secret', $keys['core_facebook_secret']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.facebook.enable', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('facebook.enable', 1);
    }

    if(!empty($keys['core_twitter_key']) && !empty($keys['core_twitter_secret'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.twitter.key', $keys['core_twitter_key']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.twitter.secret', $keys['core_twitter_secret']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.twitter.enable', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('twitter.enable', 1);
    }

    if(!empty($keys['core_janrain_domain']) && !empty($keys['core_janrain_key']) && !empty($keys['core_janrain_id'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.janrain.domain', $keys['core_janrain_domain']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.janrain.key', $keys['core_janrain_key']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core.janrain.id', $keys['core_janrain_id']); 
    }

    if(!empty($keys['windowlive_apikey']) && !empty($keys['windowlive_secretkey']) && !empty($keys['windowlive_policyurl'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('windowlive.apikey', $keys['windowlive_apikey']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('windowlive.secretkey', $keys['windowlive_secretkey']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('windowlive.policyurl', $keys['windowlive_policyurl']); 
    }

    if(!empty($keys['yahoo_clientId']) && !empty($keys['yahoo_clientSecret'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('yahoo.secretkey', $keys['yahoo_clientSecret']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('yahoo.apikey', $keys['yahoo_clientId']);
    }
    if(!empty($keys['linkedIn_clientSecret']) && !empty($keys['linkedIn_clientId'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('linkedin.secretkey', $keys['linkedIn_clientSecret']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('linkedin.apikey', $keys['linkedIn_clientId']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('linkedin.enable', 1);
    }
    if(!empty($keys['instagram_clientSecret']) && !empty($keys['instagram_clientId'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('instagram.secretkey', $keys['instagram_clientSecret']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('instagram.apikey', $keys['instagram_clientId']);
    }
    if(!empty($keys['google_clientId'])) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('google.apikey', $keys['google_clientId']);
    }

    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelogin')) {
      if(!empty($keys['yahoo_clientId']) && !empty($keys['yahoo_clientSecret'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.yahoo.clientSecret', $keys['yahoo_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.yahoo.clientId', $keys['yahoo_clientId']);
      } 
      if(!empty($keys['vk_clientSecret']) && !empty($keys['vk_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.vk.clientSecret', $keys['vk_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.vk.clientId', $keys['vk_clientId']);
      }
      if(!empty($keys['outlook_clientSecret']) && !empty($keys['outlook_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.outlook.clientSecret', $keys['outlook_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.outlook.clientId', $keys['outlook_clientId']);
      }
      if(!empty($keys['pinterest_clientId']) && !empty($keys['pinterest_clientSecret'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.pinterest.clientId', $keys['pinterest_clientId']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.pinterest.clientSecret', $keys['pinterest_clientSecret']);
      }
      if(!empty($keys['linkedIn_clientSecret']) && !empty($keys['linkedIn_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.linkedin.clientSecret', $keys['linkedIn_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.linkedin.clientId', $keys['linkedIn_clientId']);
      }  
      if(!empty($keys['instagram_clientSecret']) && !empty($keys['instagram_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.instagram.clientSecret', $keys['instagram_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.instagram.clientId', $keys['instagram_clientId']);
      } 
      if(!empty($keys['flickr_Secret']) && !empty($keys['flickr_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.flickr.clientSecret', $keys['flickr_Secret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.flickr.clientId', $keys['flickr_clientId']);
      } 
      if(!empty($keys['bitly_secretkey']) && !empty($keys['bitly_apikey'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('bitly.secretkey', $keys['bitly_secretkey']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('bitly.apikey', $keys['bitly_apikey']);
      }
      if(!empty($keys['google_clientSecret']) && !empty($keys['google_clientId'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.google.clientSecret', $keys['google_clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.google.clientId', $keys['google_clientId']);
      }
   } 

   $form->addNotice('Your changes have been saved.');

  }

  public function lightboxAction() {

    $coreTable = Engine_Api::_()->getDbtable('pages', 'core');
		$page_id = $this->view->page_id = 
				$coreTable->select()->from($coreTable->info('name'), 'page_id')
				->where('name = ?', 'header')
				->query()
        ->fetchColumn();
    $content_id = 0;

    if(!empty($page_id)) {
     $contentTable = Engine_Api::_()->getDbtable('content', 'core');
		 $content_id = $contentTable->select()
				->from($contentTable->info('name'), 'page_id')
				->where('page_id = ?', $page_id)
				->where('type = ?', 'widget')
				->where('name = ?', 'seaocore.seaocores-lightbox')
				->query()
        ->fetchColumn();
    }
		$this->view->content_id = $content_id;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_main_lightbox');
    $this->view->form = $form = new Seaocore_Form_Admin_Lightbox();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
      foreach( $values as $key => $value ) {
        if( $key == 'seaocore_lightbox_option_display' ) {
          Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
        }
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }

  public Function guidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_main_lightbox');
  }

  public function setUpgradeUrlAction() {
    // Build package url
    $authKeyRow = Engine_Api::_()->getDbtable('auth', 'core')->getKey(Engine_Api::_()->user()->getViewer(), 'package');
    $this->view->authKey = $authKey = $authKeyRow->id;

    //$installUrl = rtrim($this->view->baseUrl(), '/') . '/install/manage/select';

    $installUrl = rtrim($this->view->baseUrl(), '/') . '/install';
    if( strpos($this->view->url(), 'index.php') !== false ) {
      $installUrl .= '/index.php';
    }

   // $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . Engine_Api::_()->user()->getViewer()->getIdentity() . '&return=http://'. $_SERVER['HTTP_HOST'] .'/install/manage/select';
    $http = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
    $return_url = $http . $_SERVER['HTTP_HOST'] . $installUrl;
    $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . Engine_Api::_()->user()->getViewer()->getIdentity() . '&return=' . $return_url . '/manage/select';

    $this->view->installUrl = $installUrl;

    return $this->_helper->redirector->gotoUrl($installUrl, array('prependBase' => false));
  }

    
  //delete for 
  public function deleteAction()
  { 
    $this->_helper->layout->setLayout('admin-simple');
    $modules_mame = $this->_getParam('modules');
    
    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $version = $module_table->select()
            ->from($module_name, 'version')
            ->where($module_name . '.name =?', $modules_mame)
            ->limit(1)
            ->query()->fetchColumn();
    if (!empty($version)) {
				$fileName = "module-socialengineaddon-" . $version .".json" ;
		}
		
		if( !is_writeable(APPLICATION_PATH . '/application/modules/Socialengineaddon') ) {
      $this->view->meassge = 1; 
      //exit();
    }

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    if ($this->getRequest()->isPost()) {

			//calling function for remove the socialengineaddon directory.
			$pathlanguages = APPLICATION_PATH. "/application/modules/Socialengineaddon/";
      $this->rrmdir($pathlanguages);

			//Delete socialengineaddons package file.
			$pathpackgae = APPLICATION_PATH. "/application/packages/$fileName";
			if (@is_file($pathpackgae)) {
				@chmod($pathpackgae, 0777);
				unlink($pathpackgae);
			}

			//delete languagesfile
			$pathlanguages = APPLICATION_PATH. "/application/languages/en/socialengineaddon.csv";
			if (@is_file($pathlanguages)) {
				@chmod($pathlanguages, 0777);
				unlink($pathlanguages);
			}
			
			//delete table of socialengineaddon.
		  $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`menu` = "socialengineaddon_admin_main";');
			$db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "core_admin_plugins_Socialengineaddon";');
			$db->query('DROP TABLE IF EXISTS `engine4_socialengineaddons`;');
			$db->query('DROP TABLE `engine4_socialengineaddon_locations`;');
			$db->query('DROP TABLE `engine4_socialengineaddon_tabs`;');
			
			
	    $db->query('DELETE FROM `engine4_core_modules` WHERE `engine4_core_modules`.`name` = "socialengineaddon" LIMIT 1;');

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully removed the old version of “SocialApps.tech Core Plugin” from your site.'))
      ));
    }
  }
  
  //Removes directory
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$fp = opendir($dir);
			
			if ( $fp ) {
				while ($f = readdir($fp)) {
					$file = $dir . "/" . $f;
					if ($f == "." || $f == "..") {
						continue;
					}
					else if (is_dir($file) && !is_link($file)) {
						@chmod($file, 0777);
						$this->rrmdir($file);
					}
					else {
						@chmod($file, 0777);
						unlink($file);
					}
				}
				closedir($fp);
				rmdir($dir);
			}
		}
	}
  
  public function socialShareAction() {

    //SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');      
    
    $field = $this->_getParam('field');
      
    $this->view->form  = $form = new Seaocore_Form_Admin_Socialshare(array('field' => $field));
    
    if(!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }    
    
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
    }  
    
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        //'parentRefresh' => 10,
        'messages' => array('Successfully Saved !')
    ));    
    
  }
  
  //ACTION FOR DOWNLOADING THE TEMPLATE FACEBOOK CANVAS FILE
    public function downloadSampleCanvasAction() {

        //KILL ZEND'S OB
        $isGZIPEnabled = false;
        if (ob_get_level()) {
            $isGZIPEnabled = true;
            @ob_end_clean();
        }

        $path = APPLICATION_PATH . "/application/modules/Seaocore/settings/canvas_app.php";
        header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
        header("Content-Transfer-Encoding: Binary", true);
        //header("Content-Type: application/x-tar", true);
        // header("Content-Type: application/force-download", true);
        header("Content-Type: application/octet-stream", true);
        header("Content-Type: application/download", true);
        header("Content-Description: File Transfer", true);
        if (empty($isGZIPEnabled)) {
            header("Content-Length: " . filesize($path), true);
        }

        readfile("$path");

        exit();
    }

    // tablename - oldcolumnn - newcolumn - type string (used in change column)
    protected $se410ConflictColumns =  array(
      array('engine4_activity_comments', 'params', 'nestedcomment_params', 'text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL'),
      array('engine4_activity_actions', 'privacy', 'aaf_privacy', 'VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL'),
      array('engine4_core_comments', 'params', 'nestedcomment_params', 'text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL'),
    );

    public function upgradeSe410Action() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), '');
      $coreModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $activityModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('activity');
      $this->view->upgraded = Engine_Api::_()->seaocore()->checkVersion($coreModule->version, '4.10.0') && Engine_Api::_()->seaocore()->checkVersion($activityModule->version, '4.10.0');

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      if (empty($this->view->upgraded)) return;
      foreach ($this->se410ConflictColumns as $row) {
        $hasTable = $db->query("SHOW TABLES LIKE '$row[0]'")->fetch();
        if (empty($hasTable)) {continue; }
        $hasColumn = $db->query("SHOW COLUMNS FROM $row[0] LIKE '$row[2]';")->fetch();
        if ($hasColumn) {$this->view->upgraded = false; break; }
      }

    }

    public function upgradeSe410BeforeAction() {
      $this->_helper->layout->setLayout('admin-simple');
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();

      $executed = true;
      $tableColumns = $this->se410ConflictColumns;
      foreach ($tableColumns as $row) {
        $hasColumn = $db->query("SHOW COLUMNS FROM $row[0] LIKE '$row[2]';")->fetch();
        if (empty($hasColumn)) {$executed = false; break; }
      }

      if ($executed) {
        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 1000,
          'parentRefresh' => 1000,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Script has already Executed'))
        ));
      }

      if (!$this->getRequest()->isPost()) {
        return;
      }
      $values = $this->getRequest()->getPost();
      if (!isset($values['confirm'])) {return; }

      foreach ($tableColumns as $row) {
        $this->renameColumn($row[0], $row[1], $row[2]  ,$row[3]);
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'parentRefresh' => 1000,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Script Executed'))
      ));
    }

    public function upgradeSe410AfterAction() {
      $this->_helper->layout->setLayout('admin-simple');
      if (!$this->getRequest()->isPost()) {
        return;
      }
      $values = $this->getRequest()->getPost();
      if (!isset($values['confirm'])) {return; }

      $tableColumns = $this->se410ConflictColumns;
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();

      foreach ($tableColumns as $row) {
        $hasColumn1 = $db->query("SHOW COLUMNS FROM $row[0] LIKE '$row[1]';")->fetch();
        $hasColumn2 = $db->query("SHOW COLUMNS FROM $row[0] LIKE '$row[2]';")->fetch();
        if ($hasColumn1 && $hasColumn2) {
          $db->query("UPDATE `$row[0]` SET `$row[1]` = `$row[2]` WHERE `$row[2]` IS NOT NULL");
          $db->query("ALTER TABLE `$row[0]` DROP COLUMN `$row[2]`");
        }
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'parentRefresh' => 1000,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Script Executed'))
      ));
    }

    protected function renameColumn($table, $oldColumn, $newColumn, $typeString) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $hasTable = $db->query("SHOW TABLES LIKE '$table'")->fetch();
      if (empty($hasTable)) {return; }

      $hasColumn1 = $db->query("SHOW COLUMNS FROM $table LIKE '$oldColumn';")->fetch();
      $hasColumn2 = $db->query("SHOW COLUMNS FROM $table LIKE '$newColumn';")->fetch();
      if (!empty($hasColumn1) && empty($hasColumn2)) {
        $db->query("ALTER TABLE `$table` CHANGE `$oldColumn` `$newColumn` $typeString"); 
      }
    }
  }