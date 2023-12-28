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
                    Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember') && $module_name == 'user' && $controllerName == 'index' && $actionName == 'browse' && ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7') || (_IOS_VERSION && _IOS_VERSION >= '1.5.3.1'))
            ) {
                $request->setModuleName('sitemember');
                $request->setControllerName('members');
                $request->setActionName('index');
            }

            //Redirect to Advanced Member search page in case, If "Advanced Member" plugin enabled
            if (
                    Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember') && $module_name == 'user' && $controllerName == 'index' && $actionName == 'search-form' && ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7') || (_IOS_VERSION && _IOS_VERSION > '1.5.3.1'))
            ) {
                $request->setModuleName('sitemember');
                $request->setControllerName('members');
                $request->setActionName('search-form');
            }

            //Queue notification work..........................
            if ((Engine_Api::_()->hasModuleBootstrap('siteiosapp') || Engine_Api::_()->hasModuleBootstrap('siteandroidapp') || Engine_Api::_()->hasModuleBootstrap('sitepushnotification')) && Engine_Api::_()->getApi('settings', 'core')->getSetting('notification.queueing', 1)) {
                $loader = Engine_Loader::getInstance();
                $className = 'Advancedactivity_Model_DbTable_Notifications';
                Engine_Loader::loadClass($className);
                if (method_exists($loader, 'setComponent')) {
                    $loader->setComponent('Activity_Model_DbTable_Notifications', new $className());
                }
            }
            //.................................................

            if ((Engine_Api::_()->hasModuleBootstrap('siteiosapp') || Engine_Api::_()->hasModuleBootstrap('siteandroidapp')) && Engine_Api::_()->hasModuleBootstrap('sitemailtemplates')) {
               
                $loader = Engine_Loader::getInstance();
                $className = 'Sitemailtemplates_Api_Mail';
                Engine_Loader::loadClass($className);
                if (method_exists($loader, 'setComponent')) {
                    $loader->setComponent('Core_Api_Mail', new $className());
                }
            }
        }

        $isTestMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.app.mode.' . _CLIENT_TYPE, 0);
        if ($module_name == 'siteapi' && !$isTestMode) {
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
        $this->_writeAppLog($params, $message);
        echo Zend_Json::encode(array(
            'status_code' => $params['status_code'],
            'error' => true,
            'error_code' => $params['error_code'],
            'message' => $message
        ));
        exit;
    }

    protected function _writeAppLog($params, $message) {
        $isTestMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.app.mode.' . _CLIENT_TYPE, 0);

        //Return if setting is disabled
        if (empty($isTestMode)) {
            return;
        }

        $isAppLogFilled = Engine_Api::_()->getApi('core', 'siteapi')->checkAppLogFile();
        //Return if app log is filled
        if (empty($isAppLogFilled)) {
            return;
        }


        $front = Zend_Controller_Front::getInstance();
        $this->_request = $front->getRequest();
        $deviceId = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.test.device.id.' . _CLIENT_TYPE, 0);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $email = '';
        if (!empty($viewer_id)) {
            $email = $viewer->email;
        }
        if (empty($isTestMode) || empty($viewer_id) || empty($email) || ($deviceId !== $email)) {
            return;
        }
        $logArray = array();
        $logArray['request_params'] = $this->_request->getParams();
        $logArray['request_url'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $logArray['request_method'] = $_SERVER['REQUEST_METHOD'];
        $logArray['time'] = date('c');
        // chmod(APPLICATION_PATH."/temporary/";, 0777);
        $applog_dir = APPLICATION_PATH . "/temporary/log";
        if (!is_dir($applog_dir)) {
            mkdir($applog_dir);
        }
        chmod($applog_dir, 0777);

        $logFile = 'Seao-' . (_CLIENT_TYPE == 'ios' ? 'iOS' : 'Android');
        $applog_file = $applog_dir . '/' . $logFile . '.log';

        if (!is_file($applog_file)) {
            touch($applog_file);
        }
        chmod($applog_file, 0777);
        $logFileData = file_get_contents($applog_file);
        $logFileDataArray = !empty($logFileData) ? Zend_Json::decode($logFileData, true) : array();
        $logArray = array_merge(array(
            'status_code' => $params['status_code'],
            'error' => true,
            'error_code' => $params['error_code'],
            'message' => $message,
            'error_type' => "API Issue"
                ), $logArray);
        $logFileDataArray[] = $logArray;
        file_put_contents($applog_file, Zend_Json::encode($logFileDataArray));
    }
    
    //save defualt settings..................
    public function onUserCreateAfter($event)
    {
        $payload = $event->getPayload();
        if( $payload instanceof User_Model_User ) {
            //Default privacy............
       $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($payload);
       Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($payload, (array) $actionTypesEnabled);
        }
    }

}
