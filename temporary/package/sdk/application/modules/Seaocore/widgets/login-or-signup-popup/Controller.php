<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Controller.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_LoginOrSignupPopupController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->pageIdentity = join('-', array(
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));
    $notRenderPages = array('user-signup-index', 'user-auth-login', 'sitequicksignup-signup-index', 'sitelogin-signup-index', 'user-auth-forgot', 'core-error-requireuser', 'siteotpverifier-auth-login');
    if( Engine_Api::_()->user()->getViewer()->getIdentity() || in_array($this->view->pageIdentity, $notRenderPages) ) {
      $this->setNoRender();
      Zend_Registry::set('siteloginSignupPopUp', 0);
      return;
    }
    Zend_Registry::set('siteloginSignupPopUp', 1);
    $this->view->autoOpenLogin = $this->_getParam('autoOpenLogin', false);
    $this->view->autoOpenSignup = $this->_getParam('autoOpenSignup', false);
    $this->view->allowClose = $this->_getParam('allowClose', true);
    $this->view->popupVisibilty = $this->_getParam('popupVisibilty', 0);
  }

  public function getCacheKey()
  {
    return false;
  }

}