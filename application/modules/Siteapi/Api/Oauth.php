<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Oauth.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Oauth extends Core_Api_Abstract {

    protected $_params;
    protected $_request;
    protected $_flagCount = 0;
    protected $_OauthKeyLenght = 32;
    protected $_requestType = 'token';
    protected $_validateOauthToken = '';
    protected $_getOauthConsumer = _GETDEFAULTOAUTHCONSUMER;
    protected $_generateRandomStringFormate = _GENERATERANDOMSTRINGFORMATE;

    /**
     * Calling from login api to get OAuth token and secret.
     */
    public function getAccessOauthToken($user) {
        $this->setConsumer();
        $tokenTable = Engine_Api::_()->getDbTable('tokens', 'siteapi');

        $select = $tokenTable->getSelect(array('consumer_id' => $this->_consumer->consumer_id, 'user_id' => $user->getIdentity()));
        $token = $tokenTable->fetchRow($select);

        if (empty($token)) {
            $token = Engine_Api::_()->getDbTable('tokens', 'siteapi')->createToken($this->_consumer->consumer_id);
            $token->user_id = $user->getIdentity();
            $token->verifier = $this->generateRandomString();
            $token->authorized = 1;
            $token->save();
        } else if (!empty($token->revoked)) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_token_revoked',
                'message' => 'RESPONSE_MESSAGE_OAUTH_TOKEN_REVOKED'
            ));
        }

        $responseTokens = array();
        $this->_params['oauth_token'] = $token->token;
        $this->_params['oauth_verifier'] = $token->verifier;

        // @Todo: We will implement signature validation checks here.
        // Reset oauth_token on every request.
        if (!empty($token)) {
            $responseTokens['token'] = $token->token;
            $responseTokens['secret'] = $token->secret;
            Engine_Api::_()->getDbtable('tokens', 'siteapi')->update(
                    array(
                'num_of_login' => ++$token->num_of_login
                    ), array(
                'token_id = ?' => $token->token_id
                    )
            );
        }

        return $responseTokens;
    }

    /**
     * Remove oauth token, whenever user will be logout or delete
     */
    public function removeAccessOauthToken($user) {
        $this->setConsumer();
        $tokenTable = Engine_Api::_()->getDbTable('tokens', 'siteapi');

        $select = $tokenTable->getSelect(array('consumer_id' => $this->_consumer->consumer_id, 'user_id' => $user->getIdentity()));
        $token = $tokenTable->fetchRow($select);
        if (!empty($token)) {
            // Decrement in number of login of user token.
            if ($token->num_of_login > 1) {
                $tokenTable->update(
                        array(
                    'num_of_login' => --$token->num_of_login
                        ), array(
                    'token_id = ?' => $token->token_id
                        )
                );
            } else {
                // Delete trhe user token.
                $tokenTable->delete(array(
                    'token_id = ?' => $token->token_id
                ));
            }
        }

        return;
    }

    /**
     * Validate oauth token and return log-in user id.
     */
    public function validateOauthToken() {
        $this->_validateOauthToken = 'auto';
        $this->setConsumer();

        if (!isset($this->_params['oauth_token']))
            return;

        $tokenTable = Engine_Api::_()->getDbTable('tokens', 'siteapi');
        $select = $tokenTable->getSelect(array('token' => $this->_params['oauth_token']));
        $token = $tokenTable->fetchRow($select);

        if (empty($token)) {
            $this->_throughError(array(
                'status_code' => 406,
                'error_code' => 'oauth_invalid_token',
                'message' => 'RESPONSE_MESSAGE_OAUTH_INVALID_TOKEN'
            ));
        }
        else
        {
                $user = Engine_Api::_()->getItem('user', $token->user_id);
                if(!$user->enabled)
                {
                    unset($this->_params['oauth_token']);
                    $this->_throughError(array(
                        'status_code' => 406,
                        'error_code' => 'oauth_invalid_token',
                        'message' => 'This account is not enabled.'
                    ));
                   
                }
        }

        if ($token->consumer_id != $this->_consumer->consumer_id) {
            $this->_throughError(array(
                'status_code' => 406,
                'error_code' => 'oauth_consumer_key_not_mapped_with_token',
                'message' => 'RESPONSE_MESSAGE_OAUTH_CONSUMER_KEY_NOT_MAPPED_WITH_TOKEN'
            ));
        }

        if (empty($token->authorized)) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_token_not_authorized',
                'message' => 'RESPONSE_MESSAGE_OAUTH_TOKEN_NOT_AUTHORIZED'
            ));
        }

        if (!empty($token->revoked)) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_token_revoked',
                'message' => 'RESPONSE_MESSAGE_OAUTH_TOKEN_REVOKED'
            ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription')) {
            $paymentTable = Engine_Api::_()->getDbTable('subscriptions', 'payment');
            $select = $paymentTable->select()
                                ->where('user_id = ?',$token->user_id)
                                ->where('active = ?', 1);
            $subscriptionRow = $paymentTable->fetchRow($select);
            if(!$subscriptionRow){

                $this->_throughError(array(
                                'status_code' => 406,
                                'error_code' => 'oauth_invalid_token',
                                'message' => 'RESPONSE_MESSAGE_NO_SUBSCRIPTION_ENABLED'
                            ));  

            }
        }

