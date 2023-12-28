<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    private $_profileTypeId = false;

    /**
     * Validation: user signup form
     * 
     * @return array
     */
    public function getSignupFormValidators($values) {
        $accountValidations = $fieldValidations = array();

        // Get signup account form validation.
        if (isset($values['account_validation']) && !empty($values['account_validation'])) {
            $enabledopt = $this->hasEnableOtp();
            if ($enabledopt) {
                $accountValidations = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteotpverifier')->getSignupAccountFormValidations();
            } else {
                $accountValidations = $this->_getSignupAccountFormValidations();
            }
        }
        // Get fields account form validation.
        if (isset($values['fields_validation']) && !empty($values['fields_validation'])) {
            if (isset($values['profile_type']) && !empty($values['profile_type']))
                $this->_profileTypeId = $values['profile_type'];

            if (!empty($this->_profileTypeId))
                $fieldValidations = $this->_getSignupFieldsFormValidations();
        }

        return array_merge($accountValidations, $fieldValidations);
    }
       
    public function getLoginFormValidators() {
        $validators = array();
        
        // CUSTOM_WORK_STARTS_FROM_HERE
        // if ($enableOTP) {
        //     $validators['email'] = $this->getMobileOrEmailValidator($options);
        // } else {
        //     $validators['email'] = $this->getEmailValidator($options);
        // }
        
        if (empty($_REQUEST['loginWithOtp']))
            $validators['password'] = $this->getPassConfValidator();

        // CUSTOM_WORK_ENDS_HERE
        return $validators;
    }

    /**
     * Validation: user settings - change password.
     * 
     * @return array
     */
    public function getChangePasswordValidators() {
        $formValidators['oldPassword'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['password'] = $this->getPasswordValidator();
        $formValidators['passwordConfirm'] = $this->getPassConfValidator();

        return $formValidators;
    }

    /**
     * Validation: user settings - General settings.
     * 
     * @return array
     */
    public function getGeneralFormValidators() {
        $formValidators['email'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            ),
        );

        $formValidators['username'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('Alnum', true),
                array('StringLength', true, array(4, 64)),
                array('Regex', true, array('/^[a-z][a-z0-9]*$/i'))
            )
        );

        $formValidators['timezone'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['locale'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

    /**
     * Validation: user subscription form
     * 
     * @return array
     */
    public function getSubscriptionFormValidators() {
        $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
        $stepSelect = $stepTable->select()->where('class = ?', 'Payment_Plugin_Signup_Subscription');
        $row = $stepTable->fetchRow($stepSelect);
        $enableSubscription = $row->enable;
        // Get available subscriptions
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $packagesSelect = $packagesTable
                ->select()
                ->from($packagesTable)
                ->where('enabled = ?', true)
        ;
        $multiOptions = array();
        $packagesObj = $packagesTable->fetchAll($packagesSelect);
        if ((count($packagesObj) > 0) && isset($enableSubscription) && !empty($enableSubscription)) {
            $formValidators['package_id'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        } elseif ((Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0 &&
                Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() > 0)) {
            $formValidators['package_id'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }
        return $formValidators;
    }

    /**
     * Validation: user signup field form
     * 
     * @return array
     */
    private function _getSignupFieldsFormValidations() {
        $option_id = $this->_profileTypeId;
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('user');
        $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
        $fieldArray = array();
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            
            if(!isset($meta) || empty($meta))
            continue;

            $type = $meta->type;

            if (!empty($type) && ($type == 'heading'))
                continue;

            if (!isset($meta->show) || empty($meta->show))
                continue;

            if (!isset($meta->display) || empty($meta->display))
                continue;


            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            if (!empty($meta->alias))
                $key = $key . '_' . ( $meta->alias ? 'alias_' . $meta->alias : sprintf('field_%d', $meta->alias->field_id) );
            else
                $key = $key . '_alias_';

            if (isset($meta->required) && !empty($meta->required)) {
                $fieldArray[$key] = array(
                    'required' => true,
                    'allowEmpty' => false
                );


                if (isset($meta->label) && !empty($meta->label))
                    $fieldArray[$key]['label'] = $meta->label;
            }

            //for validation of age limit on profile field
            if ($meta->type == 'birthdate') {
                if (isset($meta->config['min_age'])) {
                    if (isset($meta->required) && !empty($meta->required))
                        $fieldArray[$key] = array(
                            'required' => true,
                            'allowEmpty' => false,
                            'min' => $meta->config['min_age'],
                            'label' => $meta->label
                        );
                    else {
                        $fieldArray[$key] = array(
                            'min' => $meta->config['min_age'],
                            'label' => $meta->label
                        );
                    }
                }
            }

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }
        return $fieldArray;
    }

    /**
     * Validation: user signup account form
     * 
     * @return array
     */
    private function _getSignupAccountFormValidations($formValidators = array()) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $options = array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'));
        $formValidators['email'] = $this->getEmailValidator($options);
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $random = ($settings->getSetting('user.signup.random', 0) == 1);

        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            $formValidators['code'] = array(
                'required' => true,
                'allowEmpty' => false,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite Code'),
            );
        }

        if (empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id']) && empty($_REQUEST['linkedin_uid']) && empty($random)) {
            $formValidators['password'] = $this->getPasswordValidator();
            $formValidators['passconf'] = $this->getPassConfValidator();
        }

        if ($settings->getSetting('user.signup.username', 1) > 0) {
            $formValidators['username'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('Alnum', true),
                    array('StringLength', true, array(4, 64)),
                    array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
                ),
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Address'),
            );
        }

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            if (COUNT($options) > 1) {
                $formValidators['profile_type'] = array(
                    'required' => true,
                    'allowEmpty' => false,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Type'),
                );
            }
        }

        $formValidators['timezone'] = array(
            'required' => true,
            'allowEmpty' => false,
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
        );

        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();
        if (COUNT($languageList) > 1) {
            $formValidators['language'] = array(
                'required' => true,
                'allowEmpty' => false,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Language'),
            );
        }

        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'ios'))) {
            if ($settings->getSetting('user.signup.terms', 1) == 1) {
                $formValidators['terms'] = array(
                    'required' => true,
                    'allowEmpty' => false,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Terms of Service'),
                );
            }
        }
        
        return $formValidators;
    }

    public function hasEnableOtp() {
         $moduleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteotpverifier');
        if (!empty($moduleEnable) && ((_IOS_VERSION && version_compare( _IOS_VERSION,'2.2.8')!=-1) || (_ANDROID_VERSION && version_compare(_ANDROID_VERSION,'3.1.7')!=-1 ))) {
        $enabledOTPClient = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showphoneSignup = $settings->getSetting('siteotpverifier.singupUserPhone', 1);
        if($moduleEnable && $enabledOTPClient && !empty($showphoneSignup)){
            return 1;
        }
        
        else
            return 0;
        }
        else return 0;
    }

    // CUSTOM_CODE_STARTS_FROM_HERE
    public function checkUsernameValidators() { 
        $formValidators['username'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('Alnum', true),
                array('StringLength', true, array(3, 64)),
                array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
                array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
            ),
        );
        
        return $formValidators;
    }
    // CUSTOM_CODE_ENDS_HERE

}
