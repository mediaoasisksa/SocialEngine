<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    SignupController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_SignupController extends Siteapi_Controller_Action_Standard {

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Validate signup form.
     * 
     * @return array
     */
    public function validationsAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $values = $_REQUEST;
        $validationMessage = $this->_validateForm($values);

        // Response form validations.
        if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
        } else {
            $this->successResponseNoContent('no_content');
        }
    }

    /**
     * Get the signup form and create user after post.
     * 
     * @return array
     */
    public function indexAction() {
        // Check if facebook details already exist.
        if (!empty($_REQUEST['facebook_uid'])) {
            $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
            $user_id = $facebookTable->select()
                    ->from($facebookTable, 'user_id')
                    ->where('facebook_uid = ?', $_REQUEST['facebook_uid'])
                    ->query()
                    ->fetchColumn();

            if (!empty($user_id))
                $this->respondWithError('facebook_uid_exist');
        }
        // Check if twitter details already exist.
        if (!empty($_REQUEST['twitter_uid'])) {
            $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
            $user_id = $twitterTable->select()
                    ->from($twitterTable, 'user_id')
                    ->where('twitter_uid = ?', $_REQUEST['twitter_uid'])
                    ->query()
                    ->fetchColumn();

            if (!empty($user_id))
                $this->respondWithError('twitter_uid_exist');
        }
        // Check if apple details already exist.
        if (!empty($_REQUEST['apple_id'])) {
            $appleTable = Engine_Api::_()->getDbtable('apple', 'user');
            $user_id = $appleTable->select()
                    ->from($appleTable, 'user_id')
                    ->where('apple_id = ?', $_REQUEST['apple_id'])
                    ->query()
                    ->fetchColumn();

            if (!empty($user_id))
                $this->respondWithError('apple_uid_exist');
        }

        // Check if gmail details already exist.
        if (!empty($_REQUEST['google_id'])) {
            $gmailTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
            $user_id = $gmailTable->select()
                    ->from($gmailTable, 'user_id')
                    ->where('google_id = ?', $_REQUEST['google_id'])
                    ->query()
                    ->fetchColumn();

            if (!empty($user_id))
                $this->respondWithError('gmail_uid_exist');
        }

        $siteapiUserSignup = Zend_Registry::isRegistered('siteapiUserSignup') ? Zend_Registry::get('siteapiUserSignup') : null;
        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.view', 0);
        $siteapiLSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.lsettings', 0);
        $siteapiInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.androiddevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.type', 0);
        $random = (Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.random', 0) == 1);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        if (empty($siteapiUserSignup) || !empty($viewer_id))
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'user')->getSignupForm());
        } else if ($this->getRequest()->isPost()) {
            $data = $_REQUEST;

            // Form validation
            $validationMessage = $this->_validateForm($data);
            $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelogin')) {
                $stepSelect =$stepTable->select()->where('class = ?', 'Sitelogin_Plugin_Signup_Photo');
            }
            else
            $stepSelect = $stepTable->select()->where('class = ?', 'User_Plugin_Signup_Photo');
            $row = $stepTable->fetchRow($stepSelect);

            if (empty($row) || empty($row->enable)) {
                $stepSelect = $stepTable->select()->where('class = ?', 'Whcore_Plugin_Signup_Photo');
                $row = $stepTable->fetchRow($stepSelect);
            }

            if (0) {
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.photo', 1)) {
                    if (empty($_FILES['photo'])) {
                        $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                        $validationMessage['photo'] = $this->translate('Please complete this field - it is required.');
                    }
                }
            }

            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if (empty($siteapiGlobalType)) {
                for ($check = 0; $check < strlen($siteapiLSettings); $check++) {
                    $tempSitemenuLtype += @ord($siteapiLSettings[$check]);
                }
                $tempSitemenuLtype = $tempSitemenuLtype + $siteapiGlobalView;
            }

            try {
                if (!empty($tempSitemenuLtype) && ($tempSitemenuLtype != $siteapiInfoType)) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting('siteapi.viewtypeinfo.type', 1);
                } else {

                    $enableOtp = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableOtp();
                    $otpSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enableotp', 1);
                    $settings = Engine_Api::_()->getApi('settings', 'core');
                    $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
                    $reqphoneno = !empty($showBothPhoneAndEmail) && $settings->getSetting('siteotpverifier.singupRequirePhone', 1);

                    if ($enableOtp && empty($showBothPhoneAndEmail)) {
                        if (!strstr($data['emailaddress'], '@')) {
                            $randomPhone = $data['phoneno'] = $data['emailaddress'];
                            $randomPhone = $this->_addRandomNo($randomPhone);
                            $autoEmailTemplate = $settings->getSetting('siteotpverifier.signupAutoEmailTemplate', 'se[PHONE_NO]@semail.com');
                            $data['email'] = str_replace('[PHONE_NO]', $randomPhone, $autoEmailTemplate);
                        } else {
                            $data['email'] = $data['emailaddress'];
                        }
                    }
                    // Save user
                    $user = $this->_saveUser($data);
                }

                if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                    // Two step verification work
                    $inputcode = $_POST['code'];
                    if (!empty($inputcode)) {
                        $code_id = Engine_Api::_()->getDbtable('codes', 'user')->isExist($inputcode, $data['email']);
                        if (!empty($code_id)) {
                            $code = Engine_Api::_()->getItem('user_code', $code_id);
                            $code->delete();
                        }
                    }
                }

                //OTp Data
                $data['user_id'] = $user->getIdentity();
                $this->otpData($data);
                unset($data['user_id']);
                //....................
                // Set photo
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'user')->setPhoto($_FILES['photo'], $user);
                
                // Set Displayname
                $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
                $user->setDisplayName($aliasValues);
                $subscriptionForm = $_REQUEST['subscriptionForm'];
                if (empty($subscriptionForm)) {
                    // Handle subscriptions
                    if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                        // Check for the user's plan
                        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                        if (!$subscriptionsTable->check($user)) {

                            // Handle default payment plan
                            $defaultSubscription = null;
                            try {
                                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                                if ($subscriptionsTable) {
                                    $defaultSubscription = $subscriptionsTable->activateDefaultPlan($user);
                                    if ($defaultSubscription) {
                                        // Re-process enabled?
                                        $user->enabled = true;
                                        $user->save();
                                    }
                                }
                            } catch (Exception $e) {
                                // Silence
                            }

                            if (!$defaultSubscription)
                                $this->respondWithError('subscription_fail');
                        }
                    }
                }

                else {
                    if (isset($_REQUEST['package_id']) && !empty($_REQUEST['package_id']))
                        $this->setPackagePlan($user, $_REQUEST['package_id']);
                }
                try {
                    $this->_profileTypemapping($user);
                } catch (Exception $ex) {
                    
                }

                // Success: forward to login api
                if (!empty($_REQUEST['facebook_uid'])) {
                    $this->_forward('facebook-login', 'signup', 'user', array(
                        'user' => $user
                    ));
                } else if (!empty($_REQUEST['twitter_uid'])) {
                    $this->_forward('twitter-login', 'signup', 'user', array(
                        'user' => $user
                    ));
                } else if (!empty($_REQUEST['google_id'])) {
                    $this->_forward('gmail-login', 'signup', 'user', array(
                      'user' => $user
                    ));
                } else if (!empty($_REQUEST['apple_id'])) {
                    $this->_forward('apple-login', 'signup', 'user', array(
                        'user' => $user
                    ));
                } else if (!empty($random)) {
                    $error = $this->translate('Thanks for joining! An email for the password has been sent to registered email ID.');
                    $this->respondWithValidationError('email_not_verified', $error);
                } else {
                    $this->_forward('login', 'auth', 'user', array(
                        'email' => $user->email,
                        'password' => $data['password'],
                        'package_id' => $_REQUEST['package_id'],
                        'ignoreOTP' => 1
                    ));
                }
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Login to facebook
     * 
     * @return array
     */
    public function facebookLoginAction() {
        $user = $this->getParam("user", null);

        //create auth token and store in database user tokens table.    
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $tokensTable = Engine_Api::_()->getDbtable('tokens', 'siteapi');
        $tokeTableSelect = $tokensTable->select()
                ->where('user_id = ?', $user->getIdentity());          // If post exists
        $userToken = $tokensTable->fetchRow($tokeTableSelect);
        if (!empty($userToken) && !empty($userToken->token)) {
            $auth_token = $userToken->token;
            $getOauthToken['token'] = $userToken->token;
            $getOauthToken['secret'] = $userToken->secret;
        } else {
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
        }
        $db = Engine_Db_Table::getDefaultAdapter();
                    // Getting IP address.
        if (isset($_REQUEST['ip']) && !empty($_REQUEST['ip'])) {
            $valid = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $_REQUEST['ip']);
            if (empty($valid))
                $this->respondWithError('ip_not_valid');

            $ipObj = new Engine_IP($_REQUEST['ip']);
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        }else {
            $this->respondWithError('ip_not_found');
        }

        $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $_REQUEST['facebook_uid'],
            'access_token' => $_REQUEST['access_token'],
            'code' => $_REQUEST['code'],
            'expires' => 0, // @todo make sure this is correct
        ));

        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];

        $device_token = !empty($_REQUEST['registration_id']) ? $_REQUEST['registration_id'] : '';
        $device_token = !empty($_REQUEST['device_token']) ? $_REQUEST['device_token'] : $device_token;
        if (!empty($_REQUEST['device_uuid']) && !empty($device_token)) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteandroidapp')) {
                Engine_Api::_()->getDbtable('gcmusers', 'siteandroidapp')->addGCMuser(array(
                    'device_uuid' => $_REQUEST['device_uuid'],
                    'registration_id' => $device_token,
                    'user_id' => $user->getIdentity()
                ));
            }
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                Engine_Api::_()->getDbtable('apnusers', 'siteiosapp')->addApnuser(array(
                    'device_uuid' => $_REQUEST['device_uuid'],
                    'token' => $device_token,
                    'user_id' => $user->getIdentity()
                ));
            }
        }

        $subscriptionForm = $_REQUEST['subscriptionForm'];
        if (empty($subscriptionForm)) {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $this->respondWithError('subscription_fail');
                }
            }
        } else {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseUrl = @trim($baseUrl, "/");
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Handle default payment plan
                    $defaultSubscription = null;
                    try {
                        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                        if ($subscriptionsTable) {
                            
                            if(_CLIENT_TYPE == 'android')
                                $defaultSubscription = $subscriptionsTable->activateDefaultPlan($user);

                            if(_CLIENT_TYPE == 'ios'){
                                unset($_REQUEST['package_id']);
                            }

                            if ($defaultSubscription) {
                                // Re-process enabled?
                                $user->enabled = true;
                                $user->save();
                            } else {
                                // Check for the user's plan
                                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                                    if (!$subscriptionsTable->check($user)) {
                                        $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                        if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                            Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user, $isIosSubscriber);
                                        }
                                    }
                                }

                                if (!$subscriptionsTable->check($user)) {
                                    // Register login
                                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                                        'user_id' => $user->getIdentity(),
                                        'email' => $email,
                                        'ip' => $_REQUEST['ip'],
                                        'timestamp' => new Zend_Db_Expr('NOW()'),
                                        'state' => 'unpaid',
                                    ));
                                    // Get package
                                    if (($packageId = $_REQUEST['package_id']) && ($package = Engine_Api::_()->getItem('payment_package', $packageId))
                                    ) {
                                        $currentSubscription = $subscriptionsTable->fetchRow(array(
                                            'user_id = ?' => $user->getIdentity(),
                                            'active = ?' => true,
                                        ));
                                        // Cancel any other existing subscriptions
                                        Engine_Api::_()->getDbtable('subscriptions', 'payment')
                                                ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);
                                        // Insert the new temporary subscription
                                        $db = $subscriptionsTable->getAdapter();
                                        $db->beginTransaction();
                                        try {
                                            $subscription = $subscriptionsTable->createRow();
                                            $subscription->setFromArray(array(
                                                'package_id' => $_REQUEST['package_id'],
                                                'user_id' => $user->getIdentity(),
                                                'status' => 'initial',
                                                'active' => false, // Will set to active on payment success
                                                'creation_date' => new Zend_Db_Expr('NOW()'),
                                            ));
                                            $subscription->save();
                                            // If the package is free, let's set it active now and cancel the other
                                            if ($package->isFree()) {
                                                $subscription->setActive(true);
                                                $subscription->onPaymentSuccess();
                                                if ($currentSubscription) {
                                                    $currentSubscription->cancel();
                                                }
                                                $user->enabled = true;
                                                $user->save();
                                            }
                                            $db->commit();
                                        } catch (Exception $e) {
                                            $db->rollBack();
                                            throw $e;
                                        }
                                        if (!$package->isFree()) {

                                            $subscription_id = $subscription->subscription_id;
                                            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                            if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                                $response['subscription_id'] = $subscription_id;
                                                $response['user_id'] = $user->getIdentity();
                                                $response['subscription'] = 1;
                                            } else {
                                                $response = $getHost . '/' . $baseUrl . "/payment/subscription/gateway?token=" . $getOauthToken['token'] . "&subscription_id=" . $subscription_id;
                                            }
                                            //RESPONSE
                                            $this->respondWithSuccess($response, true);
                                        }
                                    } else {
                                        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                        if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                            $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');
                                              
                                            $subscriptionSelect = $subscriptionsTable->select()->where('user_id = ?', $user->getIdentity())->order('subscription_id DESC')->limit(1);

                                            $subscriptionObj = $subscriptionsTable->fetchRow($subscriptionSelect);


                                            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
                                            $packagesSelect = $packagesTable
                                                    ->select()
                                                    ->from($packagesTable)
                                                    ->where('enabled = ?', true)
                                                    ->where('signup = ?', true)
                                                    ->where('package_id = ?', $subscriptionObj->package_id);
                                            $package = $packagesTable->fetchRow($packagesSelect);

                                            $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                                            $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                                            $multiOptions['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                                            $multiOptions['description'] = (string) $package->description;
                                            $multiOptions['package_id'] = $package->package_id;

                                            if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                                                $multiOptions['price'] = (double) $package->price;
                                                $multiOptions['description'] = (string) $package->description;
                                            } else {
                                                $multiOptions['price'] = (double) $package->price;
                                            }
                                            $multiOptions['currency'] = (string) $userCurrency;
                                            // trial fields work start
                                            $package_type = ($package->isFree()) ? 'free' : 'paid';
                                            $multiOptions['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                                            if($package_type != 'free'){
                                                $multiOptions['recurrence'] = $package->recurrence ;
                                                $multiOptions['recurrence_type'] = $package->recurrence_type ;
                                            }
                                            if($sitesubscriptionModuleEnable){
                                                $multiOptions['trial_duration'] = $package->trial_duration ;
                                                $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();

                                                $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                                                
                                                foreach ($featuresRowData as $feature) {
                                                    $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                                                    foreach($valueRowData as $key1 => $value1){
                                                        if($value1['value'] == null)
                                                            continue;
                                                        if($value1['package_id'] == $package->package_id)
                                                        {   
                                                            $multiOptions['trialfields'][] = $value1['value'] ;
                                                        }
                                                    }
                                                }
                                            }
                                            // trial fields work end 
        
                                            $response['user_id'] = $user->getIdentity();
                                            $response['subscription'] = 1;
                                            $response['package'] = $multiOptions;
                                        } else {
                                            $response = $getHost . '/' . $baseUrl . "/payment/subscription/choose?token=" . $getOauthToken['token'] . '&disableHeaderAndFooter=1';
                                        }
                                        //RESPONSE
                                        $this->respondWithSuccess($response, true);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Silence
                    }
                }
            }
        }

