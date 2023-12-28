<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault($event) {
        $view = $event->getPayload();
        // We have moved SSO work to Siteapi_Controller_Action_Helper_Dispatch
    }

    public function onRenderLayoutMobileSMDefault($event, $mode = null) {
        $view = $event->getPayload();
        // // We have moved SSO work to Siteapi_Controller_Action_Helper_Dispatch
    }

    public function addActivity($row) {
        $getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);

        // Delete respective oauth token
        if (isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest") && Engine_Api::_()->getApi('cache', 'siteapi')->isCacheEnabled()) {
            Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache('siteapi_activity');
            Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache('siteapi_advancedactivity');
        }
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $this->loadUserApi();
        $module_name = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        if (_ENGINE_R_TARG === 'siteapi.php') {
            //Redirect to core search if advance search is enabled
            if ($module_name == 'siteadvsearch' && $controllerName == 'index' && $actionName == 'index') {
                $request->setModuleName('core');
                $request->setControllerName('search');
                $request->setActionName('index');
            }
            
            //Redirect to Advanced Member page in case, If "Advanced Member" plugin enabled
            if (
                    Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember') && 
                    $module_name == 'user' && 
                    $controllerName == 'index' && 
                    $actionName == 'browse' && 
                    ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7') || (_IOS_VERSION && _IOS_VERSION >= '1.5.3.1'))
                    ) {
                $request->setModuleName('sitemember');
                $request->setControllerName('members');
                $request->setActionName('index');
            }
            
            //Redirect to Advanced Member search page in case, If "Advanced Member" plugin enabled
            if (
                    Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember') && 
                    $module_name == 'user' && 
                    $controllerName == 'index' && 
                    $actionName == 'search-form' && 
                    ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7') || (_IOS_VERSION && _IOS_VERSION > '1.5.3.1'))
                    ) {
                $request->setModuleName('sitemember');
                $request->setControllerName('members');
                $request->setActionName('search-form');
            }
        }
        
        if ($module_name == 'siteapi') {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'route_not_valid',
                'message' => 'RESPONSE_MESSAGE_ROUTE_NOT_VALID'
            ));
        }
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        
//        echo $module_name . ' | ' . $controllerName . ' | ' . $actionName; die;
    }

    public function onUserDeleteBefore($event) {
        $payload = $event->getPayload();
        $user_id = $payload['user_id'];
        $user = Engine_Api::_()->getItem('user', $user_id);
        $getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);

        // Delete respective oauth token
        if (isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest") && !empty($user))
            Engine_Api::_()->getApi('oauth', 'siteapi')->removeAccessOauthToken($user);
    }

    protected function loadUserApi() {
        $loader = Engine_Loader::getInstance()->load('Siteapi_Controller_Loader');
        $loader->setComponentsObject('User_Api_Siteapi_Core', 'User_Api_Core');
    }

    /**
     * Through errors
     */
    protected function _throughError($params) {
        ob_clean();
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
            
        $message = Zend_Registry::get('Zend_Translate')->translate($params['message']);
        echo Zend_Json::encode(array(
            'status_code' => $params['status_code'],
            'error' => true,
            'error_code' => $params['error_code'],
            'message' => $message
        ));
        exit;
    }

}
