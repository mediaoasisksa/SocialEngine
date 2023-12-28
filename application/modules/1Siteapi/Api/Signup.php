<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Signup.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Signup {

    //GET SIGNUP PROCESS STEPS FORM ELEMENTS
    public function getFormElements($class) {

        $classNameArray = explode("_", $class);
        $funcname = 'get' . $classNameArray[3] . 'Form';
        if (method_exists($this, $funcname)) {
            return $this->$funcname();
        }
    }

    protected function getAccountForm() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        //MAKE AN ARRAY OF FORM ELEMENTS WITH VALIDATORS
        $accountForm = array();

        //email element
        $accountForm['email'] = array('name' => 'email', 'label' => 'Email Address', 'description' => 'You will use your email address to login.', 'hasValidator' => true);

        //password element
        $accountForm['password'] = array('name' => 'password', 'label' => 'Password', 'description' => 'Passwords must be at least 6 characters in length.', 'hasValidator' => true);

        //confirm password
        $accountForm['passconf'] = array('name' => 'passconf', 'label' => 'Password Again', 'description' => 'Enter your password again for confirmation.', 'hasValidator' => true);

        //username
        $description = 'This will be the end of your profile link, for example: <br /> ' .
                '<span id="profile_address">http://"' . $_SERVER['HTTP_HOST'] . Siteapi_Controller_Front::getInstance()->getRouter()
                        ->assemble(array('id' => 'yourname'), 'user_profile') . '"</span>';

        $accountForm['text'] = array('name' => 'username', 'label' => 'Profile Address', 'description' => $description, 'hasValidator' => true);

        //profiletype element
        // Element: profile_type
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            if (count($options) > 1) {
                $options = $profileTypeField->getElementParams('user');
                unset($options['options']['order']);
                unset($options['options']['multiOptions']['0']);
                $accountForm['select'] = array('name' => 'profile_type', 'options' => $options['options'], 'hasValidator' => true);
            } else if (count($options) == 1) {
                $accountForm['hidden'] = array('name' => 'profile_type', 'value' => $options[0]->option_id);
            }
        }

        $options = array(
            'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
            'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
            'US/Central' => '(UTC-6) Central Time (US & Canada)',
            'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
            'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
            'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
            'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
            'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
            'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
            'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
            'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
            'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
            'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
            'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
            'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
            'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
            'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
            'Iran' => '(UTC+3:30) Tehran',
            'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
            'Asia/Kabul' => '(UTC+4:30) Kabul',
            'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
            'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
            'Asia/Katmandu' => '(UTC+5:45) Nepal',
            'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
            'Indian/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
            'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
            'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
            'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
            'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
            'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
            'Asia/Magadan' => '(UTC+11) Magadan, Solomon Is., New Caledonia',
            'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
        );
        //timezone element
        $accountForm['select'] = array('name' => 'timezone', 'label' => 'Timezone', 'options' => $options, 'value' => $settings->getSetting('core.locale.timezone'), 'hasValidator' => true);

        if ($settings->getSetting('user.signup.terms', 1) == 1) {
            // Set the translations for zend library.
            if (!Zend_Registry::isRegistered('Zend_Translate'))
                Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
            
            $description = Zend_Registry::get('Zend_Translate')->_('I have read and agree to the <a target="_blank" href="%s/help/terms">terms of service</a>.');
            $description = sprintf($description, Zend_Controller_Front::getInstance()->getBaseUrl());
            $accountForm['checkbox'] = array('name' => 'terms', 'label' => 'Terms of Service', 'description' => $description, 'hasValidator' => true);
        }
        return $accountForm;
    }

    //GET PROFILE FIELDS
    public function getFieldsForm() {
        //$fieldElements = Engine_Api::_()->getApi('fields', 'api')->isValid($data);echo $fieldElements;die;
        $fieldElements = Engine_Api::_()->getApi('fields', 'api')->generate();
        return $fieldElements;
    }

    //GET PHOTO UPLOAD FORM
    public function getPhotoForm() {
        $photoForm = array();
        $photoForm['image'] = array('src' => '/application/modules/User/externals/images/nophoto_user_thumb_icon.png', 'label' => 'Current Photo');
        $photoForm['file'] = array('name' => 'Filedata', 'label' => 'Choose New Photo', 'destination' => APPLICATION_PATH . '/public/temporary/', 'validators' => array(array('Count', false, 1), array('Extension', false, 'jpg,png,gif,jpeg')), 'onchange' => 'javascript:uploadSignupPhoto();');
        $photoForm['hidden'] = array('name' => 'coordinates', 'order' => 1);
        $photoForm['hidden'] = array('name' => 'uploadPhoto', 'order' => 2);
        $photoForm['hidden'] = array('name' => 'nextStep', 'order' => 3);
        $photoForm['hidden'] = array('name' => 'skip', 'order' => 4);
        $photoForm['buttons'] = array(array('name' => 'done', 'label' => 'Save Photo', 'type' => 'submit', 'onclick' => 'javascript:finishForm();'), array('name' => 'skip-link', 'label' => 'skip', 'onclick' => 'skipForm(); return false;'));
        return $photoForm;
    }

    //GET INVITE FORM
    public function getInviteForm() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
            
        $translate = Zend_Registry::get('Zend_Translate');
        //MAKE AN ARRAY OF FORM ELEMENTS WITH VALIDATORS
        $inviteForm = array();

        //textarea element for having recipients email addresses comma separated.
        $inviteForm['textarea'][] = array('name' => 'recipients', 'label' => 'Recipients', 'description' => 'Comma-separated list, or one-email-per-line.', 'hasValidatorCallback' => true);

        if ($settings->getSetting('invite.allowCustomMessage', 1) > 0) {
            //CUSTOM MESSAGE.
            $inviteForm['textarea'][] = array('name' => 'message', 'label' => 'Custom Message', 'value' => $translate->_($settings->getSetting('invite.message')), 'hasValidator' => true);
        }
        $inviteForm['checkbox'] = array('name' => 'friendship', 'label' => 'Send a friend request if the user(s) join(s) the network', 'description' => 'Comma-separated list, or one-email-per-line.');

        return $inviteForm;
    }

    public function checkPasswordConfirm($value, $passwordElement) {
        return ( $value == $passwordElement->getValue() );
    }

    public function checkInviteCode($value, $emailElement) {
        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
        $select = $inviteTable->select()
                ->from($inviteTable->info('name'), 'COUNT(*)')
                ->where('code = ?', $value)
        ;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.checkemail')) {
            $select->where('recipient LIKE ?', $emailElement->getValue());
        }

        return (bool) $select->query()->fetchColumn(0);
    }

    public function checkBannedEmail($value, $emailElement) {
        $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
        return !$bannedEmailsTable->isEmailBanned($value);
    }

    public function checkBannedUsername($value, $usernameElement) {
        $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
        return !$bannedUsernamesTable->isUsernameBanned($value);
    }

    public function validateEmails($value) {
        // Not string?
        if (!is_string($value) || empty($value)) {
            return false;
        }

        // Validate emails
        $validate = new Zend_Validate_EmailAddress();

        $emails = array_unique(array_filter(array_map('trim', preg_split("/[\s,]+/", $value))));

        if (empty($emails)) {
            return false;
        }

        foreach ($emails as $email) {
            if (!$validate->isValid($email)) {
                return false;
            }
        }

        return true;
    }

}