//        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);

        if (!$user->enabled) {
            if (!$user->verified) {
                $this->respondWithError('email_not_verified');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->respondWithError('not_approved');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            }
        }

        // Register login
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $user->email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'active' => true,
        ));

        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.logins');

        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($user->getIdentity()) {
            $user->lastlogin_date = date("Y-m-d H:i:s");
            $user->lastlogin_ip = $ipExpr;
            $user->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($viewer, $viewer, 'login');
        }

        if (!empty($user->verified) && !empty($user->approved) && empty($user->enabled)) {
            $user->enabled = 1;
            $user->save();
        }

        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto')) {
            $getUserCoverPhoto = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getCoverPhoto($user);
            if (!empty($getUserCoverPhoto))
                $userArray['cover'] = $getUserCoverPhoto;
        }

        //Member verification Work...............
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify'))
            $userArray["showVerifyIcon"] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getVerifyInfo($user);
        else {
            $userArray["showVerifyIcon"] = 0;
        }
        //End of Member Verification Work............................

        Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $user);

        // Send Primemessenger data & Access Token in login response
        $tabs = array();
        $pmAccessToken = '';
        $tabs['primemessenger'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getPrimemessengerTab($user);
        if ($tabs['primemessenger']) {
            $pmAccessToken = $_REQUEST['pmAccessToken'];
        }

        $this->respondWithSuccess(array(
            'oauth_token' => $getOauthToken['token'],
            'oauth_secret' => $getOauthToken['secret'],
            'user' => $userArray,
            'tabs' => $tabs,
            'pmAccessToken' => $pmAccessToken
        ));
    }

    /**
     * Login to Apple
     * 
     * @return array
     */
    public function appleLoginAction() {
        $user = $this->getRequestParam("user", null);

        //create auth token and store in database user tokens table.    
        $appleTable = Engine_Api::_()->getDbtable('apple', 'user');
        $tokensTable = Engine_Api::_()->getDbtable('tokens', 'siteapi');
        $tokeTableSelect = $tokensTable->select()
                ->where('user_id = ?', $user->getIdentity());          // If post exists
        $userToken = $tokensTable->fetchRow($tokeTableSelect);

        if (!empty($userToken) && !empty($userToken->token)) {
            $auth_token = $userToken->token;
            $getOauthToken['token'] = $userToken->token;
            $getOauthToken['secret'] = $userToken->secret;
        } else {
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
        }
        
        $db = Engine_Db_Table::getDefaultAdapter();
                    // Getting IP address.
        if (isset($_REQUEST['ip']) && !empty($_REQUEST['ip'])) {
            $valid = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $_REQUEST['ip']);
            if (empty($valid))
                $this->respondWithError('ip_not_valid');

            $ipObj = new Engine_IP($_REQUEST['ip']);
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        }else {
            $this->respondWithError('ip_not_found');
        }
        
        $appleTable->insert(array(
            'user_id' => $user->getIdentity(),
            'apple_id' => $_REQUEST['apple_id'],
        ));
        
        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];

        $device_token = !empty($_REQUEST['registration_id']) ? $_REQUEST['registration_id'] : '';
        $device_token = !empty($_REQUEST['device_token']) ? $_REQUEST['device_token'] : $device_token;
        if (!empty($_REQUEST['device_uuid']) && !empty($device_token)) {
            Engine_Api::_()->getDbtable('gcmusers', 'siteandroidapp')->addGCMuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'registration_id' => $device_token,
                'user_id' => $user->getIdentity()
            ));

            Engine_Api::_()->getDbtable('apnusers', 'siteiosapp')->addApnuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'token' => $device_token,
                'user_id' => $user->getIdentity()
            ));
        }

        $subscriptionForm = $_REQUEST['subscriptionForm'];
        if (empty($subscriptionForm)) {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $this->respondWithError('subscription_fail');
                }
            }
        } else {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseUrl = @trim($baseUrl, "/");
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Handle default payment plan
                    $defaultSubscription = null;
                    try {
                        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                        if ($subscriptionsTable) {

                            if(_CLIENT_TYPE == 'android')
                                $defaultSubscription = $subscriptionsTable->activateDefaultPlan($user);

                            if(_CLIENT_TYPE == 'ios'){
                                unset($_REQUEST['package_id']);
                            }

                            if ($defaultSubscription) {
                                // Re-process enabled?
                                $user->enabled = true;
                                $user->save();
                            } else {
                                // Check for the user's plan
                                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                                    if (!$subscriptionsTable->check($user)) {
                                        $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                        if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                            Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user, $isIosSubscriber);
                                        }
                                    }
                                }
                                if (!$subscriptionsTable->check($user)) {
                                    $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                    if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                        Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user);
                                    }
                                }

                                if (!$subscriptionsTable->check($user)) {
                                    // Get package
                                    if (($packageId = $_REQUEST['package_id']) && ($package = Engine_Api::_()->getItem('payment_package', $packageId))
                                    ) {
                                        $currentSubscription = $subscriptionsTable->fetchRow(array(
                                            'user_id = ?' => $user->getIdentity(),
                                            'active = ?' => true,
                                        ));
                                        // Cancel any other existing subscriptions
                                        Engine_Api::_()->getDbtable('subscriptions', 'payment')
                                                ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);
                                        // Insert the new temporary subscription
                                        $db = $subscriptionsTable->getAdapter();
                                        $db->beginTransaction();
                                        try {
                                            $subscription = $subscriptionsTable->createRow();
                                            $subscription->setFromArray(array(
                                                'package_id' => $_REQUEST['package_id'],
                                                'user_id' => $user->getIdentity(),
                                                'status' => 'initial',
                                                'active' => false, // Will set to active on payment success
                                                'creation_date' => new Zend_Db_Expr('NOW()'),
                                            ));
                                            $subscription->save();
                                            // If the package is free, let's set it active now and cancel the other
                                            if ($package->isFree()) {
                                                $subscription->setActive(true);
                                                $subscription->onPaymentSuccess();
                                                if ($currentSubscription) {
                                                    $currentSubscription->cancel();
                                                }
                                                $user->enabled = true;
                                                $user->save();
                                            }
                                            $db->commit();
                                        } catch (Exception $e) {
                                            $db->rollBack();
                                            throw $e;
                                        }
                                        if (!$package->isFree()) {

                                            $subscription_id = $subscription->subscription_id;
                                            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                            if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                                $response['subscription_id'] = $subscription_id;
                                                $response['user_id'] = $user->getIdentity();
                                                $response['subscription'] = 1;
                                            } else {
                                                $response = $getHost . '/' . $baseUrl . "/payment/subscription/gateway?token=" . $getOauthToken['token'] . "&subscription_id=" . $subscription_id;
                                            }
                                            //RESPONSE
                                            $this->respondWithSuccess($response, true);
                                        }
                                    } else {
                                        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                        if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                            $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');
                                              
                                            $subscriptionSelect = $subscriptionsTable->select()->where('user_id = ?', $user->getIdentity())->order('subscription_id DESC')->limit(1);

                                            $subscriptionObj = $subscriptionsTable->fetchRow($subscriptionSelect);


                                            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
                                            $packagesSelect = $packagesTable
                                                    ->select()
                                                    ->from($packagesTable)
                                                    ->where('enabled = ?', true)
                                                    ->where('signup = ?', true)
                                                    ->where('package_id = ?', $subscriptionObj->package_id);
                                            $package = $packagesTable->fetchRow($packagesSelect);

                                            $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                                            $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                                            $multiOptions['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                                            $multiOptions['description'] = (string) $package->description;
                                            $multiOptions['package_id'] = $package->package_id;

                                            if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                                                $multiOptions['price'] = (double) $package->price;
                                                $multiOptions['description'] = (string) $package->description;
                                            } else {
                                                $multiOptions['price'] = (double) $package->price;
                                            }
                                            $multiOptions['currency'] = (string) $userCurrency;
                                            // trial fields work start
                                            $package_type = ($package->isFree()) ? 'free' : 'paid';
                                            $multiOptions['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                                            if($package_type != 'free'){
                                                $multiOptions['recurrence'] = $package->recurrence ;
                                                $multiOptions['recurrence_type'] = $package->recurrence_type ;
                                            }
                                            if($sitesubscriptionModuleEnable){
                                                $multiOptions['trial_duration'] = $package->trial_duration ;
                                                $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();

                                                $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                                                
                                                foreach ($featuresRowData as $feature) {
                                                    $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                                                    foreach($valueRowData as $key1 => $value1){
                                                        if($value1['value'] == null)
                                                            continue;
                                                        if($value1['package_id'] == $package->package_id)
                                                        {   
                                                            $multiOptions['trialfields'][] = $value1['value'] ;
                                                        }
                                                    }
                                                }
                                            }
                                            // trial fields work end 
        
                                            $response['user_id'] = $user->getIdentity();
                                            $response['subscription'] = 1;
                                            $response['package'] = $multiOptions;
                                        } else {
                                            $response = $getHost . '/' . $baseUrl . "/payment/subscription/choose?token=" . $getOauthToken['token'] . '&disableHeaderAndFooter=1';
                                        }
                                        //RESPONSE
                                        $this->respondWithSuccess($response, true);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Silence
                    }
                }
            }
        }

