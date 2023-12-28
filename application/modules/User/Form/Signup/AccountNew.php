<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Signup_Account extends Engine_Form_Email
{  
  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ptype = $_GET['profile_type'] ? $_GET['profile_type'] : 13;
    $this->_emailAntispamEnabled = ($settings
        ->getSetting('core.spam.email.antispam.signup', 1) == 1) &&
      empty($_SESSION['facebook_signup']) &&
      empty($_SESSION['twitter_signup']) &&
      empty($_SESSION['janrain_signup']);
    
    $inviteSession = new Zend_Session_Namespace('invite');
    $tabIndex = 1;
    
    // Init form
    $this->setTitle('Create Account');
    $this->setAttrib('id', 'signup_account_form');
    $this->setAttrib('enctype', 'multipart/form-data');

    // Element: name (trap)
    $this->addElement('Text', 'name', array(
      'class' => 'signup-name',
      'label' => 'Name',
      'validators' => array(
	      array('StringLength', true, array('max' => 0)))));

    $this->name->getValidator('StringLength')->setMessage('An error has occured, please try again later.');
    
    $this->addElement('Text', 'fname', array(
      'label' => 'First Name',
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));

    $this->addElement('Text', 'lname', array(
      'label' => 'Last Name',
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));

    $this->addDisplayGroup(array('fname','lname'), 'nameg');

    // Element: email
    $emailElement = $this->addEmailElement(array(
      'label' => 'Email Address',
      'description' => 'You will use your email address to login.',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'))
      ),
      'filters' => array(
        'StringTrim'
      ),
      // fancy stuff
      'inputType' => 'email',
      'autofocus' => 'autofocus',
      'tabindex' => $tabIndex++,
    ));
    $emailElement->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    $emailElement->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $emailElement->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this email address, please use another one.', 'recordFound');
    $emailElement->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
    // Add banned email validator
    $bannedEmailValidator = new Engine_Validate_Callback(array($this, 'checkBannedEmail'), $emailElement);
    $bannedEmailValidator->setMessage("This email address is not available, please use another one.");
    $emailElement->addValidator($bannedEmailValidator);
    
    if( !empty($inviteSession->invite_email) ) {
      $emailElement->setValue($inviteSession->invite_email);
    }

    //if( $settings->getSetting('user.signup.verifyemail', 0) > 0 && $settings->getSetting('user.signup.checkemail', 0) == 1 ) {
    //  $this->email->addValidator('Identical', true, array($inviteSession->invite_email));
    //  $this->email->getValidator('Identical')->setMessage('Your email address must match the address that was invited.', 'notSame');
    //}
    
    // Element: code
    if( $settings->getSetting('user.signup.inviteonly') > 0 ) {
      $codeValidator = new Engine_Validate_Callback(array($this, 'checkInviteCode'), $emailElement);
      $codeValidator->setMessage("This invite code is invalid or does not match the selected email address");
      $this->addElement('Text', 'code', array(
        'label' => 'Invite Code',
        'required' => true
      ));
      $this->code->addValidator($codeValidator);

      if( !empty($inviteSession->invite_code) ) {
        $this->code->setValue($inviteSession->invite_code);
      }
    }

    if( $settings->getSetting('user.signup.random', 0) == 0 && 
        empty($_SESSION['facebook_signup']) && 
        empty($_SESSION['twitter_signup']) && 
        empty($_SESSION['janrain_signup']) ) {

      // Element: password
      $this->addElement('Password', 'password', array(
        'label' => 'Password',
        'description' => 'Passwords must be at least 6 characters in length.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
          array('NotEmpty', true),
          array('StringLength', false, array(6, 32)),
        ),
        'tabindex' => $tabIndex++,
      ));
      $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
      $this->password->getValidator('NotEmpty')->setMessage('Please enter a valid password.', 'isEmpty');

      // Element: passconf
      $this->addElement('Password', 'passconf', array(
        'label' => 'Password Again',
        'description' => 'Enter your password again for confirmation.',
        'required' => true,
        'validators' => array(
          array('NotEmpty', true),
        ),
        'tabindex' => $tabIndex++,
      ));
      $this->passconf->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
      $this->passconf->getValidator('NotEmpty')->setMessage('Please make sure the "password" and "password again" fields match.', 'isEmpty');

      $specialValidator = new Engine_Validate_Callback(array($this, 'checkPasswordConfirm'), $this->password);
      $specialValidator->setMessage('Password did not match', 'invalid');
      $this->passconf->addValidator($specialValidator);
    }

    $this->addDisplayGroup(array('password','passconf'), 'passwordg');

    // Element: username
    if( $settings->getSetting('user.signup.username', 1) > 0 ) {
      $description = Zend_Registry::get('Zend_Translate')
          ->_('This will be the end of your profile link, for example: <br /> ' .
              '<span id="profile_address">http://%s</span>');
      $description = sprintf($description, $_SERVER['HTTP_HOST']
          . Zend_Controller_Front::getInstance()->getRouter()
          ->assemble(array('id' => 'yourname'), 'user_profile'));

      $this->addElement('Text', 'username', array(
        'label' => 'Profile Address',
        'description' => $description,
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
          array('NotEmpty', true),
          array('Alnum', true),
          array('StringLength', true, array(4, 64)),
          array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
          array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
        ),
        'tabindex' => $tabIndex++,
          //'onblur' => 'var el = this; en4.user.checkUsernameTaken(this.value, function(taken){ el.style.marginBottom = taken * 100 + "px" });'
      ));
      $this->username->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
      $this->username->getValidator('NotEmpty')->setMessage('Please enter a valid profile address.', 'isEmpty');
      $this->username->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this profile address, please use another one.', 'recordFound');
      $this->username->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
      $this->username->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');

      // Add banned username validator
      $bannedUsernameValidator = new Engine_Validate_Callback(array($this, 'checkBannedUsername'), $this->username);
      $bannedUsernameValidator->setMessage("This profile address is not available, please use another one.");
      $this->username->addValidator($bannedUsernameValidator);
    }
    
    // // Element: profile_type
    // $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    // if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
    //   $profileTypeField = $topStructure[0]->getChild();
    //   $options = $profileTypeField->getOptions();
    //   if( count($options) > 1 ) {
    //     $options = $profileTypeField->getElementParams('user');
    //     unset($options['options']['order']);
    //     unset($options['options']['multiOptions']['0']);
    //     $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
    //           'required' => true,
    //           'allowEmpty' => false,
    //           'tabindex' => $tabIndex++,
    //           'onchange' => 'enterField(this.value)'
    //         )));
    //   } else if( count($options) == 1 ) {
    //     $this->addElement('Hidden', 'profile_type', array(
    //       'value' => $options[0]->option_id,
    //       'order' => 1001
    //     ));
    //   }
    // }
    
    if($ptype == 92) {
                $this->addElement('Hidden', 'profile_type', array(
          'value' => 92,
          'order' => 1001
        ));
    } else {
        $this->addElement('Hidden', 'profile_type', array(
          'value' => 4,
          'order' => 1001
        ));
    }
    
    $this->addElement('Select', 'gender', array(
        'label' => 'Gender',
        'multiOptions' => array(
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other')
    ));
    
    $this->addElement('Text', 'jobtitle', array(
      'label' => 'Job Title',
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));
	      
	      
	 $this->addElement('Text', 'qualifications', array(
      'label' => 'Company Name',
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));
	      
	      
	      $this->addElement('Text', 'description', array(
      'label' => 'Brief Introduction',
      'validators' => array(
	      array('StringLength', true, array('min' => 5)))));
	      
	     
	      
	      
  		$values = array();

		$sql = Engine_Api::_()->getItemTable('sitebooking_category')->getMainCategories($values);

		$categories = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll($sql)->toArray();
	      
	      $cat = array('' => '');
	      foreach($categories as $cate) {
	        $cat[$cate['category_id']] =  $cate['category_name']; 
	      }
        // Element: timezone
        $this->addElement('Select', 'consulatant_category_id', array(
        'label' => 'Consultation Category',
        'multiOptions' => $cat,
        'tabindex' => $tabIndex++,
        'allowEmpty' => false,
        'required' => true,
        ));
        $this->consulatant_category_id->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


         $this->addDisplayGroup(array('jobtitle','qualifications', 'description', 'consulatant_category_id'), 'jobg');
        
        // Element: timezone
        // $this->addElement('Select', 'mentor_category_id', array(
        // 'label' => 'Mentor Category',
        // 'multiOptions' => $cat,
        // 'tabindex' => $tabIndex++,
        // 'allowEmpty' => false,
        // 'required' => true,
        // ));
        //$this->mentor_category_id->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    //  $this->addElement('Textarea', 'description', array(
    //   'label' => 'Description',
    //   //'description'=> 'Description',
    //   'validators' => array(
	   //   array('StringLength', true, array('max' => 50000)))));
	      
	      $this->addElement('Textarea', 'history', array(
      'label' => 'Career / Employment History',
      'description'=> 'Please include CV ( MAX 1 PAGE) IF YOU WISH',
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));
	          $this->addElement('File', 'file', array(
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Upload CV',
        //'description' => 'Upload a cv [Note: cv with extension: “docx, pdf] only.]',
    ));
    $this->file->addValidator('Extension', false, 'docx, pdf, doc, txt');
	   //         $this->addElement('File', 'cvupload', array(
    //       'label' => "CV Upload"
    //   ));
    // $this->addElement('Text', 'specialist', array(
    //   'label' => 'Specialist',
    //   'validators' => array(
	   //   array('StringLength', true, array('max' => 64)))));
	      
    // $this->addElement('Text', 'contactno', array(
    //   'label' => 'Contact No',
    //   'validators' => array(
	   //   array('StringLength', true, array('max' => 64))))); 
	      
 $this->addElement('Text', 'country', array(
      'label' => 'Country',
        'required' => true,
              'allowEmpty' => false,
      'validators' => array(
	      array('StringLength', true, array('max' => 64))))); 
	      
	       $this->addElement('Text', 'city', array(
      'label' => 'City',
        'required' => true,
              'allowEmpty' => false,
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));
	      
      $this->addDisplayGroup(array('country','city'), 'locationg');

	   $this->addElement('Text', 'price', array(
      'label' => 'Consultancy Fees',
        'required' => true,
              'allowEmpty' => false,
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));
	      
	 $this->addElement('Text', 'duration', array(
      'label' => 'Consultancy Duration (In Mins)',
      'placeholder' => 30,
        'required' => true,
              'allowEmpty' => false,
      'validators' => array(
	      array('StringLength', true, array('max' => 64)))));  
	      
    $this->addDisplayGroup(array('price','duration'), 'priceg');
	         
    // Element: timezone
    $this->addElement('Select', 'timezone', array(
      'label' => 'Timezone',
      'value' => $settings->getSetting('core.locale.timezone'),
      'multiOptions' => array(
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
      ),
      'tabindex' => $tabIndex++,
    ));
    $this->timezone->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    // Element: language

    // Languages
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    //$currentLocale = Zend_Registry::get('Locale')->__toString();
    // Prepare default langauge
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if( !in_array($defaultLanguage, $languageList) ) {
      if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }

    // Prepare language name list
    $localeObject = Zend_Registry::get('Locale');
    
    $languageNameList = array();
    $languageDataList = Zend_Locale_Data::getList($localeObject, 'language');
    $territoryDataList = Zend_Locale_Data::getList($localeObject, 'territory');

    foreach( $languageList as $localeCode ) {
      $languageNameList[$localeCode] = Zend_Locale::getTranslation($localeCode, 'language', $localeCode);
      if( empty($languageNameList[$localeCode]) ) {
        list($locale, $territory) = explode('_', $localeCode);
        $languageNameList[$localeCode] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
      }
    }
    $languageNameList = array_merge(array(
      $defaultLanguage => $defaultLanguage
    ), $languageNameList);

    if(count($languageNameList)>1){
      $this->addElement('Select', 'language', array(
        'label' => 'Language',
        'multiOptions' => $languageNameList,
        'tabindex' => $tabIndex++,
      ));
      $this->language->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }
    else{
      $this->addElement('Hidden', 'language', array(
        'value' => current((array)$languageNameList),
        'order' => 1002
      ));
    }

    $this->addDisplayGroup(array('timezone','language'), 'ltg');

    // Element: captcha
    if( Engine_Api::_()->getApi('settings', 'core')->core_spam_signup ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
        'tabindex' => $tabIndex++,
      )));
    }
    
    if( $settings->getSetting('user.signup.terms', 1) == 1 ) {
      // Element: terms
      $description = Zend_Registry::get('Zend_Translate')->_('I have read and agree to the <a target="_blank" href="%s/help/terms">terms of service</a>.');
      $description = sprintf($description, Zend_Controller_Front::getInstance()->getBaseUrl());

      $this->addElement('Checkbox', 'terms', array(
        'label' => 'Terms of Service',
        'description' => $description,
        'required' => true,
        'validators' => array(
          'notEmpty',
          array('GreaterThan', false, array(0)),
        ),
        'tabindex' => $tabIndex++,
      ));
      $this->terms->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
      //$this->terms->getDecorator('Label')->setOption('escape', false);

      $this->terms->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
          ->addDecorator('DivDivDivWrapper');

      //$this->terms->setDisableTranslator(true);
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => $tabIndex++,
    ));
    
    if( empty($_SESSION['facebook_signup']) ){
      // Init facebook login link
//      if( 'none' != $settings->getSetting('core_facebook_enable', 'none')
//          && $settings->core_facebook_secret ) {
//        $this->addElement('Dummy', 'facebook', array(
//          'content' => User_Model_DbTable_Facebook::loginButton(),
//        ));
//      }
    }
    
    if( empty($_SESSION['twitter_signup']) ){
      // Init twitter login link
//      if( 'none' != $settings->getSetting('core_twitter_enable', 'none')
//          && $settings->core_twitter_secret ) {
//        $this->addElement('Dummy', 'twitter', array(
//          'content' => User_Model_DbTable_Twitter::loginButton(),
//        ));
//      }
    } 
    // Set default action
    $action = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true) . '?step=1';
    $this->setAction($action);
  }

  public function checkPasswordConfirm($value, $passwordElement)
  {
    return ( $value == $passwordElement->getValue() );
  }

  public function checkInviteCode($value, $emailElement)
  {
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $select = $inviteTable->select()
      ->from($inviteTable->info('name'), 'COUNT(*)')
      ->where('code = ?', $value)
      ;
      
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.checkemail') ) {
      $select->where('recipient LIKE ?', $emailElement->getValue());
    }
    
    return (bool) $select->query()->fetchColumn(0);
  }

  public function checkBannedEmail($value, $emailElement)
  {
    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
    if ($bannedEmailsTable->isEmailBanned($value)) {
      return false;
    }
    $isValidEmail = true;
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onCheckBannedEmail', $value);
    foreach ((array)$event->getResponses() as $response) {
      if ($response) {
        $isValidEmail = false;
        break;
      }
    }
    return $isValidEmail;
  }

  public function checkBannedUsername($value, $usernameElement)
  {
    $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
    return !$bannedUsernamesTable->isUsernameBanned($value);
  }
}
