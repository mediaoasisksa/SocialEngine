<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    https://socialapps.tech/license/
 * @version    $Id: Core.php 6590 2013-10-19 9:40:21Z SocialApps.tech $
 * @author     SocialApps.tech
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
    $service->setURl(base64_decode('aHR0cHM6Ly9zb2NpYWxhcHBzLnRlY2gvbGljZW5zZXMvcHJvZHVjdHM='));
    $view = Zend_Registry::get('Zend_View');
    $purchasedPlugins = $service->post(array('u' => $view->serverUrl(), 'token' => $token));
    return $purchasedPlugins;
  }

}
?>