//        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);

        if (!$user->enabled) {
            if (!$user->verified) {
                $this->respondWithError('email_not_verified');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->respondWithError('not_approved');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            }
        }

        $this->respondWithSuccess(array(
            'oauth_token' => $getOauthToken['token'],
            'oauth_secret' => $getOauthToken['secret'],
            'user' => $userArray,
        ));
    }


    public function saveProfileFields($user, $data) {
        // Profile Fields: start work to save profile fields.
        $profileTypeField = null;
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
        }

        if ($profileTypeField) {
            $profileTypeValue = $data['profile_type'];
            if ($profileTypeValue) {
                $values = Engine_Api::_()->fields()->getFieldsValues($user);
                $valueRow = $values->createRow();
                $valueRow->field_id = $profileTypeField->field_id;
                $valueRow->item_id = $user->getIdentity();
                $valueRow->value = $data['profile_type'];
                $valueRow->save();
            } else {
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                    $options = $profileTypeField->getOptions();
                    if (count($options) == 1) {
                        $values = Engine_Api::_()->fields()->getFieldsValues($user);
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $profileTypeField->field_id;
                        $valueRow->item_id = $user->getIdentity();
                        $valueRow->value = $options[0]->option_id;
                        $valueRow->save();
                    }
                }
            }

            // Save the profile fields information.
            Engine_Api::_()->getApi('Siteapi_Core', 'user')->setProfileFields($user, $data);

            // Set Displayname
            $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
            $user->setDisplayName($aliasValues);
            $user->save();
            
            
            
            if($data['profile_type'] == 13) {
        $user->level_id = 6;
        $user->save();
        
        
        
$provider = Engine_Api::_()->getItemTable('sitebooking_pro');
    $location = Engine_Api::_()->getItemTable('sitebooking_providerlocation');
    $db = $provider->getAdapter();
    $db->beginTransaction();

    try {
    
            $valuess = array();
            $valuess['title'] = $data['fname'] . ' ' . $data['lname'];
            $valuess['slug'] = "provider-" . $user->getIdentity();
            $valuess['designation'] = $data['specialist'];
            $valuess['description'] = "Consulting Service";
            $valuess['status'] =1;
            $valuess['timezone'] =$data['timezone'];
            $valuess['location'] =$data['country'];
            $valuess['city'] =$data['city'];
            $valuess['view'] ="everyone";
            $valuess['comment'] ="registered";
            $valuess['owner_id'] =$user->getIdentity();
            $valuess['approved'] =1;
            $provider = $provider->createRow();
            $provider->setFromArray($valuess);
            $provider->save();
    


          // Auth
          $auth = Engine_Api::_()->authorization()->context;
          $roles = array('owner_network', 'registered', 'everyone');
    
          $viewMax = array_search($valuess['view'], $roles);
    
          foreach( $roles as $i => $role ) {
              $auth->setAllowed($provider, $role, 'view', ($i <= $viewMax));
          }
    
          $roles = array('owner_network', 'registered', 'everyone');
    
          $viewMax = array_search($valuess['comment'], $roles);
    
          foreach( $roles as $i => $role ) {
              $auth->setAllowed($provider, $role, 'comment', ($i <= $viewMax));
          }
    $table = Engine_Api::_()->getItemTable('sitebooking_ser');

        $valuesss = array();
        $valuesss['title'] =  "Consulting Service";
        $valuesss['price'] = 100;
        $valuesss['description'] = $data['description'];
        $valuesss['slug'] = "service-" . $provider->getIdentity();
        $valuesss['category_id'] = 1; //$data['consulatant_category_id'];
        $valuesss['duration'] = 30 * 60;
        $valuesss['view'] = "everyone";
        $valuesss['comment'] ="registered";
        $valuesss['owner_id'] = $user->getIdentity();
        $valuesss['approved'] = 1;
        $valuesss['parent_type'] = 'sitebooking_pro';
        $valuesss['parent_id'] = $provider->getIdentity();
        $valuesss['status'] = 1;
        $valuesss['type'] = 1;
        $providerS = $table->createRow();
        $providerS->setFromArray($valuesss);
        $providerS->save();
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner_network', 'registered', 'everyone');
        
        $viewMax = array_search($valuesss['view'], $roles);
        
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($providerS, $role, 'view', ($i <= $viewMax));
        }
        
        $roles = array('owner_network', 'registered', 'everyone');
        
        $viewMax = array_search($valuesss['comment'], $roles);
            
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($providerS, $role, 'comment', ($i <= $viewMax));
        }
        
        
        
        
        $valuesss = array();
        $valuesss['title'] =  "Mentor Service";
        $valuesss['price'] = 300;
        $valuesss['description'] = $data['description'];
        $valuesss['slug'] = "mentor-service-" . $provider->getIdentity();
        $valuesss['category_id'] = 2; //$data['consulatant_category_id'];
        $valuesss['type'] = 2;
        $valuesss['duration'] = 30 * 60;
        $valuesss['view'] = "everyone";
        $valuesss['comment'] ="registered";
        $valuesss['owner_id'] = $user->getIdentity();
        $valuesss['approved'] = 1;
        $valuesss['parent_type'] = 'sitebooking_pro';
        $valuesss['parent_id'] = $provider->getIdentity();
        $valuesss['status'] = 1;
        $providerS = $table->createRow();
        $providerS->setFromArray($valuesss);
        $providerS->save();
      
          
          
      $db->commit();
        }
        catch (Execption $e) {
          $db->rollBack();
          }
    }
    
        }
        
        // save fields in search table
        if (isset($profileTypeField->field_id) && !empty($profileTypeField->field_id))
        {
            $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
            $searchTableName = $searchTable->info('name');
            $searchFields = $this->setFieldSearchStructure($data);
            if (isset($searchFields) && !empty($searchFields)) {
                $searchFields['profile_type'] = $profileTypeField->field_id;
                $searchFields['item_id'] = $user->getIdentity();
                $userSearchFields = $searchTable->createRow();
                $userSearchFields->setFromarray($searchFields);
                $selectQuery = $searchTable->select()
                        ->from($searchTableName, array('COUNT(1) AS exists'))
                        ->where('`item_id` = ?', $user->getIdentity());
                $result = $searchTable->fetchRow($selectQuery);
                if ($result->exists == 0) {
                    $userSearchFields->save();
                }
            }
        }

    }

    /**
     * Login to Twitter
     * 
     * @return array
     */
    public function twitterLoginAction() {
        $user = $this->getRequestParam("user", null);

        //create auth token and store in database user tokens table.    
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $tokensTable = Engine_Api::_()->getDbtable('tokens', 'siteapi');
        $tokeTableSelect = $tokensTable->select()
                ->where('user_id = ?', $user->getIdentity());          // If post exists
        $userToken = $tokensTable->fetchRow($tokeTableSelect);

        if (!empty($userToken) && !empty($userToken->token)) {
            $auth_token = $userToken->token;
            $getOauthToken['token'] = $userToken->token;
            $getOauthToken['secret'] = $userToken->secret;
        } else {
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
        }

        $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $_REQUEST['twitter_uid'],
            'twitter_token' => $_REQUEST['twitter_token'],
            'twitter_secret' => $_REQUEST['twitter_secret']
        ));

        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];

        $device_token = !empty($_REQUEST['registration_id']) ? $_REQUEST['registration_id'] : '';
        $device_token = !empty($_REQUEST['device_token']) ? $_REQUEST['device_token'] : $device_token;
        if (!empty($_REQUEST['device_uuid']) && !empty($device_token)) {
            Engine_Api::_()->getDbtable('gcmusers', 'siteandroidapp')->addGCMuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'registration_id' => $device_token,
                'user_id' => $user->getIdentity()
            ));

            Engine_Api::_()->getDbtable('apnusers', 'siteiosapp')->addApnuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'token' => $device_token,
                'user_id' => $user->getIdentity()
            ));
        }

        $subscriptionForm = $_REQUEST['subscriptionForm'];
        if (empty($subscriptionForm)) {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $this->respondWithError('subscription_fail');
                }
            }
        } else {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseUrl = @trim($baseUrl, "/");
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Handle default payment plan
                    $defaultSubscription = null;
                    try {
                        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                        if ($subscriptionsTable) {

                            if(_CLIENT_TYPE == 'android')
                                $defaultSubscription = $subscriptionsTable->activateDefaultPlan($user);

                            if(_CLIENT_TYPE == 'ios'){
                                unset($_REQUEST['package_id']);
                            }

                            if ($defaultSubscription) {
                                // Re-process enabled?
                                $user->enabled = true;
                                $user->save();
                            } else {
                                // Check for the user's plan
                                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                                    if (!$subscriptionsTable->check($user)) {
                                        $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                        if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                            Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user, $isIosSubscriber);
                                        }
                                    }
                                }
                                if (!$subscriptionsTable->check($user)) {
                                    $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                    if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                        Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user);
                                    }
                                }

                                if (!$subscriptionsTable->check($user)) {
                                    // Get package
                                    if (($packageId = $_REQUEST['package_id']) && ($package = Engine_Api::_()->getItem('payment_package', $packageId))
                                    ) {
                                        $currentSubscription = $subscriptionsTable->fetchRow(array(
                                            'user_id = ?' => $user->getIdentity(),
                                            'active = ?' => true,
                                        ));
                                        // Cancel any other existing subscriptions
                                        Engine_Api::_()->getDbtable('subscriptions', 'payment')
                                                ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);
                                        // Insert the new temporary subscription
                                        $db = $subscriptionsTable->getAdapter();
                                        $db->beginTransaction();
                                        try {
                                            $subscription = $subscriptionsTable->createRow();
                                            $subscription->setFromArray(array(
                                                'package_id' => $_REQUEST['package_id'],
                                                'user_id' => $user->getIdentity(),
                                                'status' => 'initial',
                                                'active' => false, // Will set to active on payment success
                                                'creation_date' => new Zend_Db_Expr('NOW()'),
                                            ));
                                            $subscription->save();
                                            // If the package is free, let's set it active now and cancel the other
                                            if ($package->isFree()) {
                                                $subscription->setActive(true);
                                                $subscription->onPaymentSuccess();
                                                if ($currentSubscription) {
                                                    $currentSubscription->cancel();
                                                }
                                                $user->enabled = true;
                                                $user->save();
                                            }
                                            $db->commit();
                                        } catch (Exception $e) {
                                            $db->rollBack();
                                            throw $e;
                                        }
                                        if (!$package->isFree()) {

                                            $subscription_id = $subscription->subscription_id;
                                            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                            if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                                $response['subscription_id'] = $subscription_id;
                                                $response['user_id'] = $user->getIdentity();
                                                $response['subscription'] = 1;
                                            } else {
                                                $response = $getHost . '/' . $baseUrl . "/payment/subscription/gateway?token=" . $getOauthToken['token'] . "&subscription_id=" . $subscription_id;
                                            }
                                            //RESPONSE
                                            $this->respondWithSuccess($response, true);
                                        }
                                    } else {
                                        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                        if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                            $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');
                                              
                                            $subscriptionSelect = $subscriptionsTable->select()->where('user_id = ?', $user->getIdentity())->order('subscription_id DESC')->limit(1);

                                            $subscriptionObj = $subscriptionsTable->fetchRow($subscriptionSelect);


                                            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
                                            $packagesSelect = $packagesTable
                                                    ->select()
                                                    ->from($packagesTable)
                                                    ->where('enabled = ?', true)
                                                    ->where('signup = ?', true)
                                                    ->where('package_id = ?', $subscriptionObj->package_id);
                                            $package = $packagesTable->fetchRow($packagesSelect);

                                            $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                                            $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                                            $multiOptions['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                                            $multiOptions['description'] = (string) $package->description;
                                            $multiOptions['package_id'] = $package->package_id;

                                            if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                                                $multiOptions['price'] = (double) $package->price;
                                                $multiOptions['description'] = (string) $package->description;
                                            } else {
                                                $multiOptions['price'] = (double) $package->price;
                                            }
                                            $multiOptions['currency'] = (string) $userCurrency;
                                            // trial fields work start
                                            $package_type = ($package->isFree()) ? 'free' : 'paid';
                                            $multiOptions['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                                            if($package_type != 'free'){
                                                $multiOptions['recurrence'] = $package->recurrence ;
                                                $multiOptions['recurrence_type'] = $package->recurrence_type ;
                                            }
                                            if($sitesubscriptionModuleEnable){
                                                $multiOptions['trial_duration'] = $package->trial_duration ;
                                                $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();

                                                $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                                                
                                                foreach ($featuresRowData as $feature) {
                                                    $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                                                    foreach($valueRowData as $key1 => $value1){
                                                        if($value1['value'] == null)
                                                            continue;
                                                        if($value1['package_id'] == $package->package_id)
                                                        {   
                                                            $multiOptions['trialfields'][] = $value1['value'] ;
                                                        }
                                                    }
                                                }
                                            }
                                            // trial fields work end 
        
                                            $response['user_id'] = $user->getIdentity();
                                            $response['subscription'] = 1;
                                            $response['package'] = $multiOptions;
                                        } else {
                                            $response = $getHost . '/' . $baseUrl . "/payment/subscription/choose?token=" . $getOauthToken['token'] . '&disableHeaderAndFooter=1';
                                        }
                                        //RESPONSE
                                        $this->respondWithSuccess($response, true);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Silence
                    }
                }
            }
        }

