<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Bootstrap.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        // Check if webview opened for package payment set token and event id
         $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');
        if (strstr($_SERVER['REQUEST_URI'], "/".$slug_plural."/payment") &&
                isset($_GET['token']) &&
                !empty($_GET['token']) &&
                Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid')
        ) {
            $packagePaymentSession = new Zend_Session_Namespace();
            if (!isset($packagePaymentSession->eventUserToken)) {
                if (($event_id = $_GET['event_id']) &&
                        !isset($packagePaymentSession->event_id)
                ) {
                    $packagePaymentSession->event_id = $event_id;
                }
                $packagePaymentSession->eventUserToken = $_GET['token'];
            }
        }

        // Check if webview opened for payment set token and subscription id
        if (strstr($_SERVER['REQUEST_URI'], "/payment/subscription") &&
                isset($_GET['token']) &&
                !empty($_GET['token']) &&
                Engine_Api::_()->hasModuleBootstrap('payment')
        ) {
            $apiSubscriptionSession = new Zend_Session_Namespace();
            if (!isset($apiSubscriptionSession->token)) {
                if (($subscription_id = $_GET['subscription_id']) &&
                        $subscription = Engine_Api::_()->getItem('payment_subscription', $subscription_id) &&
                        !isset($apiSubscriptionSession->subscription_id)
                ) {
                    $apiSubscriptionSession->subscription_id = $subscription_id;
                }
                $apiSubscriptionSession->token = $_GET['token'];
            }
        }
        // Check if webview opened for sitereview package payment set token and listing id
        if (strstr($_SERVER['REQUEST_URI'], "listings/payment") &&
                isset($_GET['token']) &&
                !empty($_GET['token']) &&
                Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewpaidlisting')
        ) {
            $sitereviewPaymentSession = new Zend_Session_Namespace();
            if (!isset($sitereviewPaymentSession->sitereviewToken)) {
                if (($listing_id = $_GET['listing_id']) &&
                        !isset($sitereviewPaymentSession->listing_id)
                ) {
                    $sitereviewPaymentSession->listing_id = $listing_id;
                }
                if (($listingtype_id = $_GET['listingtype_id']) &&
                        !isset($sitereviewPaymentSession->listingtype_id)
                ) {
                    $sitereviewPaymentSession->listingtype_id = $listingtype_id;
                }
                $sitereviewPaymentSession->sitereviewToken = $_GET['token'];
            }
        }
        // Check if webview opened for package payment set token and page id
        if (strstr($_SERVER['REQUEST_URI'], "/pageitems/payment") &&
                isset($_GET['token']) &&
                !empty($_GET['token']) &&
                Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)
        ) {
            $packagePaymentSession = new Zend_Session_Namespace();
            $packagePaymentSession->maintain = false;

            if (!isset($packagePaymentSession->pagetUserToken)) {
                if (($page_id = $_GET['page_id']) &&
                        !isset($packagePaymentSession->page_id)
                ) {
                    $packagePaymentSession->page_id = $page_id;
                }
                $packagePaymentSession->pagetUserToken = $_GET['token'];
            }
        }
        
        if( strstr($_SERVER['REQUEST_URI'], "/groupitems/payment") &&
      isset($_GET['token']) &&
      !empty($_GET['token']) &&
      Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.enable', 1)
    ) {
       
      $packagePaymentSession = new Zend_Session_Namespace();
      $packagePaymentSession->maintain=false;
      if( !isset($packagePaymentSession->groupUserToken) ) {
        if( ($group_id = $_GET['group_id']) &&
          !isset($packagePaymentSession->group_id)
        ) { 
          $packagePaymentSession->group_id = $group_id;
        }
        $packagePaymentSession->groupUserToken = $_GET['token'];
      }
      elseif($_GET['group_id']){
          $packagePaymentSession->group_id= $_GET['group_id'];
                  

      }
    }
    
    //store package payment work
    $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "storeitems");
    if( strstr($_SERVER['REQUEST_URI'], "/".$routeStartP."/payment") &&
      isset($_GET['token']) &&
      !empty($_GET['token']) &&
      Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)
    ) {
       
      $packagePaymentSession = new Zend_Session_Namespace();
      $packagePaymentSession->maintain=false;
      if( !isset($packagePaymentSession->storeUserToken) ) {
        if( ($store_id= $_GET['store_id']) &&
          !isset($packagePaymentSession->store_id)
        ) { 
          $packagePaymentSession->store_id = $store_id;
        }
        $packagePaymentSession->storeUserToken = $_GET['token'];
      }
      elseif($_GET['store_id']){
          $packagePaymentSession->store_id= $_GET['store_id'];
                  

      }
    }

        $this->initActionHelperPath();
        // Initialize FriendPopups helper
        Zend_Controller_Action_HelperBroker::addHelper(new Siteapi_Controller_Action_Helper_Dispatch());

        include APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license.php';
    }

    // Hide the header and footer in case of web view.
    protected function _initLayout() {
        $session = new Zend_Session_Namespace();
        if (isset($session->hideHeaderAndFooter) && !empty($session->hideHeaderAndFooter)) {

            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteandroidapp') && file_exists(APPLICATION_PATH . '/application/modules/Siteandroidapp/externals/scripts/disableHeaderFooter.js')) {
                $view->headLink()->appendStylesheet('/application/modules/Siteandroidapp/externals/styles/webView.css');
                $view->headScript()
                        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteandroidapp/externals/scripts/disableHeaderFooter.js');
            } else if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp') && file_exists(APPLICATION_PATH . '/application/modules/Siteiosapp/externals/scripts/disableHeaderFooter.js')) {
                $view->headLink()->appendStylesheet('/application/modules/Siteiosapp/externals/styles/webView.css');
                $view->headScript()
                        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteiosapp/externals/scripts/disableHeaderFooter.js');
            }
            else{
                $view->headLink()->appendStylesheet('/application/modules/Siteapi/externals/styles/webView.css');
                $view->headScript()
                        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteapi/externals/scripts/disableHeaderFooter.js');
            }
        }
    }

}