//        // @todo:
//        if($token->verifier != $this->_params['oauth_verifier']) {
//            $this->_throughError(array(
//                'status_code' => 400,
//                'error_code' => 'oauth_invalid_verifier',
//                'message' => 'RESPONSE_MESSAGE_OAUTH_INVALID_VERIFIER'
//            )); 
//        }

        return $token->user_id;
    }

    /**
     * Genrate the random tokens string.
     */
    public function generateRandomString($lenght = 32, $dummyString = '0123456789abcdefghijklmnopqrstuvwxyz') {
        $tempStr = '';
        if ($this->_generateRandomStringFormate == 'string') {
            $strLenght = @strlen($dummyString) - 1;
            for ($i = 0; $i < $lenght; $i++) {
                $tempStr .= $dummyString[mt_rand(0, $strLenght)];
            }
        }

        return $tempStr;
    }

    /**
     * Check consumer valid or not
     */
    public function isConsumerValid() {
        $this->setConsumer();
    }

    /**
     * Set the consumer and validate all params.
     */
    protected function setConsumer() {
        // Set request
        $front = Zend_Controller_Front::getInstance();
        $this->_request = $front->getRequest();
        $siteapiOauthConsumerType = Zend_Registry::isRegistered('siteapiOauthConsumerType') ? Zend_Registry::get('siteapiOauthConsumerType') : null;

        if (($this->_getOauthConsumer == 'default')) {
            // Set parameters        
            foreach ($_REQUEST as $key => $value) {
                if (strstr($key, 'oauth') && !empty($value))
                    $this->_params[$key] = $value;
            }

            if (empty($this->_params)) {
                if (function_exists('getallheaders')) {
                    $header = @getallheaders();
                    if (!empty($header)) {
                        foreach ($header as $key => $value) {
                            if (strstr($key, 'oauth') && !empty($value))
                                $this->_params[$key] = $value;
                        }
                    }
                }
            }

            // @Todo: Remove following parameters, whenever implement OAuth 1.0(a)
            $this->_params['oauth_callback'] = '';
            $this->_params['oauth_version'] = '1.0';
            $this->_params['oauth_signature_method'] = 'PLAINTEXT';
        }

        // Start params validation
        // Validate OAuth version. It should be 1.0
        if (isset($this->_params['oauth_version']) && '1.0' != $this->_params['oauth_version']) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_version_not_valid',
                'message' => 'RESPONSE_MESSAGE_OAUTH_VERSION_NOT_VALID'
            ));
        }

        // Validate requiered parameters
        if (!isset($this->_params['oauth_consumer_key']) || !isset($this->_params['oauth_signature_method'])) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_parameter_missing',
                'message' => 'RESPONSE_MESSAGE_OAUTH_PARAMETER_MISSING'
            ));
        }

        // Validate consumer key lenght.
        if (strlen($this->_params['oauth_consumer_key']) != $this->_OauthKeyLenght) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_consumer_key_not_valid',
                'message' => 'RESPONSE_MESSAGE_OAUTH_CONSUMER_KEY_NOT_VALID'
            ));
        }

        // validate signature method
        if (!in_array($this->_params['oauth_signature_method'], array('HMAC-SHA1', 'PLAINTEXT'))) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_signature_method_not_valid',
                'message' => 'RESPONSE_MESSAGE_OAUTH_SIGNATURE_METHOD_NOT_VALID'
            ));
        }

        // @Todo: When implement OAuth 1.0(3-Legged)