//        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);

        if (!$user->enabled) {
            if (!$user->verified) {
                $this->respondWithError('email_not_verified');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->respondWithError('not_approved');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            }
        }

        // Register login
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $user->email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'active' => true,
        ));

        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.logins');

        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($user->getIdentity()) {
            $user->lastlogin_date = date("Y-m-d H:i:s");
            $user->lastlogin_ip = $ipExpr;
            $user->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($viewer, $viewer, 'login');
        }

        if (!empty($user->verified) && !empty($user->approved) && empty($user->enabled)) {
            $user->enabled = 1;
            $user->save();
        }

        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto')) {
            $getUserCoverPhoto = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getCoverPhoto($user);
            if (!empty($getUserCoverPhoto))
                $userArray['cover'] = $getUserCoverPhoto;
        }

        //Member verification Work...............
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify'))
            $userArray["showVerifyIcon"] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getVerifyInfo($user);
        else {
            $userArray["showVerifyIcon"] = 0;
        }
        //End of Member Verification Work............................

        Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $user);

        // Send Primemessenger data & Access Token in login response
        $tabs = array();
        $pmAccessToken = '';
        $tabs['primemessenger'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getPrimemessengerTab($user);
        if ($tabs['primemessenger']) {
            $pmAccessToken = $_REQUEST['pmAccessToken'];
        }

        $this->respondWithSuccess(array(
            'oauth_token' => $getOauthToken['token'],
            'oauth_secret' => $getOauthToken['secret'],
            'user' => $userArray,
            'tabs' => $tabs,
            'pmAccessToken' => $pmAccessToken
        ));
    }

    /**
     * Validate posted signup form
     * 
     * @return array
     */
    private function _validateForm($values) {
        $validationMessage = array();
        $enableOtp = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableOtp();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
        if ($enableOtp && empty($showBothPhoneAndEmail)) {
            if (!strstr($values['emailaddress'], '@')) {
                $autoEmailTemplate = $settings->getSetting('siteotpverifier.signupAutoEmailTemplate', 'se[PHONE_NO]@semail.com');
                $randomPhone = $this->_addRandomNo($values['emailaddress']);
                $values['email'] = str_replace('[PHONE_NO]', $randomPhone, $autoEmailTemplate);
            }
        }
        // Enable: account form validation.
        $values['account_validation'] = $this->getRequestParam('account_validation', true);

        // Enable: field form validation.
        $values['fields_validation'] = $this->getRequestParam('fields_validation', true);

        // Set the default profile field id.
        if (isset($values['fields_validation']) && !empty($values['fields_validation']) && empty($values['profile_type'])) {
            $profileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileTypes();
            if (!empty($profileFields) && (COUNT($profileFields) == 1)) {
                $values['profile_type'] = @end(@array_flip($profileFields));
            }
        }

        // If enable the "Field Validations" and "Profile Type" id not exist then return the error. 
        if (!empty($values['fields_validation']) && empty($values['profile_type'])) {
            $this->_forward('throw-error', 'signup', 'user', array(
                "error_code" => "profile_type_missing"
            ));
            return;
        }

        // Getting the validator array
        $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'user')->getSignupFormValidators($values);
        $values['validators'] = $validators;
        $validationMessage = $this->isValid($values);
        if ($enableOtp && empty($showBothPhoneAndEmail)) {
            if (!strstr($values['emailaddress'], '@')) {
                if (is_array($validationMessage) && isset($validationMessage['email'])) {
                    $validationMessage['emailaddress'] = $validationMessage['email'];
                    unset($validationMessage['email']);
                }
            }
        }
        if (isset($_REQUEST['terms']) && empty($_REQUEST['terms'])) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
            $validationMessage['terms'] = $this->translate('Please complete this field - it is required.');
        }

        if (!empty($_REQUEST['facebook_uid'])) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();

            $facebook_uid = $this->getRequestParam('facebook_uid', null);
            if (empty($facebook_uid))
                $validationMessage['facebook_uid'] = 'Please complete this field - it is required.';

            $access_token = $this->getRequestParam('access_token', null);
            if (empty($access_token))
                $validationMessage['access_token'] = 'Please complete this field - it is required.';

            $code = $this->getRequestParam('code', null);
            if (empty($code))
                $validationMessage['code'] = 'Please complete this field - it is required.';
        }

        if (!empty($_REQUEST['twitter_uid'])) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();

            $twitter_uid = $this->getRequestParam('twitter_uid', null);
            if (empty($twitter_uid))
                $validationMessage['twitter_uid'] = 'Please complete this field - it is required.';

            $twitter_token = $this->getRequestParam('twitter_token', null);
            if (empty($twitter_token))
                $validationMessage['twitter_token'] = 'Please complete this field - it is required.';

            $twitter_secret = $this->getRequestParam('twitter_secret', null);
            if (empty($twitter_secret))
                $validationMessage['twitter_secret'] = 'Please complete this field - it is required.';
        }
        if (!empty($_REQUEST['apple_id'])) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();

            $apple_id = $this->getRequestParam('apple_id', null);
            if (empty($apple_id))
                $validationMessage['apple_id'] = 'Please complete this field - it is required.';
        }


        if (!empty($_REQUEST['google_id'])) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();

            $google_id = $this->getRequestParam('google_id', null);
            if (empty($google_id))
                $validationMessage['google_id'] = 'Please complete this field - it is required.';
        }

        /*
         * Start: manual signup form validations.
         */

        if (empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id']) && $random) {
            // Validate: password and confirm password.
            if (!empty($values['password']) && !empty($values['passconf']) && ($values['password'] != $values['passconf'])) {
                $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                $validationMessage['passconf'] = $this->getMessageTemplate('password_mismatch');
            }
        }

        // Validate: timezone
        if (!empty($values['timezone'])) {
            $timeZone = Engine_Api::_()->getApi('Siteapi_Core', 'user')->_getTimeZone;
            if (!array_key_exists($values['timezone'], $timeZone)) {
                $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                $validationMessage['timezone'] = $this->getMessageTemplate('timezone_mismatch');
            }
        }

        $key = $this->phoneNoValidation($values);
        if (!empty($key)) {
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
            $validationMessage[$key] = $this->translate('Someone is already registered with this Phone Number. Please try with another number.');
        }

        return $validationMessage;
    }

    /**
     * Create user using posted values.
     * 
     * @return array
     */
    private function _saveUser($data) {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $random = ($settings->getSetting('user.signup.random', 0) == 1);
        $emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
        if ($emailadmin) {
            // the signup notification is emailed to the first SuperAdmin by default
            $users_table = Engine_Api::_()->getDbtable('users', 'user');
            $users_select = $users_table->select()
                    ->where('level_id = ?', 1)
                    ->where('enabled >= ?', 1);
            $super_admin = $users_table->fetchRow($users_select);
        }

        if (empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id']) && $random) {
            $data['password'] = Engine_Api::_()->user()->randomPass(10);
        }

        if (isset($data['language'])) {
            $data['locale'] = $data['language'];
        }

        // Create user
        $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
        $user->setFromArray($data);
        $user->save();

        $this->saveProfileFields($user, $data);

        // Increment signup counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

        if ($user->verified && $user->approved) {
            // Create activity for them
            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
            // Set user as logged in if not have to verify email
            Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
        }

        $mailType = null;
        $mailParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => time(),
            'recipient_title' => $user->getTitle(),
            'recipient_link' => $user->getHref(),
            'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
            'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        );

        // Add password to email if necessary
        if (empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id']) && $random) {
            $mailParams['password'] = $data['password'];
        }

        // Mail stuff
        switch ($settings->getSetting('user.signup.verifyemail', 0)) {
            case 0:
                // only override admin setting if random passwords are being created
                if ($random) {
                    $mailType = 'core_welcome_password';
                }
                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';

                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => date("F j, Y, g:i a"),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->displayname,
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            case 1:
                // send welcome email
                $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';

                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => date("F j, Y, g:i a"),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->getTitle(),
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            case 2:
            case 3:
                // verify email before enabling account
                $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
                $verify_row = $verify_table->createRow();
                $verify_row->user_id = $user->getIdentity();
                $verify_row->code = md5($user->email
                        . $user->creation_date
                        . $settings->getSetting('core.secret', 'staticSalt')
                        . (string) rand(1000000, 9999999));
                $verify_row->date = $user->creation_date;
                $verify_row->save();

                $token = base64_encode(time() . ":" . $user->getIdentity());
                $mailType = ($random ? 'core_verification_password' : 'core_verification');

                $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'action' => 'verify',
                    'email' => $user->email,
                    'token' => $token,
                    'verify' => $verify_row->code
                        ), 'user_signup', true);

                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';

                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => date("F j, Y, g:i a"),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->getTitle(),
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            default:
                // do nothing
                break;
        }

        if (!empty($mailType)) {
            // Moved to User_Plugin_Signup_Fields
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                    $user, $mailType, $mailParams
            );
        }

        if (!empty($mailAdminType)) {
            // Moved to User_Plugin_Signup_Fields
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                    $user, $mailAdminType, $mailAdminParams
            );
        }
        return $user;
    }

    /**
     *  @param Get Field Structure 
     *  @param Get Form Data 
     * @return User _Search Data here.  
     */
    public function setFieldSearchStructure($result) {
        foreach ($result as $key => $value) {
            if (strstr($key, '_first_name')) {
                $fieldStructure['first_name'] = $value;
            } else if (strstr($key, '_last_name')) {
                $fieldStructure['last_name'] = $value;
            } else if (strstr($key, '_gender')) {
                $fieldStructure['gender'] = $value;
            } else if (strstr($key, '_birthdate')) {
                $fieldStructure['birthdate'] = $value;
            }
        }
        return $fieldStructure;
    }

     /**
     * Login to Gmail
     * 
     * @return array
     */
    public function gmailLoginAction() {
        $user = $this->getRequestParam("user", null);

        //create auth token and store in database user tokens table.    
        $gmailTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
        $tokensTable = Engine_Api::_()->getDbtable('tokens', 'siteapi');
        $tokeTableSelect = $tokensTable->select()
                ->where('user_id = ?', $user->getIdentity());          // If post exists
        $userToken = $tokensTable->fetchRow($tokeTableSelect);

        if (!empty($userToken) && !empty($userToken->token)) {
            $auth_token = $userToken->token;
            $getOauthToken['token'] = $userToken->token;
            $getOauthToken['secret'] = $userToken->secret;
        } else {
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
        }
        
        $db = Engine_Db_Table::getDefaultAdapter();
                    // Getting IP address.
        if (isset($_REQUEST['ip']) && !empty($_REQUEST['ip'])) {
            $valid = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $_REQUEST['ip']);
            if (empty($valid))
                $this->respondWithError('ip_not_valid');

            $ipObj = new Engine_IP($_REQUEST['ip']);
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        }else {
            $this->respondWithError('ip_not_found');
        }
       
        $gmailTable->insert(array(
            'user_id' => $user->getIdentity(),
            'google_id' => $_REQUEST['google_id'],
        ));
       
        $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

        // Add images
        $getContentImages = Engine_Api::_()->getApi('core', 'siteapi')->getContentImage($user);
        $userArray = array_merge($userArray, $getContentImages);

        $userArray['cover'] = $userArray['image'];

        $device_token = !empty($_REQUEST['registration_id']) ? $_REQUEST['registration_id'] : '';
        $device_token = !empty($_REQUEST['device_token']) ? $_REQUEST['device_token'] : $device_token;
        if (!empty($_REQUEST['device_uuid']) && !empty($device_token)) {
            Engine_Api::_()->getDbtable('gcmusers', 'siteandroidapp')->addGCMuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'registration_id' => $device_token,
                'user_id' => $user->getIdentity()
            ));

            Engine_Api::_()->getDbtable('apnusers', 'siteiosapp')->addApnuser(array(
                'device_uuid' => $_REQUEST['device_uuid'],
                'token' => $device_token,
                'user_id' => $user->getIdentity()
            ));
        }

        $subscriptionForm = $_REQUEST['subscriptionForm'];
        if (empty($subscriptionForm)) {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $this->respondWithError('subscription_fail');
                }
            }
        } else {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseUrl = @trim($baseUrl, "/");
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {

                    // Handle default payment plan
                    $defaultSubscription = null;
                    try {
                        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                        if ($subscriptionsTable) {

                            if(_CLIENT_TYPE == 'android')
                                $defaultSubscription = $subscriptionsTable->activateDefaultPlan($user);

                            if(_CLIENT_TYPE == 'ios'){
                                unset($_REQUEST['package_id']);
                            }
                            
                            if ($defaultSubscription) {
                                // Re-process enabled?
                                $user->enabled = true;
                                $user->save();
                            } else {
                                // Check for the user's plan
                                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                                    if (!$subscriptionsTable->check($user)) {
                                        $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                        if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                            Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user, $isIosSubscriber);
                                        }
                                    }
                                }
                                if (!$subscriptionsTable->check($user)) {
                                    $isIosSubscriber = Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscription($user);
                                    if (isset($isIosSubscriber) && !empty($isIosSubscriber)) {
                                        Engine_Api::_()->getApi('core', 'siteapi')->hasUserIosSubscriptionExpire($user);
                                    }
                                }

                                if (!$subscriptionsTable->check($user)) {
                                    // Get package
                                    if (($packageId = $_REQUEST['package_id']) && ($package = Engine_Api::_()->getItem('payment_package', $packageId))
                                    ) {
                                        $currentSubscription = $subscriptionsTable->fetchRow(array(
                                            'user_id = ?' => $user->getIdentity(),
                                            'active = ?' => true,
                                        ));
                                        // Cancel any other existing subscriptions
                                        Engine_Api::_()->getDbtable('subscriptions', 'payment')
                                                ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);
                                        // Insert the new temporary subscription
                                        $db = $subscriptionsTable->getAdapter();
                                        $db->beginTransaction();
                                        try {
                                            $subscription = $subscriptionsTable->createRow();
                                            $subscription->setFromArray(array(
                                                'package_id' => $_REQUEST['package_id'],
                                                'user_id' => $user->getIdentity(),
                                                'status' => 'initial',
                                                'active' => false, // Will set to active on payment success
                                                'creation_date' => new Zend_Db_Expr('NOW()'),
                                            ));
                                            $subscription->save();
                                            // If the package is free, let's set it active now and cancel the other
                                            if ($package->isFree()) {
                                                $subscription->setActive(true);
                                                $subscription->onPaymentSuccess();
                                                if ($currentSubscription) {
                                                    $currentSubscription->cancel();
                                                }
                                                $user->enabled = true;
                                                $user->save();
                                            }
                                            $db->commit();
                                        } catch (Exception $e) {
                                            $db->rollBack();
                                            throw $e;
                                        }
                                        if (!$package->isFree()) {

                                            $subscription_id = $subscription->subscription_id;
                                            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                            if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                                $response['subscription_id'] = $subscription_id;
                                                $response['user_id'] = $user->getIdentity();
                                                $response['subscription'] = 1;
                                            } else {
                                                $response = $getHost . '/' . $baseUrl . "/payment/subscription/gateway?token=" . $getOauthToken['token'] . "&subscription_id=" . $subscription_id;
                                            }
                                            //RESPONSE
                                            $this->respondWithSuccess($response, true);
                                        }
                                    } else {
                                        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                                        if (_IOS_VERSION && _IOS_VERSION >= '1.5.8') {
                                            $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');
                                              
                                            $subscriptionSelect = $subscriptionsTable->select()->where('user_id = ?', $user->getIdentity())->order('subscription_id DESC')->limit(1);

                                            $subscriptionObj = $subscriptionsTable->fetchRow($subscriptionSelect);


                                            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
                                            $packagesSelect = $packagesTable
                                                    ->select()
                                                    ->from($packagesTable)
                                                    ->where('enabled = ?', true)
                                                    ->where('signup = ?', true)
                                                    ->where('package_id = ?', $subscriptionObj->package_id);
                                            $package = $packagesTable->fetchRow($packagesSelect);

                                            $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                                            $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                                            $multiOptions['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                                            $multiOptions['description'] = (string) $package->description;
                                            $multiOptions['package_id'] = $package->package_id;

                                            if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                                                $multiOptions['price'] = (double) $package->price;
                                                $multiOptions['description'] = (string) $package->description;
                                            } else {
                                                $multiOptions['price'] = (double) $package->price;
                                            }
                                            $multiOptions['currency'] = (string) $userCurrency;
                                            // trial fields work start
                                            $package_type = ($package->isFree()) ? 'free' : 'paid';
                                            $multiOptions['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                                            if($package_type != 'free'){
                                                $multiOptions['recurrence'] = $package->recurrence ;
                                                $multiOptions['recurrence_type'] = $package->recurrence_type ;
                                            }
                                            if($sitesubscriptionModuleEnable){
                                                $multiOptions['trial_duration'] = $package->trial_duration ;
                                                $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();

                                                $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                                                
                                                foreach ($featuresRowData as $feature) {
                                                    $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                                                    foreach($valueRowData as $key1 => $value1){
                                                        if($value1['value'] == null)
                                                            continue;
                                                        if($value1['package_id'] == $package->package_id)
                                                        {   
                                                            $multiOptions['trialfields'][] = $value1['value'] ;
                                                        }
                                                    }
                                                }
                                            }
                                            // trial fields work end 
        
                                            $response['user_id'] = $user->getIdentity();
                                            $response['subscription'] = 1;
                                            $response['package'] = $multiOptions;
                                        } else {
                                            $response = $getHost . '/' . $baseUrl . "/payment/subscription/choose?token=" . $getOauthToken['token'] . '&disableHeaderAndFooter=1';
                                        }
                                        //RESPONSE
                                        $this->respondWithSuccess($response, true);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Silence
                    }
                }
            }
        }

