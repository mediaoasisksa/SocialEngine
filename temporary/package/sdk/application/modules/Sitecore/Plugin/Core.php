<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z Seaocores $
 * @author     SocialEngineAddOns
 */
class Sitecore_Plugin_Core extends Zend_Controller_Plugin_Abstract
{

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    if( substr($request->getPathInfo(), 1, 5) == "admin" ) {
      Zend_Feed::setHttpClient(new Zend_Http_Client(
        null, array('timeout' => 60,
        'adapter' => 'Zend_Http_Client_Adapter_Curl',
        'curloptions' => array(CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_SSL_VERIFYPEER => false
      ))));

      $requestURL = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
      $unpackValuesArray = @unpack('H*', $requestURL);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('core_adminmenutype', $unpackValuesArray[1]);
    }

    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();
    $actionKey = $moduleName . '-' . $controllerName . '-' . $actionName;
    if( in_array($actionKey, array('seaocore-index-index', 'seaocore-admin-module-index', 'seaocore-admin-settings-upgrade-plugin')) ) {
      $request->setModuleName('sitecore');
      $request->setParam("module_name", 'sitecore');
    }
  }

  public function onRenderLayoutDefault($event)
  {

    //Start work for responsive theme/media query
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'externals/font-awesome/css/font-awesome.min.css');
    // This is used in Usercover photos only
    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/animate.css');


    $theme = '';
    $themeArray = $view->layout()->themes;
    if( isset($themeArray[0]) ) {
      $theme = $view->layout()->themes[0];
    }

    if( $theme == 'shoppinghub' || $theme == 'clear' || $theme == 'demotheme' || $theme == 'luminous' || $theme == 'spectacular' || $theme == 'captivate' ) {
      $view->headMeta()->setName('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
    }

    //End work for responsive theme/media query
    // Add a version check for it
    if( !Engine_Api::_()->seaocore()->hasAddedWidgetOnPage("header", "seaocore.seaocores-lightbox") ) {
      return;
    }
    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');

    $view->headTranslate(array(
      'Save', 'Cancel', 'delete',
    ));

    $fixWindowEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sea.lightbox.fixedwindow', 1);
    if( $fixWindowEnable ) {
      $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/tagger/tagger.js');
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/lightbox/fixWidthLightBox.js');
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/style_advanced_photolightbox.css');
    } else {
      $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
        ->appendFile($view->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/lightBox.js');
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/style_photolightbox.css');
    }
  }

  public function onRenderLayoutDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutMobileDefault($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutMobileDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

}