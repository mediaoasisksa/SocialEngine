<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Validators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Validators extends Core_Api_Abstract {

    protected $_elements = array();
    protected static $_messages = array();

    public function checkFormValidator($data = null) {
        $valid = true;

        try {

            if (!empty($data)) {
                $validators = array();
                //CHECK IF A VALIDATOR IS SET FOR THAT PARTICULA ELEMENT.
                if (isset($data['validators'])) {
                    $validators = $data['validators'];
                    unset($data['validators']);
                }
                $error_message = array();
                foreach ($validators as $key => $elementValue1) {
                    $elementValue = $data[$key];

                    if (isset($validators[$key])) {

                        //FIRST ADD THE VALIDATORS TO THIS ELEMENT.
                        $formElement = new Zend_Form_Element($key, $validators[$key]);
                        $this->_elements[$key] = $formElement;
                        //ADD VALIDATOR IF ANY                       
                        $this->addValidator($key, $formElement);
                        //NOW SET ERROR MESSAGES ON VALIDATORS
                        if (isset($validators[$key]['validators'])) {
                            foreach ($validators[$key]['validators'] as $validate) {
                                if (is_array($validate))
                                    $validate = $validate[0];

                                $this->setErrorMessages($formElement, $validate, $key);
                            }
                        }

                        $isValidElement = $formElement->isvalid($elementValue);

                        //validation of age limit on profile field
                        if (strstr($key, 'birthdate') && isset($validators[$key]['min']) && !empty($validators[$key]['min']) && isset($elementValue) && !empty($elementValue)) {
                            $min_age = $validators[$key]['min'];
                            $userDateOfBirth = strtotime($elementValue);
                            if (time() - $userDateOfBirth < $min_age * 31536000) {
                                $isValidElement = false;
                            }
                        }
                        if (!$isValidElement) {

                            $getMessages = $formElement->getMessages();

                            $tempGetMessage = @end($getMessages);
                            if (isset($elementValue1['label']) && !empty($elementValue1['label'])) {
                                $tempGetMessage = $elementValue1['label'] . ' - ' . $tempGetMessage;
                            }

                            if (strstr($key, 'birthdate') && isset($validators[$key]['min']) && !empty($validators[$key]['min'])) {
                                $tempGetMessage = "You should be atleast " . $min_age . " years to become a member.";
                            }


                            self::$_messages[$key] = $tempGetMessage;
                        }

                        $valid = $isValidElement && $valid;
                    }
                }
            }
        } catch (Exception $e) {
            
        }

        return $valid;
    }

    //add the custom validators to the elements.
    public function addValidator($key, Zend_Form_Element $element) {

        $callBackClass = Engine_Api::_()->getApi('signup', 'Siteapi');
        switch ($key) {
            case 'email':
                $bannedEmailValidator = new Engine_Validate_Callback(array($callBackClass, 'checkBannedEmail'), $element);
                $bannedEmailValidator->setMessage("This email address is not available, please use another one.");
                $element->addValidator($bannedEmailValidator);
                break;
            case 'code':
                $emailElement = $this->getElement('email');
                if ($emailElement == null)
                    break;
                $codeValidator = new Engine_Validate_Callback(array($callBackClass, 'checkInviteCode'), $emailElement);
                $codeValidator->setMessage("This invite code is invalid or does not match the selected email address");
                $element->addValidator($codeValidator);
                break;
            case 'passconf':
                $passElement = $this->getElement('password');
                if ($passElement == null)
                    break;
                $specialValidator = new Engine_Validate_Callback(array($callBackClass, 'checkPasswordConfirm'), $passElement);
                $specialValidator->setMessage('Password did not match', 'invalid');
                $element->addValidator($specialValidator);
                break;
            case 'username':
                $bannedUsernameValidator = new Engine_Validate_Callback(array($callBackClass, 'checkBannedUsername'), $element);
                $bannedUsernameValidator->setMessage("This profile address is not available, please use another one.");
                $element->addValidator($bannedUsernameValidator);
                break;

            case 'recipients':
                $emailValidator = new Engine_Validate_Callback(array(new User_Form_Signup_Invite(), 'validateEmails'), $element);
                $emailValidator->setMessage("Please enter a valid value.");
                $element->addValidator($emailValidator);
                break;
        }
    }

    protected function getElement($name) {
        if (array_key_exists($name, $this->_elements)) {
            return $this->_elements[$name];
        }
        return null;
    }

//set the custom error message to the validated fields.
    public function setErrorMessages(Zend_Form_Element $element, $validate = null, $key = null) {

        $validator = $element->getValidator($validate);
        if (!$validator)
            return;

        switch ($validate) {
            case 'NotEmpty':
                if ($key == 'email') {
                    $validator->setMessage('Please enter a valid email address', 'isEmpty');
                } elseif ($key == 'password')
                    $validator->setMessage('Please enter a valid password.', 'isEmpty');
                elseif ($key == 'passconf')
                    $validator->setMessage('Please make sure the "password" and "password again" fields match.', 'isEmpty');
                elseif ($key == 'username')
                    $validator->setMessage('Please enter a valid profile address.', 'isEmpty');
                elseif ($key == 'emailaddress')
                    $validator->setMessage('Please enter a valid email address or mobile no.', 'isEmpty');


                break;
            case 'Db_NoRecordExists':

                if ($key == 'email')
                    $validator->setMessage('Someone has already registered this email address, please use another one.', 'recordFound');
                elseif ($key == 'username')
                    $validator->setMessage('Someone has already picked this profile address, please use another one.', 'recordFound');
                elseif ($key == 'emailaddress')
                    $validator->setMessage('Someone has already picked this email address or mobile no, please use another one.', 'recordFound');
                elseif ($key == 'phoneno')
                    $validator->setMessage('Someone has already picked this mobile no, please use another one.', 'recordFound');
                break;

            case 'Regex':
                if ($key == 'emailaddress')
                    $validator->setMessage('Please enter a valid email address or mobile no.', 'regexNotMatch');
                elseif ($key == 'phoneno')
                    $validator->setMessage('Please enter a valid mobile no.', 'regexNotMatch');
                elseif ($key == 'password')
                    if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                        $validator->setMessage('Password must be at least 6 characters and contain one upper and one lower case letter, one number and one special character. Special Characters Allowed !#$%&*-?@^', 'regexNotMatch');
                    }
                else
                    $validator->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
                break;
            case 'Alnum':
                $validator->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');
                break;
            case 'GreaterThan':
                if ($key == 'terms')
                    $validator->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
                break;
        }
    }

    public function setMessage($key, $value) {

//    self::$_messages[$key] = $value;
        self::$_messages[] = $value;
    }

    public function getMessages() {

        return self::$_messages;
    }

    public function __call($method, array $arguments = array()) {
        throw new Engine_Exception(sprintf('Method "%s" not supported', $method));
    }

    //return the email validators and filters
    public function getEmailValidator($options = array()) {
        $emailValidator = array();
        $emailValidator = array('required' => 1,
            'allowEmpty' => false,
            'validators' => array(array('NotEmpty', 1), array('EmailAddress', 1), array('Db_NoRecordExists', '1', array('engine4_users', 'email'))),
            'filters' => array('StringTrim')
        );

        if (isset($options['required']) && !$options['required'])
            unset($emailValidator['required']);
        if (isset($options['Db_NoRecordExists']) && !$options['Db_NoRecordExists'])
            unset($emailValidator['validators'][2]);

        return $emailValidator;
    }

    //returns the password validators and filters
    public function getPasswordValidator($addValidators = array(), $removeValidators = array()) {

        if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            $validators = array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(6, 32)),
                    array('Regex', true, array('/^(?=.*[A-Z].*)(?=.*[\!#\$%&\*\-\?\@\^])(?=.*[0-9].*)(?=.*[a-z].*).*$/')),
                );
        } else {
            $validators = array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(6, 32)),
                );
        }
        $passwordValidator = array();
        $passwordValidator = array(
            'required' => 1,
            'allowEmpty' => false,
            'validators' => $validators

        );

        return $passwordValidator;
    }

    //returns the passwordconf validators and filters
    public function getPassConfValidator($addValidators = array(), $removeValidators = array()) {
        $passwordValidator = array();
        $passwordValidator = array('required' => true,
            'allowEmpty' => false,
            'validators' => array(array('NotEmpty', true))
        );

        return $passwordValidator;
    }

    //returns the validator posses on text field.
    public function getTextValidator($addValidators = array(), $removeValidators = array()) {
        $textValidator = array();
        $textValidator = array(
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('Alnum', true),
                array('StringLength', true, array(4, 64)),
                array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
                array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
            )
        );

        return $textValidator;
    }

    //returns the validator posses on select box
    public function getSelectValidator($addValidators = array(), $removeValidators = array()) {
        $selectValidator = array();
        $selectValidator = array('required' => true,
            'allowEmpty' => false,
        );

        return $selectValidator;
    }

    //returns the validator posses on checkbox
    public function getCheckboxValidator($addValidators = array(), $removeValidators = array()) {
        $checkboxValidator = array();
        $checkboxValidator = array('required' => true,
            'validators' => array(
                'NotEmpty',
                array('GreaterThan', false, array(0)),
            )
        );

        return $checkboxValidator;
    }

    //returns the validator posses on checkbox
    public function getTextareaValidator($addValidators = array(), $removeValidators = array()) {
        $textareaValidator = array();
        $textareaValidator = array('required' => FALSE,
            'allowEmpty' => TRUE,
            'filters' => array(
                new Engine_Filter_Censor(),
            )
        );

        return $textareaValidator;
    }

    //return the email validators and filters
    public function getMobileValidator($options = array()) {
        $phoneValidator = array();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
        $reqphoneno = !empty($showBothPhoneAndEmail) && $settings->getSetting('siteotpverifier.singupRequirePhone', 1);
        if (empty($showBothPhoneAndEmail)) {
            $reqphoneno = 1;
        }
        $phoneValidator = array('required' => $reqphoneno,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', empty($reqphoneno) ? false : true),
                array('Regex', true, array('/^[1-9][0-9]{7,10}$/')),
                array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'siteotpverifier_users', 'phoneno'))
            ),
        );


        return $phoneValidator;
    }

    //return the email validators and filters
    public function getMobileOrEmailValidator($options = array()) {
        $phoneOrEmailValidator = array();
        $phoneOrEmailValidator = array('required' => 1,
            'allowEmpty' => false,
            'filters' => array(
                'StringTrim',
            ),
            'validators' => array(
                array('NotEmpty', true),
                array('Regex', true, "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})|([1-9][0-9]{7,11})$/"),
            ),
        );


        return $phoneOrEmailValidator;
    }

}
