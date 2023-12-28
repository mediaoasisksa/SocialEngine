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
class Sitecore_AdminSettingsController extends Core_Controller_Action_Admin
{

  public function upgradeAction()
  {
    $this->view->selectedMenuType = !empty($_GET['type']) ? $_GET['type'] : "all";
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_upgrade');
  
    $this->view->seaoDetailsForm = new Sitecore_Form_Admin_SeaoAuth();
    $this->view->redirectUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $this->view->seaoDetailsSession = false;
    $this->view->session = $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    if( empty($session->token) ) {
      return;
    }
    $this->view->seaoDetailsSession = true;
    $enabledPlugins = array();
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') )
      $enabledPlugins[] = 'sitepage';
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') )
      $enabledPlugins[] = 'sitebusiness';
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup') )
      $enabledPlugins[] = 'sitegroup';
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent') )
      $enabledPlugins[] = 'siteevent';
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview') )
      $enabledPlugins[] = 'sitereview';

    $this->view->enabledPluginsArray = $enabledPlugins;
  }

  public function newsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_news');
  }

  public function informationAction()
  {
    //RETURN PAGE NOT FOUND PAGE, AS THE INFORMATION TAB HAS BEEN DELETED
    return $this->notFoundAction();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_info');
  }

  public function upgradePluginAction()
  {
    $getTitle = @base64_decode($this->_getParam("title"));
    $this->view->title = $title = str_replace("_", "/", $getTitle);
    $this->view->key = $key = trim($this->_getParam("key"));
    $this->view->ptype = $ptype = $this->_getParam("ptype");
    $this->view->name = $name = @base64_decode($this->_getParam("name"));
    $this->view->version = $version = $this->_getParam("version");
    $this->view->calling = $calling = $this->_getParam("calling");
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check auth
    if( !$viewer || !$viewer->getIdentity() ) {
      $this->view->error = TRUE;
      $this->view->error_flag = 1;
    }
    $viewerLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->find($viewer->level_id)->current();
    if( null === $viewerLevel || $viewerLevel->flag != 'superadmin' ) {
      $this->view->error = TRUE;
      $this->view->error_flag = 1;
    }

    // Check plugin auth
    if( empty($ptype) || empty($name) || empty($version) ) {
      $this->view->error = TRUE;
    }

    $getXmlPath = $this->getXmlPath($calling);

    if( $this->getRequest()->isPost() ) {
      $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
      if( !empty($session->token) && empty($key)) {
        $purchasedPlugins = $this->getPurchasedPluginsInfo($session->token);
        if (!empty($purchasedPlugins[$ptype])) {
          $key = $purchasedPlugins[$ptype]['license'];
        }
      }
      include_once APPLICATION_PATH . '/application/modules/Sitecore/controllers/license/request.php';
      $this->view->setUpgradeUrl = TRUE;
    }
  }

  public function setUpgradeUrlAction()
  {
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

  public function getXmlPath($_calling)
  {
    switch( $_calling ) {
      case 'sitepage':
        return 'http://www.socialengineaddons.com/extensions/feed';
        break;
      case 'sitebusiness':
        return 'http://www.socialengineaddons.com/bizextensions/feed';
        break;
      default:
        return 'http://www.socialengineaddons.com/plugins/feed';
        break;
    }
  }

  private function getPurchasedPluginsInfo($token) 
  {
    return Engine_Api::_()->sitecore()->getPurchasedPluginsInfo();;
  }
}