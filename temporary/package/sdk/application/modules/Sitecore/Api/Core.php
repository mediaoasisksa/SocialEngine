<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecore_Api_Core extends Core_Api_Abstract
{

  public function getPurchasedPluginsInfo($token = null , $showAll = false)
  {
    //FETCH THE PURCHASED PLUGIN LIST
    if (empty($token)) {
      $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
      $token = $session->token;
    }
    $service = new Seaocore_Service_Curl();
    $service->setURl(base64_decode('aHR0cHM6Ly93d3cuc29jaWFsZW5naW5lYWRkb25zLmNvbS9saWNlbnNlcy9wcm9kdWN0cw=='));
    $view = Zend_Registry::get('Zend_View');
    $purchasedPlugins = $service->post(array('u' => $view->serverUrl(), 'token' => $token));
    return $purchasedPlugins;
  }

}
?>