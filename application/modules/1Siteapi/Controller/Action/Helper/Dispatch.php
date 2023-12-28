<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Dispatch.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Controller_Action_Helper_Dispatch extends Zend_Controller_Action_Helper_Abstract {
    /*
     * Delete cache in case of Browser Activities. Following hook will not call in case of API calling request.
     */

    public function preDispatch() {
        // Delete API caching. If it's enabled.
        if (Engine_Api::_()->getApi('cache', 'siteapi')->isCacheEnabled())
            $this->_deleteAvailableCache();

        // SSO work
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $isMobile = Engine_API::_()->seaocore()->isMobile();
        $session = new Zend_Session_Namespace();
        if (isset($isMobile) && !empty($isMobile)) {
            //redirect user to payment if subscription_id set
            if (isset($session->token) && isset($session->subscription_id) && !($session->maintain)) {
                if (isset($session->token) && !empty($session->token)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->token);
                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->token;
                $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                $subscriptionSession->unsetAll();
                $subscriptionSession->subscription_id = $session->subscription_id;
                $subscriptionSession->user_id = $viewer_id;
                //maintains session from looping
                $session->maintain = true;
                unset($session->subscription_id);
                unset($session->ssoNotRefresh);

                $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'siteapi', 'controller' => 'payment-api', 'action' => 'gateway'), 'default', true);
                $this->singleSignon($view, 'main');

                //redirect user to choose subscription 
            } else if (isset($session->token) && !($session->maintain)) {
                if (isset($session->token) && !empty($session->token)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->token);

                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->token;
                $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                $subscriptionSession->unsetAll();
                $subscriptionSession->user_id = $viewer_id;
                $session->maintain = true;
                unset($session->ssoNotRefresh);
                $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'siteapi', 'controller' => 'payment-api', 'action' => 'choose'), 'default', true);
                $this->singleSignon($view, 'main');

                //redirect user to single signon if token is set
            } else if (isset($session->eventUserToken) && isset($session->event_id) && !($session->maintain)) {
                if (isset($session->eventUserToken) && !empty($session->eventUserToken)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->eventUserToken);
                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->eventUserToken;
                $siteeventPayamentSession = new Zend_Session_Namespace('Payment_Siteevent');
                $siteeventPayamentSession->unsetAll();
                $siteeventPayamentSession->event_id = $session->event_id;
                $siteeventPayamentSession->user_id = $viewer_id;
                //maintains session from looping
                $session->maintain = true;
                unset($session->event_id);
                unset($session->ssoNotRefresh);
                $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'siteeventpaid', 'controller' => 'payment', 'action' => 'process'), 'default', true);
                $this->singleSignon($view, 'main');
            } else if (isset($session->sitereviewToken) && isset($session->listing_id) && !($session->maintain)) {
                if (isset($session->sitereviewToken) && !empty($session->sitereviewToken)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->sitereviewToken);
                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->sitereviewToken;
                $sitereviewPackageSession = new Zend_Session_Namespace('Payment_Sitereview');
                $sitereviewPackageSession->unsetAll();
                $sitereviewPackageSession->listing_id = $session->listing_id;
                $sitereviewPackageSession->user_id = $viewer_id;
                $listingtype_id = $session->listingtype_id;
                //maintains session from looping
                $session->maintain = true;
                unset($session->listing_id);
                unset($session->ssoNotRefresh);
                $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'sitereviewpaidlisting', 'controller' => 'payment', 'action' => 'process', 'listingtype_id' => $listingtype_id), 'default', true);
                $this->singleSignon($view, 'main');
            } else if (isset($session->pagetUserToken) && isset($session->page_id) && !($session->maintain)) {
                if (isset($session->pagetUserToken) && !empty($session->pagetUserToken)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->pagetUserToken);
                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->pagetUserToken;
                $sitepagePayamentSession = new Zend_Session_Namespace('Payment_Siteevent');
                $sitepagePayamentSession->unsetAll();
                $sitepagePayamentSession->page_id = $session->page_id;
                $sitepagePayamentSession->user_id = $viewer_id;
                //maintains session from looping
                $session->maintain = true;
                unset($session->page_id);
                unset($session->ssoNotRefresh);
                $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'sitepage', 'controller' => 'payment', 'action' => 'index', 'page_id' => $session->page_id), 'default', true);
                $this->singleSignon($view, 'main');
            }
             else if (isset($session->groupUserToken) && isset($session->group_id) && !($session->maintain)) { 
                if (isset($session->gorupUserToken) && !empty($session->groupUserToken)) {
                    $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($session->pagetUserToken);
                    if (!empty($tokenObj)) {
                        $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                        $viewer_id = $viewer->getIdentity();
                    }
                }
                $_REQUEST['disableHeaderAndFooter'] = true;
                $_REQUEST['token'] = $session->groupUserToken;
                $sitepagePayamentSession = new Zend_Session_Namespace('Payment_Sitegroup');
                $sitepagePayamentSession->unsetAll();
                $sitepagePayamentSession->group_id = $session->group_id;
                $sitepagePayamentSession->user_id = $viewer_id;
                //maintains session from looping
                $session->maintain = true;
                unset($session->group_id);
                unset($session->ssoNotRefresh);
               $_SERVER['REQUEST_URI'] = $view->url(array('module' => 'sitegroup', 'controller' => 'payment', 'action' => 'index', 'group_id' => $session->group_id), 'default', true);
                $this->singleSignon($view, 'main');
            } 
            
            else {
                $session->maintain = true;
                $this->singleSignon($view, 'main');
            }
        }

        // Validate root file.
        $getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);
        if (isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest")) {
            // Validate root file
            $isRootFileValid = Engine_Api::_()->getApi('Core', 'siteapi')->isRootFileValid();
            if (empty($isRootFileValid)) {
                echo Zend_Json::encode(array(
                    'status_code' => 401,
                    'error' => true,
                    'error_code' => 'invalid_root_file',
                    'message' => 'Root file(index.php) file not valid. Please goto admin section of the plugin and set it to correct.'
                ));
                die;
            }
        }
    }

    protected function singleSignon($view, $type = 'mobile') {
        // Single signon work.
        $session = new Zend_Session_Namespace();


        // @Todo Web view user login issue -: will update it once confirmed from android team
//        if (isset($_REQUEST['disableHeaderAndFooter']) && !empty($_REQUEST['disableHeaderAndFooter']) && !isset($session->hideHeaderAndFooter) && !$_REQUEST['token']) {
//            setcookie('app_user', 'loggedOut', time() + (86400 * 365), '/');
//        }
//
//        if (isset($_COOKIE['app_user']) && !empty($_COOKIE['app_user']) && $_COOKIE['app_user'] == 'loggedOut') {
//            $viewer = Engine_Api::_()->user()->getViewer();
//            $viewer_id = $viewer->getIdentity();
//            if (isset($viewer_id) && !empty($viewer_id))
//                setcookie('app_user', $viewer, time() + (86400 * 365), '/');
//        }
        // @Todo: Resolved folllowing in future.
        if (isset($_REQUEST['seaolocation']) && !empty($_REQUEST['seaolocation'])) {
            $specificLocationsDetails = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($_REQUEST['seaolocation']);
            $oldLocation = $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $getMyLocationDetailsCookie['location'] = $_REQUEST['seaolocation'];
            $getMyLocationDetailsCookie['latitude'] = $specificLocationsDetails->latitude;
            $getMyLocationDetailsCookie['longitude'] = $specificLocationsDetails->longitude;
            $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);

            if (isset($oldLocation) && !empty($oldLocation['location']) && ($oldLocation['location'] != $_REQUEST['seaolocation'])) {
                unset($session->hideHeaderAndFooter);
                unset($session->ssoNotRefresh);
            }
        }


        if (isset($_REQUEST['restapilocation']) && !empty($_REQUEST['restapilocation'])) {
            $specificLocationsDetails = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($_REQUEST['restapilocation']);
            $oldLocation = $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $getMyLocationDetailsCookie['location'] = $_REQUEST['restapilocation'];
            $getMyLocationDetailsCookie['latitude'] = $specificLocationsDetails->latitude;
            $getMyLocationDetailsCookie['longitude'] = $specificLocationsDetails->longitude;
            $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);

            if (isset($oldLocation) && !empty($oldLocation['location']) && ($oldLocation['location'] != $_REQUEST['restapilocation'])) {
                unset($session->hideHeaderAndFooter);
                unset($session->ssoNotRefresh);
            }
        }

        if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {
            $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($_REQUEST['token']);

            if (!empty($tokenObj)) {
                $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                $viewer_id = $viewer->getIdentity();

                $old_user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

                if ($old_user_id != $viewer_id && isset($viewer->enabled) && !empty($viewer->enabled) && isset($viewer->verified) && !empty($viewer->verified)) {
                    unset($session->hideHeaderAndFooter);
                    unset($session->ssoNotRefresh);
                }
            }
        }

        if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {
            $tokenObj = Engine_Api::_()->getDbTable('tokens', 'siteapi')->validateToken($_REQUEST['token']);
            $viewerLocal = $viewerLanguage = null;
            if (!empty($tokenObj)) {
                $viewer = Engine_Api::_()->getItem('user', $tokenObj->user_id);
                $viewer_id = $viewer->getIdentity();
                $viewerLocal = $viewer->locale;
                $viewerLanguage = $viewer->language;
            }

            if (!isset($session->ssoNotRefresh) && !empty($tokenObj))
                Zend_Auth::getInstance()->getStorage()->write($tokenObj->user_id);
        }

        // Start language and local work.
        // @TODO: In case, if we are not sending Language then set loggedin user Language.
        // @TODO: In case, If we are not sending language for loggedout user.
        if (isset($_REQUEST['language']) && !empty($_REQUEST['language']) && isset($_REQUEST['disableHeaderAndFooter']) && !empty($_REQUEST['disableHeaderAndFooter'])) {
            $locale = (isset($_REQUEST['local']) && !empty($_REQUEST['local'])) ? $_REQUEST['local'] : $_REQUEST['language'];
            $language = $_REQUEST['language'];

            // Get zend local
            if (!empty($locale)) {
                try {
                    $locale = Zend_Locale::findLocale($locale);
                } catch (Exception $e) {
                    $locale = null;
                }
            }

            // Get zend language
            if (!empty($language)) {
                try {
                    $language = Zend_Locale::findLocale($language);
                } catch (Exception $e) {
                    $language = null;
                }
            }

            // Set as cookie
            if (!empty($locale) && !empty($language)) {
                setcookie('en4_language', $language, time() + (86400 * 365), '/');
                setcookie('en4_locale', $locale, time() + (86400 * 365), '/');

                if ($viewer && $viewer->getIdentity()) {
                    $viewer->locale = $locale;
                    $viewer->language = $language;
                    $viewer->save();
                }
            }

            unset($session->ssoNotRefresh);
        }

        if (!isset($session->ssoNotRefresh)) {
            $getRequestUri = $_SERVER['REQUEST_URI'];
            $getRequestUriArray = @explode("?", $getRequestUri);
            $getQueryArray = @explode("&", $getRequestUriArray[1]);
            foreach ($getQueryArray as $key => $value) {
                if (!strstr($value, "language=")) {
                    $requestURLArray[] = $value;
                }
            }

            // Hide header and footer from the page.
            $disableHeaderFooterSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.header.disable', 0);
            if (isset($disableHeaderFooterSetting) && !empty($disableHeaderFooterSetting)) {
                $_REQUEST['disableHeaderAndFooter'] = 0;
            }

            if (isset($_REQUEST['disableHeaderAndFooter']) && !empty($_REQUEST['disableHeaderAndFooter']) && !isset($session->hideHeaderAndFooter))
                $session->hideHeaderAndFooter = true;

            $session->ssoNotRefresh = true;

            $host = _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
            $url = $host . $getRequestUriArray[0] . '?' . implode("&", $requestURLArray);
            $url = rtrim($url, '?');
            $script = <<<EOF
      window.location.href="{$url}"         
EOF;
            try {
                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
                    $view->headScriptSM()->appendScript($script);
                else
                    $view->headScript()->appendScript($script);
            } catch (Exception $ex) {
                $view->headScript()->appendScript($script);
            }
        }

        $disableHeaderFooterSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.header.disable', 0);
        if (isset($disableHeaderFooterSetting) && !empty($disableHeaderFooterSetting)) {
            $_REQUEST['disableHeaderAndFooter'] = 0;
        }
        if (isset($_REQUEST['disableHeaderAndFooter']) && !empty($_REQUEST['disableHeaderAndFooter']) && !isset($session->hideHeaderAndFooter)) {
            $session->hideHeaderAndFooter = true;

            if (!empty($tokenObj))
                Zend_Auth::getInstance()->getStorage()->write($tokenObj->user_id);

            $script = <<<EOF
  window.location.reload();          
EOF;

            try {
                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
                    $view->headScriptSM()->appendScript($script);
                else
                    $view->headScript()->appendScript($script);
            } catch (Exception $ex) {
                $view->headScript()->appendScript($script);
            }
        }
    }

    /*
     * Delete the API caching
     */

    private function _deleteAvailableCache() {
        // First check cache enabled or not
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        // Get available app modules array
        $getAPIModulesName = Engine_Api::_()->getApi('Core', 'siteapi')->getAPIModulesName();

        // Get available action name, when need to delete cache of the module.
        $getAvailableActionNames = array(
            'edit',
            'delete',
            'create',
            'close',
            'remove'
        );

        if ($module == 'forum') {
            $getAvailableActionNames[] = 'topic-create';
        }

        if (strstr('album', $module) || strstr('photo', $controller)) {
            $getAvailableActionNames[] = 'upload';
            $getAvailableActionNames[] = 'list';
        }

        if (($module == 'group') || ($module == 'event')) {
            $getAvailableActionNames[] = 'invite';
            $getAvailableActionNames[] = 'accept';
            $getAvailableActionNames[] = 'ignore';
            $getAvailableActionNames[] = 'leave';
            $getAvailableActionNames[] = 'join';
            $getAvailableActionNames[] = 'request';
            $getAvailableActionNames[] = 'cancel';
            $getAvailableActionNames[] = 'post';
            $getAvailableActionNames[] = 'sticky';
        }

        if (in_array($module, $getAPIModulesName) && in_array($action, $getAvailableActionNames)) {
            Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache();
        }
    }

}
