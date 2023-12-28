<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecore_AdminPluginManageController extends Core_Controller_Action_Admin
{
  protected $_replaceSkuNames;

  public function init()
  {
    $this->_replaceSkuNames = array('feedbacks' => 'feedback', 'seaddons-core' => 'seaocore', 'sponsoredstories' => 'communityadsponsored', 'groupdocumentsv4' => 'groupdocument', 'backup' => 'dbbackup', 'like' => 'sitelike', 'contactpageowners' => 'sitepageadmincontact', 'sitepageshorturl' => 'sitepageurl', 'Siteeventadmincontact' => 'siteeventadmincontact');
  }
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_licence_configure');
    $this->view->seaoDetailsForm = new Sitecore_Form_Admin_SeaoAuth();
    $this->view->redirectUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

    $this->view->seaoDetailsSession = false;
    $this->view->session = $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    if( empty($session->token) ) {
      return;
    }
    $this->view->seaoDetailsSession = true;
    $this->view->seaoToken = $session->token;
    $purchasedPlugins = $this->getPurchasedPluginsInfo($session->token);
    $this->view->purchasedPlugins = $purchasedPlugins = $this->replaceNewSku($purchasedPlugins);
    //MAKE A ARRAY OF PLUGINS WHICH HAS NEW LICENCE WORK, WE WILL JUST SAVE LSETTING FOR THOSE
    $onlyUpdateKeysPlugunList = $this->getOnlyUpdateKeysPluginList();
    $this->view->onlyUpdateKeysPlugunList = "[]";
    if(!empty($onlyUpdateKeysPlugunList)) {
      $this->view->onlyUpdateKeysPlugunList = json_encode($onlyUpdateKeysPlugunList);
    }
    $allLicensedPlugins = $this->getLsettingsModules();
    $modulesOldLicenses = $this->getOldLicenses($allLicensedPlugins);
    foreach( $modulesOldLicenses as $name => $license ) {
      if( $purchasedPlugins[$name]['license'] == $license ) {
        unset($allLicensedPlugins[$name]);
      }
    }
    $this->view->reconfigurableModules = $allLicensedPlugins;
  }

  public function saveLicenceAction()
  {
    //SAVE THE LICENCE KEY FOR SOME NEW PLUGINS WHICH HAS NEW LICENCE WORK
    $moduleName = $this->getParam('moduleName');
    $moduleLicenceKey = $this->getParam('moduleLicenceKey');
    $data = array('status' => false);
    if( !empty($moduleName) ) {
      $lsettingName = $moduleName . ".lsettings";
      Engine_Api::_()->getApi('settings', 'core')->setSetting($lsettingName, $moduleLicenceKey);
      $data['status'] = true;
    }
    return $this->_helper->json($data);
  }

  //STORE THE USER'S SEAO ACCOUNT DETAILS AND TOKEN IN THE SESSION OR IN FILE
  public function saveSeaoAuthTokenAction()
  {
    $seaoToken = $this->getParam('token');
    $seaoID = $this->getParam('id');
    $seaoName = $this->getParam('name');
    if( empty($seaoToken) ) {
      return $this->_helper->json(array('status' => 0));
    }
    $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    $session->setExpirationSeconds(86400 * 1); // 86400*1;
    $session->token = $seaoToken;
    $session->userId = $seaoID;
    $session->userName = $seaoName;
    $session->isAllowAll = $this->getParam('isAllowAll');
    return $this->_helper->json(array('status' => 1));
  }

  public function logoutAction()
  {
    $redirectUrl = $this->_getParam('redirectUrl');
    $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    if( !empty($session->token) ) {
      Zend_Session::namespaceUnset('SEAOSITE_UserAuth');
    }
    return $this->_helper->redirector->gotoUrl(urldecode($redirectUrl), array('prependBase' => false));
  }

  public function notInstalledAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_plugin_installation');
    $this->view->seaoDetailsForm = $seaoDetailsForm = new Sitecore_Form_Admin_SeaoAuth();
    $this->view->redirectUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $this->view->seaoDetailsSession = false;
    $this->view->session = $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    if( empty($session->token) ) {
      return;
    }
    $this->view->seaoDetailsSession = true;
    $this->view->seaoToken = $session->token;
    $purchasedPlugins = $this->replaceNewSku($this->getPurchasedPluginsInfo($session->token));
    $session = new Zend_Session_Namespace('PURCHASED_Licences');
    $session->licences = $purchasedPlugins;
    $validPurchasedPlugins = array();
    $purchasedPluginsTemp = array();
    foreach( $purchasedPlugins as $name => $pluginInfo ) {
      if( !empty($pluginInfo['license']) ) {
        $validPurchasedPlugins[] = $name;
        $purchasedPluginsTemp[$name] = $pluginInfo['title'];
      }
    }
    $this->view->validPurchasedPlugins = json_encode($validPurchasedPlugins);
    $installedPluginsArray = $this->getModulesArray();
    $purchasedInstalledPlugins = array_intersect_key($installedPluginsArray, $purchasedPluginsTemp);
    $purchasedNotInstalledPlugins = array_keys(array_diff_key($purchasedPluginsTemp, $purchasedInstalledPlugins));
    $purchasedInstalledPlugins = array_keys($purchasedInstalledPlugins);
    $this->view->error = false;
    $pluginInfoUrls = array('http://www.socialengineaddons.com/plugins/feed',
      'http://www.socialengineaddons.com/groupextensions/feed',
      'http://www.socialengineaddons.com/bizextensions/feed',
      'http://www.socialengineaddons.com/eventextensions/feed',
      'http://www.socialengineaddons.com/extensions/feed',
      'http://www.socialengineaddons.com/reviewextensions/feed',
      'http://www.socialengineaddons.com/themes/feed');
    foreach( $pluginInfoUrls as $url ) {
      try {

        //Zend_Feed::setHttpClient(new Zend_Http_Client(null, array('timeout' => 60, 'adapter' => 'Zend_Http_Client_Adapter_Curl')));
        $rss = Zend_Feed::import($url);
        $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
        foreach( $rss as $item ) {
          if( !$session->isAllowAll && $item->product_grade() == 'se_certifeid' ) {
            continue;
          }
          $product_type = $item->ptype();
          $license_key = $purchasedPlugins[$product_type]['license'];
          if( in_array($product_type, array('sitereaction', 'sitetagcheckin', 'sitehashtag')) ) {
            continue;
          }
          if( $item->product_grade() == 'se_un_certifeid' && in_array('certified-' . $product_type, $purchasedNotInstalledPlugins) ) {
            continue;
          }
          if( $item->product_grade() == 'se_certifeid' && (in_array(str_replace('certified-', '', $product_type), $purchasedInstalledPlugins) )) {
            continue;
          }
          if( $product_type == 'sitemobileandroidapp' )
            $product_type = 'siteandroidapp';

          if( $product_type == 'sitemobileiosapp' )
            $product_type = 'siteiosapp';
          if( in_array($product_type, $purchasedNotInstalledPlugins) ) {
            $plugin_info['title'] = $item->title();
            $plugin_info['ptype'] = $product_type;
            $plugin_info['product_version'] = $item->version();
            $plugin_info['key'] = $license_key;
            $plugin_info['name'] = $this->getModuleName($product_type);
            $notInstalledPluginsArray['items'][$plugin_info['ptype']] = $plugin_info;
          }
        }
      } catch( Exception $ee ) {
        $this->view->error = true;
      }
    }

    $this->view->notInstalledPluginsArray = $notInstalledPluginsArray['items'];
  }

  public function notActivatedAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_plugin_notactivated');
    $this->view->seaoDetailsForm = $seaoDetailsForm = new Sitecore_Form_Admin_SeaoAuth();
    $this->view->redirectUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $this->view->seaoDetailsSession = false;
    $this->view->session = $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    if( empty($session->token) ) {
      return;
    }
    $this->view->seaoDetailsSession = true;
    $this->view->seaoToken = $session->token;
    $allLicensedPluginsTitle = $this->getLsettingsModules();
    $allLicensedPluginsKeys = array_keys($this->getLsettingsModules());

    $settingsTable = Engine_Api::_()->getDbTable('settings', 'core');
    $setingsTableName = $settingsTable->info('name');
    $activatedModules = array();
    $select = $settingsTable->select()
      ->from($setingsTableName, array('name'))
      ->where($setingsTableName . '.name LIKE ?', "%isActivate")
      ->where($setingsTableName . '.value = ?', 1);
    $settingsName = $select->query()->fetchAll();
    foreach( $settingsName as $setting ) {
      $moduleName = explode('.', $setting['name'])[0];
      $activatedModules[] = $moduleName;
    }
    $notActivatedPluginsArray = array();
    $notActivatedPlugins = array_diff($allLicensedPluginsKeys, $activatedModules);
    $notHaveIsActivateSetting = $this->getNotIsActivatePluginList(); 
    foreach( $notActivatedPlugins as $name ) {
      $licenseFile = APPLICATION_PATH . '/application/modules/' . ucwords($name) . '/controllers/license/license.php';
      if( file_exists($licenseFile) && !in_array($name, $notHaveIsActivateSetting) ) {
        $notActivatedPluginsArray[$name] = $allLicensedPluginsTitle[$name];
      }  
    }
    $this->view->notActivatedPluginsArray = $notActivatedPluginsArray;
  }

  //RETURN THE MODULE NAME, REPLACE THE SKU IF IT IS DIFFERENT FROM NAME
  private function getModuleName($product_type)
  {
    $name = $product_type;
    $replaceSkuArray = array_flip($this->_replaceSkuNames);
    if( in_array($product_type, $replaceSkuArray) ) {
      $replaceSkuArray = array_flip($replaceSkuArray);
      return $replaceSkuArray[$product_type];
    }
    return $name;
  }

  //RETURN THE ARRAY WITH REPLACED SKUS
  private function replaceNewSku($purchasedPlugins)
  {
    //HERE KEY WILL BE REPLACED BY THE RESPECTIVE VALUE
    $replaceSkuNames = $this->_replaceSkuNames;
    foreach( $replaceSkuNames as $findSku => $replaceSku ) {
      if( isset($purchasedPlugins[$findSku]) ) {
        $purchasedPlugins[$replaceSku] = $purchasedPlugins[$findSku];
        unset($purchasedPlugins[$findSku]);
      }
    }
    //sitemobileiosapp and sitemobileandroidapp have some issue so we keep both the names in the array
    if( !empty($purchasedPlugins['sitemobileiosapp']) ) {
      $purchasedPlugins['siteiosapp'] = $purchasedPlugins['sitemobileiosapp'];
    }
    if( !empty($purchasedPlugins['sitemobileandroidapp']) ) {
      $purchasedPlugins['siteandroidapp'] = $purchasedPlugins['sitemobileandroidapp'];
    }
    return $purchasedPlugins;
  }

  //RETURN THE MODULE LIST, WHICH NEEDS ONLY LICENCE KEY UPDATION NOT COMPLETE SUBMISSION OF GLOBAL SETTING
  private function getOnlyUpdateKeysPluginList()
  {
    $newLsettingModules = array();
    $modulesFolder = scandir(APPLICATION_PATH . '/application/modules');
    foreach( $modulesFolder as $module ) {
      $filePath = APPLICATION_PATH . '/application/modules/' . $module . '/settings/seaocore_install.php';
      if( file_exists($filePath) ) {
        $newLsettingModules[] = strtolower($module);
      }
    }
    //WE INCLUDE FACEBOOK RELATED MODULE IN THIS LIST, BECAUSE WE JUST UPDATE LSETTING FOR IT, DO NOT SUBMIT GLOBAL SETTINGS PAGE
    $newLsettingModules = array_merge($newLsettingModules, array('facebooksepage', 'facebookse'));
    return $newLsettingModules;
  }

  private function getCertifiedPluginsList()
  {
    $certifiedPluginList = array();
    // for certified plugins...
    foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
      if( isset($data['package']['sku']) ) {
        $certifiedPluginList[] = $data['package']['name'];
      }
    }

    return array_unique($certifiedPluginList);
  }

  //RETURN THE MODULE NAMES WHICH HAS NEW SEAOCORE_INSTALL FILE
  private function getNewSeaocoreInstallModules()
  {
    $modulesFolder = scandir(APPLICATION_PATH . '/application/modules');
    $certifiedPluginList = $this->getCertifiedPluginsList();
    $newLsettingModules = array();
    // for non-certified (new license work)...
    foreach( $modulesFolder as $module ) {
      $filePath = APPLICATION_PATH . '/application/modules/' . $module . '/settings/seaocore_install.php';
      if( file_exists($filePath) ) {
        $newLsettingModules[] = strtolower($module);
      }
    }
    return array_unique(array_merge($newLsettingModules, $certifiedPluginList));
  }

  private function getNotIsActivatePluginList()
  {
    $newLsettingModules = $this->getNewSeaocoreInstallModules();
    //WE INCLUDE FACEBOOK RELATED MODULE IN THIS LIST, BECAUSE WE JUST UPDATE LSETTING FOR IT, DO NOT SUBMIT GLOBAL SETTINGS PAGE
    $newLsettingModules = array_merge($newLsettingModules, array('advancedslideshow', 'birthday', 'documentintegration', 'list', 'poke', 'siteeventadmincontact', 'siteeventdocument', 'siteeventemail', 'siteeventrepeat', 'siteeventticket', 'siteeventinvite', 'siteeventpaid', 'sitemobileapp', 'sitemobileiosapp', 'siteotpverifier', 'sitereviewpaidlisting', 'sitereviewlistingtype', 'siteslideshow', 'siteuseravatar', 'siteuserurl', 'sitevideointegration'));
    return $newLsettingModules;
  }

  private function getPurchasedPluginsInfo($token)
  {
    //FETCH THE PURCHASED PLUGIN LIST
    return Engine_Api::_()->sitecore()->getPurchasedPluginsInfo($token);
  }

  //RETURN THE PREVIOUSLY SAVED LICENCES FOR PASSED MODULES
  private function getOldLicenses($contentModuleArray)
  {
    if( empty($contentModuleArray) ) {
      return array();
    }
    $settingsTable = Engine_Api::_()->getDbTable('settings', 'core');
    $setingsTableName = $settingsTable->info('name');
    $moduleLsettingNames = array();
    $moduleLicenses = array();
    foreach( $contentModuleArray as $key => $value ) {
      $moduleLsettingNames[] = $key . ".lsettings";
    }
    $select = $settingsTable->select()
      ->from($setingsTableName, "*")
      ->where($setingsTableName . '.name IN(?)', $moduleLsettingNames);
    $lsettingInfo = $select->query()->fetchAll();
    foreach( $lsettingInfo as $info ) {
      $moduleLicenses[explode('.', $info['name'])[0]] = $info['value'];
    }
    return $moduleLicenses;
  }

  //RETURN THE ARRAY OF MODULES HAVING l-SETTINGS
  private function getLsettingsModules()
  {
    //THIS ARRAY CONTAINS THE MODULES NAME WHICH DO NOT HAVE LSETTING, BUT SOME TIME LSETTING FOUND IN CORE_SETTINGS TABLE, SO WE WILL REMOVE THIS/ OR SOME OF THESE PLUGINS ARE FREE
    $notIncludeModule = array('seaocore', 'sitecore', 'list', 'poke', 'birthday', 'birthdayemail', 'sitetagcheckin', 'sitehashtag', 'sitereaction', 'sitestoreproduct', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestorelikebox', 'sitestoreoffer', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'mapprofiletypelevel', 'sitelike', 'sitepagediscussion', 'sitepageadmincontact', 'communityadsponsored', 'sitepageurl', 'sitebusinessadmincontact', 'sitebusinessdiscussion', 'sitebusinessurl', 'siteeventadmincontact', 'sitegroupalbum', 'sitegroupmember', 'siteverify');
    $certifiedPluginList = $this->getCertifiedPluginsList(); //DO NOT SHOW CERTIFIED PLUGINS TO RECONFIGURE LICENSE
    $notIncludeModule = array_unique(array_merge($notIncludeModule, $certifiedPluginList));
    $settingsTable = Engine_Api::_()->getDbTable('settings', 'core');
    $setingsTableName = $settingsTable->info('name');
    $modulesTable = Engine_Api::_()->getDbTable('modules', 'core');
    $moduleTableName = $modulesTable->info('name');
    $licensedModules = array();
    $select = $settingsTable->select()
      ->from($setingsTableName, array('name'))
      ->where($setingsTableName . '.name LIKE ?', "%lsettings");

    $settingsName = $select->query()->fetchAll();
    foreach( $settingsName as $setting ) {
      $moduleName = explode('.', $setting['name'])[0];
      if( !in_array($moduleName, $notIncludeModule) ) {
        $modulesHavingLicence[] = $moduleName;
      }
    }
    $includeModules = $modulesHavingLicence;
    if( empty($includeModules) ) {
      return $licensedModules;
    }
    $select = $modulesTable->select()
      ->from($moduleTableName, array('name', 'title'));
    $select->where($moduleTableName . '.name IN(?)', $includeModules);
    $select->where($moduleTableName . '.enabled = ?', 1);
    $enabledModules = $select->query()->fetchAll();
    foreach( $enabledModules as $modules ) {
      $licensedModules[$modules['name']] = $modules['title'];
    }
    return $licensedModules;
  }

  //RETURN THE ARRAY OF INSTALLED MODULES
  private function getModulesArray()
  {
    $not_include = array('activity', 'authorization', 'announcement', 'messages', 'core', 'fields', 'invite', 'network', 'payment', 'storage', 'user', 'bigstep', 'sitesetup', 'seaocore');
    $modulesTable = Engine_Api::_()->getDbTable('modules', 'core');
    $moduleTableName = $modulesTable->info('name');
    $select = $modulesTable->select()
      ->from($moduleTableName, array('name', 'title'))
      ->where($moduleTableName . '.name not in(?)', $not_include);
    $contentModule = $select->query()->fetchAll();
    foreach( $contentModule as $modules ) {
      $contentModuleArray[$modules['name']] = $modules['title'];
    }
    return $contentModuleArray;
  }

}