<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Standard.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
abstract class Siteapi_Controller_Action_Standard extends Engine_Controller_Action {

    /**
     * Set response params
     * @var object
     */
    public $view;

    /**
     * HTTP HOST
     * @var string
     */
    public $getHost;

    /**
     * Get request all params
     * @var array
     */
    public $getRequestAllParams;

    /**
     * Get device id
     * @var string
     */
    public $getDeviceId = null;

    /**
     * Get device type
     * @var string
     */
    public $getDeviceType = 'all';

    /**
     * Flag variable to set dispatched.
     * @var boolean
     */
    private $_noDispatched = false;

    /**
     * Get status code
     * @var array
     */
    protected $_statusCode = array(
        'success [Remove]' => 200,
        'created' => 201,
        'no_content' => 204,
        'route_not_valid' => 400,
        'consumer_not_valid' => 400,
        'validation_fail' => 400,
        'youtube_validation_fail' => 400,
        'vimeo_validation_fail' => 400,
        'answers_not_possible' => 400,
        'oauth_parameter_missing' => 400,
        'oauth_consumer_key_not_valid' => 400,
        'oauth_consumer_disabled' => 400,
        'oauth_token_not_authorized' => 400,
        'oauth_token_revoked' => 400,
        'oauth_consumer_key_not_mapped_with_token' => 406,
        'oauth_nonce_parameter_missing' => 400,
        'oauth_invalid_signature' => 400,
        'oauth_version_not_valid' => 400,
        'oauth_nonce_used' => 400,
        'oauth_timestamp_not_valid' => 400,
        'oauth_timestamp_parameter_missing' => 400,
        'oauth_signature_method_not_valid' => 400,
        'parameter_missing' => 400,
        'profile_type_missing' => 400,
        'invalid_file_size' => 400,
        'exceed_allow_uplod_limit' => 400,
        'password_mismatch' => 400,
        'timezone_mismatch' => 400,
        'language_mismatch' => 400,
        'old_password_mismatch' => 400,
        'invalid_password' => 400,
        'invalid_method' => 400,
        'exist_in_playlist' => 400,
        'email_not_found' => 400,
        'ip_not_found' => 400,
        'ip_not_valid' => 400,
        'video_not_found' => 400,
        'username_not_found' => 400,
        'invalid_upload' => 400,
        'invalid_user_ids' => 400,
        'useCaseSensitiveActions_error' => 400,
        'urlNotvalid' => 400,
        'email_not_verified' => 401,
        'ssl_not_enabled' => 401,
        'maintenance_enabled' => 401,
        'not_approved' => 401,
        'poll_closed' => 401,
        'auth_fail' => 401,
        'user_login_default' => 401,
        'already_logged_out' => 401,
        'already_rated' => 401,
        'review_already_present' => 401,
        'already_voted' => 401,
        'already_liked' => 401,
        'already_unliked' => 401,
        'already_claimed' => 401,
        'unauthorized' => 401,
        'listing_closed' => 401,
        'listing_not_approved' => 401,
        'listing_not_published' => 401,
        'listing_not_searchable' => 401,
        'file_not_uploaded' => 401,
        'subscription_already_exist' => 401,
        'subscription_already_not_exist' => 401,
        'invalid_token' => 401,
        'subscription_fail' => 401,
        'user_already_friend' => 401,
        'user_blocked' => 401,
        'user_not_in_list' => 401,
        'friendship_disabled' => 401,
        'page_creation_quota_exceed' => 401,
        'group_creation_quota_exceed' => 401,
        'calendar_creation_quota_exceed' => 401,
        'listing_creation_quota_exceed' => 401,
        'email_not_found_or_already_registered' => 404,
        'username_not_found_or_already_registered' => 404,
        'no_record' => 404,
        'internal_server_error' => 500,
        'invalid_url' => 401,
        'siteevent_package_error' => 401,
        'sitereview_package_error' => 401,
        'sitegroup_package_error' => 401,
        'oauth_invalid_token' => 406
    );

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {

        // Set the host name
        $this->_setHost();

        // Set the device id
        $this->_setDeviceId();

        // Set the 
//        $this->_setDeviceType();
        // Set request params
        $this->_setRequestAllParams();

        // Set stdClass to set the response
        $this->_setStdClass();

        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Dispatch the requested action
     *
     * @param string $action Method name of action
     * @return void
     */
    public function dispatch($action) {
        // Notify helpers of action preDispatch state
        $this->_helper->notifyPreDispatch();
        $this->preDispatch();

        if (empty($this->_noDispatched) && !empty($action) && $this->getRequest()->isDispatched()) {
            if (null === $this->_classMethods) {
                $this->_classMethods = get_class_methods($this);
            }

            if (!($this->getResponse()->isRedirect())) {
                if ($this->getInvokeArg('useCaseSensitiveActions') || in_array($action, $this->_classMethods)) {
                    if ($this->getInvokeArg('useCaseSensitiveActions')) {
                        $this->respondWithError('useCaseSensitiveActions_error');
                    } else {
                        $this->$action();
                    }
                } else {
                    $this->__call($action, array());
                }
            }
        }

        // Notify helpers of action preDispatch state
        $this->_helper->notifyPostDispatch();
        $this->postDispatch();

        //send json response back.
        $data = Zend_Json::encode($this->view);
        $this->getResponse()->setBody($data);
    }

    /**
     * Pre-Dispatch the requested action
     */
    public function preDispatch() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $siteapiGetResponse = Zend_Registry::isRegistered('siteapiGetResponse') ? Zend_Registry::get('siteapiGetResponse') : null;
//        $fetchSSLVerification = $db->query('SELECT * FROM `engine4_core_settings` WHERE `name` LIKE \'siteapi.ssl.verification\'')->fetch();
        // In case <SSL verification enabled> and <SSL not enabled at your server>
//        if (!empty($fetchSSLVerification['value']) && !_ENGINE_SSL) {
//            $this->respondWithError('ssl_not_enabled');
//            $this->_noDispatched = true;
//            return;
//        }
        // get general config
        if (file_exists(APPLICATION_PATH . DS . 'application/settings/general.php')) {
            $generalConfig = include APPLICATION_PATH . DS . 'application/settings/general.php';
            if (!empty($generalConfig['maintenance']['enabled'])) {
                $this->respondWithError('maintenance_enabled');
                $this->_noDispatched = true;
                return;
            }
        }
        // Validate consumer key and secret
        Engine_Api::_()->getApi('oauth', 'siteapi')->isConsumerValid();
        // Getting the cache of the action.        
        $isCacheEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.caching.status', 0);
        if (!empty($siteapiGetResponse) && !empty($isCacheEnabled)) {
            $body = Engine_Api::_()->getApi('cache', 'siteapi')->getCache();
            if (!empty($body)) {
                // Set the parameters for cache response.
                $this->respondWithSuccess($body);
                $this->_noDispatched = true;
            }
        }
    }