//        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);

        if (!$user->enabled) {
            if (!$user->verified) {
                $this->respondWithError('email_not_verified');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->respondWithError('not_approved');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            }
        }

        $this->respondWithSuccess(array(
            'oauth_token' => $getOauthToken['token'],
            'oauth_secret' => $getOauthToken['secret'],
            'user' => $userArray,
        ));
    }

    public function setPackagePlan($user, $package_id) {
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');

        // Have any gateways or packages been added yet?
        if ($gatewaysTable->getEnabledGatewayCount() <= 0 || $packagesTable->getEnabledNonFreePackageCount() <= 0) {
            return false;
        }



        // Get the default package
        $package = $packagesTable->fetchRow(array(
            '`package_id` = ?' => $package_id,
            'enabled = ?' => true,
        ));

        if (!$package) {
            return false;
        }

        // Create the default subscription
        $subscription = $subscriptionsTable->createRow();
        $subscription->setFromArray(array(
            'package_id' => $package->package_id,
            'user_id' => $user->getIdentity(),
            'status' => 'initial',
            'active' => false,
            'creation_date' => new Zend_Db_Expr('NOW()'),
        ));
        $subscription->save();

        if ($package->isFree()) {
            $subscription->setActive(true);
            $subscription->onPaymentSuccess();
        }
        return $subscription;
    }

    private function _profileTypemapping($user) {
        $profile_type_id = $_REQUEST['profile_type'];
        if (empty($profile_type_id))
            return;

        $enable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mapprofiletypelevel');
        if (empty($enable))
            return;
        $mapprofileTable = Engine_Api::_()->getDbtable('mapprofiletypelevels', 'mapprofiletypelevel');
        $mapname = $mapprofileTable->info('name');
        $select = $mapprofileTable->select()->from($mapname, array('profile_type_id', 'mapprofiletypelevel_id', 'member_level_id'));
        $profiletypes_array = $select->query()->fetchAll();
        foreach ($profiletypes_array as $value) {
            if ($value['profile_type_id'] == $profile_type_id) {
                $user->level_id = $value['member_level_id'];
                $user->save();
            }
        }
    }

    private function _addRandomNo($phoneno, $lenght = 3, $dummyString = '0123456789abcdefghijklmnopqrstuvwxyz') {
        $tempStr = '';

        $strLenght = @strlen($dummyString) - 1;
        for ($i = 0; $i < $lenght; $i++) {
            $phoneno .= $dummyString[mt_rand(0, $strLenght)];
        }


        return $phoneno;
    }

    public function otpData($userData) {
        $enableOtp = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableOtp();
        if (empty($enableOtp) || empty($userData['phoneno']))
            return;
        $otpusertable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
        $userRow = $otpusertable->createRow();
        $userRow->user_id = $userData['user_id'];
        $userRow->phoneno = $userData['phoneno'];
        $userRow->country_code = $userData['country_code'];
        $userRow->enable_verification = 1;
        $userRow->save();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
        if ($showBothPhoneAndEmail) {
            return;
        }
        $user = Engine_Api::_()->getItem('user', $userData['user_id']);
        if ($user->verified) {
            return;
        }
        $user->verified = 1;
        $user->enabled = (int) ( $user->approved && $user->verified );
        $user->save();
    }

    public function phoneNoValidation($values) {
        $phoneno = null;
        $enableOtp = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableOtp();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
        $key = 'phoneno';
        if ($enableOtp && empty($showBothPhoneAndEmail)) {
            if (!strstr($values['emailaddress'], '@')) {
                $phoneno = $values['emailaddress'];
                $key = "emailaddress";
            }
        } else {
            $phoneno = $values['phoneno'];
        }
        if (empty($phoneno))
            return null;

        $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
        $sqlquery = $userTable->select()
                ->from($userTable->info('name'), array('user_id'))
                ->where('phoneno = ?', $phoneno);
        $userAdded = $userTable->fetchRow($sqlquery);
        if (!empty($userAdded)) {
            return $key;
        } else
            return null;
    }

    public function generateCodeAction() {
        //verification code mail send
        $stepTable = Engine_Api::_()->getDbTable('signup', 'user');
        $stepRow = $stepTable->fetchRow($stepTable->select()->where('class = ?', 'User_Plugin_Signup_Otp'));
        $bodyParams['response']['isOtpSend'] = false;

        if (!isset($_POST['email'])) 
            $bodyParams['response']['isOtpSend'] = false;
        if (empty($_POST['email']))
            $bodyParams['response']['isOtpSend'] = false;
        
        if($stepRow->enable) {
          $email = $_POST['email'];
          $codeTable = Engine_Api::_()->getDbTable('codes', 'user');
          $isEmailExist = $codeTable->isEmailExist($email);
          if($isEmailExist) {
            $isEmailExist->delete();
          }
          $code = rand(100000, 999999);
          $row = $codeTable->createRow();
          $row->email = $email;
          $row->code = $code;
          $row->creation_date = date('Y-m-d H:i:s');
          $row->modified_date = date('Y-m-d H:i:s');
          $row->save();
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'user_otp', array('host' => $_SERVER['HTTP_HOST'], 'code' => $code));
          $bodyParams['response']['isOtpSend'] = true;
          $bodyParams['response']['code'] = $code;
        }
        $this->respondWithSuccess($bodyParams);
    }

    //CUSTOM_CODE_STARTED_FROM_HERE
    public function usernameValidationAction() {
        $this->validateRequestMethod('POST');
        $values['username'] = $username = $_POST['username'];

        $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'user')->checkUsernameValidators();
        $values['validators'] = $validators;
        $validationMessage = $this->isValid($values);
        if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
        }
        $users_table = Engine_Api::_()->getDbtable('users', 'user');
        $users_select = $users_table->select()->where('username = ?', $username);
        $alreadyExist = $users_table->fetchRow($users_select);
        if($alreadyExist){
            $this->respondWithError('unauthorized', "Someone has already picked up this username.");
        }
        $this->successResponseNoContent('no_content');

    }
    //CUSTOM_CODE_ENDS_HERE

}