//        // Validate nonce data, if signature method is not PLAINTEXT
//        if (('PLAINTEXT' != $this->_params['oauth_signature_method']) && ( ++$this->_flagCount == 1)) {
//            if (($nonce = $this->_params['oauth_nonce']) && empty($nonce)) {
//                $this->_throughError(array(
//                    'status_code' => 400,
//                    'error_code' => 'oauth_nonce_parameter_missing',
//                    'message' => 'RESPONSE_MESSAGE_OAUTH_NONCE_PARAMETER_MISSING'
//                ));
//            }
//
//            if (($timestamp = (int) $this->_params['oauth_timestamp']) && empty($timestamp)) {
//                $this->_throughError(array(
//                    'status_code' => 400,
//                    'error_code' => 'oauth_timestamp_parameter_missing',
//                    'message' => 'RESPONSE_MESSAGE_OAUTH_TIMESTAMP_PARAMETER_MISSING'
//                ));
//            }
//
//            if ($timestamp <= 0 || $timestamp > (time() + 500)) {
//                $this->_throughError(array(
//                    'status_code' => 400,
//                    'error_code' => 'oauth_timestamp_not_valid',
//                    'message' => 'RESPONSE_MESSAGE_OAUTH_TIMESTAMP_NOT_VALID'
//                ));
//            }
//
//            $nonceTable = Engine_Api::_()->getDbTable('nonce', 'siteapi');
//            $select = $nonceTable->getSelect(array('nonce' => $nonce));
//            $nonceObj = $nonceTable->fetchRow($select);
//            if (!empty($nonceObj)) {
//                $this->_throughError(array(
//                    'status_code' => 400,
//                    'error_code' => 'oauth_nonce_used',
//                    'message' => 'RESPONSE_MESSAGE_OAUTH_NONCE_USED'
//                ));
//            }
//
//            Engine_Api::_()->getDbTable('nonce', 'siteapi')->insertRow(array('nonce' => $nonce, 'timestamp' => $timestamp));
//        }
        // Set consumer 
        $consumersTable = Engine_Api::_()->getDbTable('consumers', 'siteapi');
        $select = $consumersTable->getSelect(array('key' => $this->_params['oauth_consumer_key']));
        $this->_consumer = $consumersTable->fetchRow($select);

        if (empty($this->_consumer)) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_consumer_key_not_valid',
                'message' => 'RESPONSE_MESSAGE_OAUTH_CONSUMER_KEY_NOT_VALID'
            ));
        }

        if (isset($this->_consumer->status) && empty($this->_consumer->status)) {
            $this->_throughError(array(
                'status_code' => 400,
                'error_code' => 'oauth_consumer_disabled',
                'message' => 'RESPONSE_MESSAGE_OAUTH_CONSUMER_DISABLED'
            ));
        }

        return;
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


        if (!isset($this->_params['oauth_token']) || empty($this->_params['oauth_token']))
            return;

        $tokenTable = Engine_Api::_()->getDbTable('tokens', 'siteapi');
        $select = $tokenTable->getSelect(array('token' => $this->_params['oauth_token']));
        $token = $tokenTable->fetchRow($select);
        if (empty($token))
            return;

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

}

?>