    /**
     * Post-Dispatch the requested action
     *
     * @param string $action Method name of action
     * @return void
     */
    public function postDispatch() {
        
    }

    /**
     * Translate the language
     *
     * @return string
     */
    public function translate($language = '', $params = array()) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($language, null, $params);
    }

    /**
     * Return the requested param value according to SocialEngine.
     *
     * @param string $name requested parameter name
     * @param string $default set this value as default, if value not exist.
     * @return string | integer
     */
    public function getParam($name, $default = null) {
        return $this->_getParam($name, $default);
    }

    /**
     * Get request param
     *
     * @return false
     */
    public function getRequestParam($name, $default = null) {
        if ($this->getParam($name)) {
            return $this->getParam($name);
        } else if (isset($this->getRequestAllParams[$name])) {
            return $this->getRequestAllParams[$name];
        } else {
            return $default;
        }
    }

    /**
     * JSON Response: In case of success and have content to set in body like Content Browse Page.
     * 
     * @param object $body response body of the API calling.
     * @param boolean $setCache Set the cache of the body response.
     */
    public function respondWithSuccess($body, $setCache = true) {
        $this->view->status_code = 200;
        if (isset($body)) {
            $this->view->body = $body;
            // Set the cache
            if (!empty($setCache)) {
                if (is_string($setCache))
                    Engine_Api::_()->getApi('cache', 'siteapi')->setCache($body, $setCache);
                else
                    Engine_Api::_()->getApi('cache', 'siteapi')->setCache($body);
            }
        } else {
            $this->view->body = '';
        }

        $this->sendResponse();
    }

    /**
     * JSON Response: In case of success but don't have any content to set in body like Delete and Edit.
     * 
     * @param string $statusCode Status code
     * @param boolean $deleteCache Delete exist cache
     */
    public function successResponseNoContent($statusCode, $deleteCache = true) {
        $this->view->status_code = $statusCsode = $this->_statusCode[$statusCode];

        // Delete cache
        if (!empty($deleteCache)) {
            if (is_string($deleteCache))
                Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache($deleteCache);
            else
                Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache();
        }

        $this->sendResponse();
    }

    /**
     * JSON Response: In case of to return error.
     * 
     * @param string $statusCode Status code
     */
    public function respondWithError($statusCode, $message = null, $type = null) {
        $this->view->status_code = $this->_statusCode[$statusCode];
        $this->view->error = true;
        $this->view->error_code = $statusCode;

        if (isset($message) && !empty($message)) {
            $this->view->message = $this->translate($message);
        } else if (isset($type) && !empty($type)) {
            $this->view->message = $this->translate('You do not have permission to view this ' . $type);
        } else
            $this->view->message = $this->getMessageTemplate($statusCode);

        $this->sendResponse();
    }

    public function sendResponse() {
        ob_clean();
        // We are calling default php "json_encode" function, First check that if exist on server or not because after php 5.3.3 release there are problem that it's convert int value to string in that case iOS (Swift) not accept it because iOS has the type casting.

        if (empty($this->view->status_code) && !isset($this->view->status_code))
            $this->respondWithError('unauthorized');

        //if (function_exists('json_encode')) {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        if ((($moduleName == 'siteevent' || $moduleName == 'sitereview') &&
                ($controllerName == 'index') &&
                ($actionName == 'create' || $actionName == 'edit' || $actionName == 'manage')) || ($moduleName == 'sitegroupmember' && $controllerName == 'member' && $actionName == 'join') || ($moduleName == 'advancedactivity' && $controllerName == 'feeling' && $actionName == 'get-status-form') || ($moduleName == 'user' && $controllerName == 'signup' && $actionName == 'index')|| ($moduleName == 'messages' && $controllerName == 'messages' && $actionName == 'view') || ($moduleName == 'sitebooking') || ($moduleName == 'siteotpverifier' && $controllerName == 'auth' && $actionName == 'forgot-password') || ($moduleName == 'sitequicksignup' && $controllerName == 'signup' && $actionName == 'index')) {
            $data = @json_encode($this->view, JSON_UNESCAPED_UNICODE);
        } else {
            $data = @json_encode($this->view, JSON_NUMERIC_CHECK);
        }
        //}
        $this->_writeAppLog($data);
        if (!function_exists('json_encode') || empty($data))
            $data = Zend_Json::encode($this->view);

        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody($data);
        $this->getResponse()->sendResponse();
        exit;
    }

    /**
     * JSON Response: In case of error, If need to add parameters name custom.
     * 
     * @param string $statusCode Status code
     */
    public function respondWithValidationError($statusCode, $message = null) {
        $this->view->status_code = $this->_statusCode[$statusCode];
        $this->view->error = true;
        $this->view->error_code = $statusCode;

        if (isset($message) && !empty($message)) {
            if (is_array($message)) {
                foreach ($message as $key => $value) {
                    $messages[$key] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($value);
                }
                $message = $messages;
            } else {
                $message = $this->getMessageTemplate($statusCode) . ': ' . $message;
            }
        }

        $this->view->message = $message;

        $this->sendResponse();
    }

    /**
     * Set the method
     */
    public function setRequestMethod($method = 'GET') {
        $_SERVER['REQUEST_METHOD'] = $method;
        return;
    }

    /**
     * Validate the request method type.
     *
     * @return false
     */
    public function validateRequestMethod($method = 'GET') {
        $isValidMethod = true;
        switch ($method) {
            case 'GET':
                if (!$this->getRequest()->isGet())
                    $isValidMethod = false;
                break;
            case 'POST':
                if (!$this->getRequest()->isPost())
                    $isValidMethod = false;
                break;
            case 'PUT':
                if (!$this->getRequest()->isPut())
                    $isValidMethod = false;
                break;
            case 'DELETE':
                if (!$this->getRequest()->isDelete())
                    $isValidMethod = false;
                break;
        }

        if (empty($isValidMethod)) {
            $this->respondWithError('invalid_method');

            $this->sendResponse();
        }

        return;
    }

    /**
     * Get the response message template
     *
     * @return string
     */
    public function getMessageTemplate($errorCode = '') {
        switch ($errorCode) {
            case 'success':return $this->translate('RESPONSE_MESSAGE_SUCCESS');
                break;
            case 'validation_fail':return $this->translate('RESPONSE_MESSAGE_VALIDATION_FAIL');
                break;
            case 'youtube_validation_fail':return $this->translate('RESPONSE_MESSAGE_YOUTUBE_VALIDATION_FAIL');
                break;
            case 'vimeo_validation_fail':return $this->translate('RESPONSE_MESSAGE_VIMEO_VALIDATION_FAIL');
                break;
            case 'consumer_not_valid':return $this->translate('RESPONSE_MESSAGE_CONSUMER_NOT_VALID');
                break;
            case 'route_not_valid':return $this->translate('RESPONSE_MESSAGE_ROUTE_NOT_VALID');
                break;
            case 'answers_not_possible':return $this->translate('RESPONSE_MESSAGE_NEED_ANSWERS');
                break;
            case 'parameter_missing':return $this->translate('RESPONSE_MESSAGE_PARAMETER_MISSING');
                break;
            case 'oauth_parameter_missing':return $this->translate('RESPONSE_MESSAGE_OAUTH_PARAMETER_MISSING');
                break;
            case 'oauth_consumer_key_not_valid':return $this->translate('RESPONSE_MESSAGE_OAUTH_CONSUMER_KEY_NOT_VALID');
                break;
            case 'oauth_consumer_disabled':return $this->translate('RESPONSE_MESSAGE_OAUTH_CONSUMER_DISABLED');
                break;
            case 'oauth_token_not_authorized':return $this->translate('RESPONSE_MESSAGE_OAUTH_TOKEN_NOT_AUTHORIZED');
                break;
            case 'oauth_token_revoked':return $this->translate('RESPONSE_MESSAGE_OAUTH_TOKEN_REVOKED');
                break;
            case 'oauth_consumer_key_not_mapped_with_token':return $this->translate('RESPONSE_MESSAGE_OAUTH_CONSUMER_KEY_NOT_MAPPED_WITH_TOKEN');
                break;
            case 'oauth_nonce_parameter_missing':return $this->translate('RESPONSE_MESSAGE_OAUTH_NONCE_PARAMETER_MISSING');
                break;
            case 'oauth_invalid_token':return $this->translate('RESPONSE_MESSAGE_OAUTH_INVALID_TOKEN');
                break;
            case 'oauth_invalid_signature':return $this->translate('RESPONSE_MESSAGE_OAUTH_INVALID_SIGNATURE');
                break;
            case 'oauth_version_not_valid':return $this->translate('RESPONSE_MESSAGE_OAUTH_VERSION_NOT_VALID');
                break;
            case 'oauth_nonce_used':return $this->translate('RESPONSE_MESSAGE_OAUTH_NONCE_USED');
                break;
            case 'oauth_timestamp_not_valid':return $this->translate('RESPONSE_MESSAGE_OAUTH_TIMESTAMP_NOT_VALID');
                break;
            case 'oauth_timestamp_parameter_missing':return $this->translate('RESPONSE_MESSAGE_OAUTH_TIMESTAMP_PARAMETER_MISSING');
                break;
            case 'oauth_signature_method_not_valid':return $this->translate('RESPONSE_MESSAGE_OAUTH_SIGNATURE_METHOD_NOT_VALID');
                break;
            case 'profile_type_missing':return $this->translate('RESPONSE_MESSAGE_PROFILE_TYPE_MISSING');
                break;
            case 'invalid_file_size':return $this->translate('RESPONSE_MESSAGE_INVALID_FILE_SIZE');
                break;
            case 'exceed_allow_uplod_limit':return $this->translate('RESPONSE_MESSAGE_ALLOWED_UPLOAD_LIMIT_EXCEEDED');
                break;
            case 'password_mismatch':return $this->translate('RESPONSE_MESSAGE_PASSWORD_MISMATCH');
                break;
            case 'timezone_mismatch':return $this->translate('RESPONSE_MESSAGE_TIMEZONE_MISMATCH');
                break;
            case 'language_mismatch':return $this->translate('RESPONSE_MESSAGE_LANGUAGE_MISMATCH');
                break;
            case 'old_password_mismatch':return $this->translate('RESPONSE_MESSAGE_OLD_PASSWORD_MISMATCH');
                break;
            case 'invalid_password':return $this->translate('RESPONSE_MESSAGE_INVALID_PASSWORD');
                break;
            case 'invalid_method':return $this->translate('RESPONSE_MESSAGE_INVALID_METHOD');
                break;
            case 'exist_in_playlist':return $this->translate('RESPONSE_MESSAGE_EXIST_IN_PLAYLIST');
                break;
            case 'email_not_found':return $this->translate('RESPONSE_MESSAGE_EMAIL_NOT_FOUND');
                break;
            case 'ip_not_found':return $this->translate('RESPONSE_MESSAGE_IP_NOT_FOUND');
                break;
            case 'ip_not_valid':return $this->translate('RESPONSE_MESSAGE_IP_NOT_VALID');
                break;
            case 'video_not_found':return $this->translate('RESPONSE_MESSAGE_VIDEO_NOT_FOUND');
                break;
            case 'username_not_found':return $this->translate('RESPONSE_MESSAGE_USERNAME_NOT_FOUND');
                break;
            case 'invalid_upload':return $this->translate('RESPONSE_MESSAGE_INVALID_UPLOAD');
                break;
            case 'invalid_user_ids':return $this->translate('RESPONSE_MESSAGE_INVALID_USER_IDS');
                break;
            case 'email_not_verified':return $this->translate('RESPONSE_MESSAGE_EMAIL_UNVERIFIED');
                break;
            case 'urlNotvalid':return $this->translate("URL_NOT_VALID");
                break;
            case 'ssl_not_enabled':return $this->translate('RESPONSE_MESSAGE_SSL_NOT_ENABLED');
                break;
            case 'maintenance_enabled':return $this->translate('RESPONSE_MESSAGE_MAINTENANCE_ENABLED');
                break;
            case 'not_approved':return $this->translate('RESPONSE_MESSAGE_ACCOUNT_UNAPPROVED');
                break;
            case 'user_login_default':return $this->translate('RESPONSE_MESSAGE_USER_LOGGED_IN');
                break;
            case 'already_logged_out':return $this->translate('RESPONSE_MESSAGE_USER_LOGGED_OUT');
                break;
            case 'review_already_present':return $this->translate('RESPONSE_REVIEW_ALREADY_PRESENT_ERROR');
                break;
            case 'already_rated':return $this->translate('ALREADY_RATED_ERROR');
                break;
            case 'poll_closed':return $this->translate('RESPONSE_MESSAGE_POLL_CLOSED');
                break;
            case 'auth_fail':return $this->translate('RESPONSE_MESSAGE_AUTH_FAIL');
                break;
            case 'already_liked':return $this->translate('RESPONSE_MESSAGE_ALREADY_LIKED');
                break;
            case 'already_unliked':return $this->translate('RESPONSE_MESSAGE_ALREADY_UNLIKED');
                break;
            case 'already_claimed':return $this->translate("RESPONSE_MESSAGE_PAGE_ALREADY_CLAIMED");
                break;
            case 'already_voted':return $this->translate('RESPONSE_MESSAGE_ALREADY_VOTED');
                break;
            case 'unauthorized':return $this->translate('RESPONSE_MESSAGE_UNAUTHORIZED');
                break;
            case 'listing_closed':return $this->translate('RESPONSE_MESSAGE_LISTING_CLOSED');
                break;
            case 'listing_not_approved':return $this->translate('RESPONSE_MESSAGE_LISTING_NOT_APPROVED');
                break;
            case 'listing_not_published':return $this->translate('RESPONSE_MESSAGE_LISTING_NOT_PUBLISHED');
                break;
            case 'listing_not_searchable':return $this->translate('RESPONSE_MESSAGE_LISTING_NOT_SEARCHABLE');
                break;
            case 'file_not_uploaded':return $this->translate('RESPONSE_MESSAGE_FILE_NOT_UPLOADED');
                break;
            case 'invalid_url':return $this->translate('RESPONSE_MESSAGE_INVALID_URL');
                break;
            case 'subscription_already_exist':return $this->translate('RESPONSE_MESSAGE_SUBSCRIPTION_ALREADY_EXIST');
                break;
            case 'subscription_already_not_exist':return $this->translate('RESPONSE_MESSAGE_SUBSCRIPTION_ALREADY_CANCELLED');
                break;
            case 'invalid_token':return $this->translate('RESPONSE_MESSAGE_INVALID_TOKEN');
                break;
            case 'subscription_fail':
                Engine_Api::_()->getApi('Core', 'siteapi')->setView();
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                $url = Engine_Api::_()->getApi('Core', 'siteapi')->getHost() . $view->url(array(), 'home', true);
                return $this->translate('RESPONSE_MESSAGE_SUBSCRIPTION_FAILED', array('<a href="' . $url . '">', '</a>'));
                break;
            case 'user_already_friend':return $this->translate('RESPONSE_MESSAGE_USER_ALREADY_FRIEND');
                break;
            case 'user_blocked':return $this->translate('RESPONSE_MESSAGE_USER_BLOCKED');
                break;
            case 'user_not_in_list':return $this->translate('RESPONSE_MESSAGE_USER_NOT_FRIEND');
                break;
            case 'friendship_disabled':return $this->translate('RESPONSE_MESSAGE_FRIENDSHIP_DISABLED');
                break;
            case 'email_not_found_or_already_registered':return $this->translate('RESPONSE_MESSAGE_EMAIL_NOT_FOUND_OR_ALREADY_REGISTERED');
                break;
            case 'username_not_found_or_already_registered':return $this->translate('RESPONSE_MESSAGE_USERNAME_NOT_FOUND_OR_ALREADY_REGISTERED');
                break;
            case 'no_record':return $this->translate('RESPONSE_MESSAGE_NO_RECORD');
                break;
            case 'internal_server_error':return $this->translate('RESPONSE_MESSAGE_INTERNAL_SERVER_ERROR');
                break;
            case 'useCaseSensitiveActions_error':return $this->translate('RESPONSE_MESSAGE_USECASESENSITIVEACTION_ERROR');
                break;
            case 'siteevent_package_error':
                return $this->translate('RESPONSE_MESSAGE_PACKAGE_FAILED');
                break;
            case 'page_creation_quota_exceed':return $this->translate('RESPONSE_MESSAGE_PAGE_CREATION_QUOTA_EXCEED');
                break;
            case 'group_creation_quota_exceed':return $this->translate('RESPONSE_MESSAGE_GROUP_CREATION_QUOTA_EXCEED');
                break;
            case 'calendar_creation_quota_exceed':return $this->translate('RESPONSE_MESSAGE_CALENDAR_CREATION_QUOTA_EXCEED');
                break;
            case 'listing_creation_quota_exceed':return $this->translate('RESPONSE_MESSAGE_LISTING_CREATION_QUOTA_EXCEED');
                break;
            case 'internal_server_error':return $this->translate('RESPONSE_MESSAGE_INTERNAL_SERVER_ERROR');
                break;
            case 'sitereview_package_error':return $this->translate('RESPONSE_MESSAGE_SITEREVIEW_PACKAGE_ERROR');
                break;
            case 'sitegroup_package_error':return $this->translate('RESPONSE_MESSAGE_SITEGROUP_PACKAGE_ERROR');
                break;
        }

        return $errorCode;
    }

    /**
     * Check the form post values validation.
     *
     * @return boolean
     */
    public function isValid($data) {

        $valid = Engine_Api::_()->getApi('Validators', 'siteapi')->checkFormValidator($data);
        if (!$valid) {
            return Engine_Api::_()->getApi('Validators', 'siteapi')->getMessages();
        }

        return $valid;
    }

    /**
     * Set the host name
     */
    private function _setHost() {
        $this->getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
    }

    /**
     * Set the requested all params for all type of calling methods
     */
    private function _setRequestAllParams() {
        $this->getRequestAllParams = !empty($_REQUEST) ? $_REQUEST : array();
    }

    /**
     * Set the device id
     */
    private function _setDeviceId() {
        if (isset($_REQUEST['device_id'])) {
            $this->getDeviceId = $_REQUEST['device_id'];
        } else {
            if (function_exists('getallheaders')) {
                $getAllHeader = getallheaders();
                if (!empty($getAllHeader) && isset($getAllHeader['device_id']))
                    $this->getDeviceId = $getAllHeader['device_id'];
            }
        }
    }

    protected function _writeAppLog($data = array()) {
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
        $logArray = !empty($data) ? Zend_Json::decode($data) : Zend_Json::decode($this->view);
        $logArray['request_params'] = $this->getRequestAllParams;
        $logArray['request_url'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $logArray['error_type'] = !empty($logArray['error']) ? "API Issue" : "~~";
        $logArray['request_method'] = $_SERVER['REQUEST_METHOD'];
        $logArray['time'] = date('c');
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
        unset($logArray['body']);
        $logFileDataArray[] = $logArray;
        file_put_contents($applog_file, Zend_Json::encode($logFileDataArray));
    }

    /**
     * Set the stdClass to set the response.
     */
    private function _setStdClass() {
        $this->view = new stdClass();
    }

}
