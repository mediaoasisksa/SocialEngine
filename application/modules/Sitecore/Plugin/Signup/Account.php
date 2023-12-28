<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Sitecore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 10099 2013-10-19 14:58:40Z ivan $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Sitecore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */

/*
 * Sitecore_Plugin_Signup_UserAccount This is copy the complete code of User_Plugin_Signup_Account
 * from application/modules/User/Plugin/Signup/Account.php, For latest code you can simply copy the code are paste here
 */
class Sitecore_Plugin_Signup_UserAccount extends Core_Plugin_FormSequence_Abstract
{

  protected $_name = 'account';
  protected $_formClass = 'User_Form_Signup_Account';
  protected $_script = array('signup/form/account.tpl', 'user');
  protected $_adminFormClass = 'User_Form_Admin_Signup_Account';
  protected $_adminScript = array('admin-signup/account.tpl', 'user');
  public $email = null;

  public function onView()
  {
    if( !empty($_SESSION['facebook_signup']) ||
      !empty($_SESSION['twitter_signup']) ||
      !empty($_SESSION['janrain_signup']) ) {

      // Attempt to preload information
      if( !empty($_SESSION['facebook_signup']) ) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if( $facebook && $settings->core_facebook_enable ) {
            // Get email address
            $apiInfo = $facebook->api('/me?fields=name,gender,email,locale');
            // General
            $form = $this->getForm();

            if( ($emailEl = $form->getElement('email')) && !$emailEl->getValue() ) {
              $emailEl->setValue($apiInfo['email']);
            }
            if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
              $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $apiInfo['name']));
            }

            // Locale
            $localeObject = new Zend_Locale($apiInfo['locale']);
            if( ($localeEl = $form->getElement('locale')) && !$localeEl->getValue() ) {
              $localeEl->setValue($localeObject->toString());
            }
            if( ($languageEl = $form->getElement('language')) && !$languageEl->getValue() ) {
              if( isset($languageEl->options[$localeObject->toString()]) ) {
                $languageEl->setValue($localeObject->toString());
              } else if( isset($languageEl->options[$localeObject->getLanguage()]) ) {
                $languageEl->setValue($localeObject->getLanguage());
              }
            }
          }
        } catch( Exception $e ) {
          // Silence?
        }
      }

      // Attempt to preload information
      if( !empty($_SESSION['twitter_signup']) ) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if( $twitter && $settings->core_twitter_enable ) {
            $accountInfo = $twitter->account->verify_credentials();

            // General
            $this->getForm()->populate(array(
              //'email' => $apiInfo['email'],
              'username' => preg_replace('/[^A-Za-z]/', '', $accountInfo->name), // $accountInfo->screen_name
              // 'timezone' => $accountInfo->utc_offset, (doesn't work)
              'language' => $accountInfo->lang,
            ));
          }
        } catch( Exception $e ) {
          // Silence?
        }
      }

      // Attempt to preload information
      if( !empty($_SESSION['janrain_signup']) &&
        !empty($_SESSION['janrain_signup_info']) ) {
        try {
          $form = $this->getForm();
          $info = $_SESSION['janrain_signup_info'];

          if( ($emailEl = $form->getElement('email')) && !$emailEl->getValue() && !empty($info['verifiedEmail']) ) {
            $emailEl->setValue($info['verifiedEmail']);
          }
          if( ($emailEl = $form->getElement('email')) && !$emailEl->getValue() && !empty($info['email']) ) {
            $emailEl->setValue($info['email']);
          }

          if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() && !empty($info['preferredUsername']) ) {
            $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $info['preferredUsername']));
          }
        } catch( Exception $e ) {
          // Silence?
        }
      }
    }
  }

  public function onProcess()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $random = ($settings->getSetting('user.signup.random', 0) == 1);
    $emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
    if( $emailadmin ) {
      // the signup notification is emailed to the first SuperAdmin by default
      $users_table = Engine_Api::_()->getDbtable('users', 'user');
      $users_select = $users_table->select()
        ->where('level_id = ?', 1)
        ->where('enabled >= ?', 1);
      $super_admin = $users_table->fetchRow($users_select);
    }
    $data = $this->getSession()->data;

    // Add email and code to invite session if available
    $inviteSession = new Zend_Session_Namespace('invite');
    if( isset($data['email']) ) {
      $inviteSession->signup_email = $data['email'];
    }
    if( isset($data['code']) ) {
      $inviteSession->signup_code = $data['code'];
    }

    if( $random ) {
      $data['password'] = Engine_Api::_()->user()->randomPass(10);
    }

    if( !empty($data['language']) ) {
      $data['locale'] = $data['language'];
    }

    // Create user
    // Note: you must assign this to the registry before calling save or it
    // will not be available to the plugin in the hook
    $this->_registry->user = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
    $user->setFromArray($data);
    $user->save();

    Engine_Api::_()->user()->setViewer($user);

    // Increment signup counter
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

    if( $user->verified && $user->enabled ) {
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
    if( $random ) {
      $mailParams['password'] = $data['password'];
    }

    // Mail stuff
    switch( $settings->getSetting('user.signup.verifyemail', 0) ) {
      case 0:
        // only override admin setting if random passwords are being created
        if( $random ) {
          $mailType = 'core_welcome_password';
        }
        if( $emailadmin ) {
          $mailAdminType = 'notify_admin_user_signup';
          $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
          $date = new DateTime("now", new DateTimeZone($siteTimezone));
          $mailAdminParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => $date->format('F j, Y, g:i a'),
            'recipient_title' => $super_admin->displayname,
            'object_title' => $user->displayname,
            'object_link' => $user->getHref(),
          );
        }
        break;

      case 1:
        // send welcome email
        $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
        if( $emailadmin ) {
          $mailAdminType = 'notify_admin_user_signup';
          $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
          $date = new DateTime("now", new DateTimeZone($siteTimezone));
          $mailAdminParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => $date->format('F j, Y, g:i a'),
            'recipient_title' => $super_admin->displayname,
            'object_title' => $user->getTitle(),
            'object_link' => $user->getHref(),
          );
        }
        break;

      case 2:
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

        $mailType = ($random ? 'core_verification_password' : 'core_verification');

        $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'verify',
          'token' => Engine_Api::_()->user()->getVerifyToken($user->getIdentity()),
          'verify' => $verify_row->code
          ), 'user_signup', true);

        if( $emailadmin ) {
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

    if( !empty($mailType) ) {
      $this->_registry->mailParams = $mailParams;
      $this->_registry->mailType = $mailType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }

    if( !empty($mailAdminType) ) {
      $this->_registry->mailAdminParams = $mailAdminParams;
      $this->_registry->mailAdminType = $mailAdminType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }

    // Attempt to connect facebook
    if( !empty($_SESSION['facebook_signup']) ) {
      try {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $facebook && $settings->core_facebook_enable ) {
          $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            //'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect twitter
    if( !empty($_SESSION['twitter_signup']) ) {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $twitter && $twitterOauth && $settings->core_twitter_enable ) {
          $accountInfo = $twitter->account->verify_credentials();
          $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $accountInfo->id,
            'twitter_token' => $twitterOauth->getToken(),
            'twitter_secret' => $twitterOauth->getTokenSecret(),
          ));
        }
      } catch( Exception $e ) {
        // Silence?
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect twitter
    if( !empty($_SESSION['janrain_signup']) ) {
      try {
        $janrainTable = Engine_Api::_()->getDbtable('janrain', 'user');
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $info = $_SESSION['janrain_signup_info'];
        if( $settings->core_janrain_enable ) {
          $janrainTable->insert(array(
            'user_id' => $user->getIdentity(),
            'identifier' => $info['identifier'],
            'provider' => $info['providerName'],
            'token' => (string) @$_SESSION['janrain_signup_token'],
          ));
        }
      } catch( Exception $e ) {
        // Silence?
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    if( $values['inviteonly'] == 1 ) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }

}

class User_Plugin_Signup_Account extends Sitecore_Plugin_Signup_UserAccount
{

  protected $_enableSubscription = false;
  protected $_enableSitelogin = false;
  protected $_otpPlugin;

  public function init()
  {
    if( Engine_Api::_()->hasModuleBootstrap("siteotpverifier") ) {
      $this->_otpPlugin = new Siteotpverifier_Plugin_Signup_Account();
      $this->_otpPlugin->setPlugin($this);
    }
    $this->_enableSubscription = Engine_Api::_()->hasModuleBootstrap("sitesubscription");
    $this->_enableSitelogin = Engine_Api::_()->hasModuleBootstrap("sitelogin");
    if( $this->_enableSitelogin ) {
      $this->_script = array('signup/form/account.tpl', 'sitelogin');
    }
  }

//  public function getSession()
//  {
//    if( is_null($this->_session) ) {
//      $this->_session = new Zend_Session_Namespace('User_Plugin_Signup_Account');
//      if( !isset($this->_session->active) ) {
//        $this->_session->active = true;
//      }
//    }
//    return $this->_session;
//  }

  public function getForm()
  {
    if( !is_null($this->_form) ) {
      return $this->_form;
    }
    parent::getForm();

    $this->_getSiteotpverifierBaseForm();
    $this->_getSubscriptionsBaseForm();
    $this->_getSiteloginBaseForm();
    return $this->_form;
  }

  public function onProcess()
  {
    parent::onProcess();
    $this->_processBaseOnOtp();
    $this->_processBaseOnSitelogin();
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    if( $this->_otpPlugin ) {
      $this->_otpPlugin->onSubmitBefore($request);
    }
    parent::onSubmit($request);

    if( $this->_otpPlugin ) {
      $this->_otpPlugin->onSubmitAfter($request);
    }
  }

  public function onView()
  {
    parent::onView();
    $this->_onViewBaseOnSitelogin();
  }

  public function getRegistry()
  {
    return $this->_registry;
  }

  protected function _onViewBaseOnSitelogin()
  {
    if( !$this->_enableSitelogin ) {
      return;
    }
    //Zend_Registry::set('siteloginSignupPopUp', 0);
    $form = $this->getForm();
    $front = Zend_Controller_Front::getInstance();
    $value = $front->getRequest();
    if( isset($value->error_flag) && !empty($value->error_flag) ) {
      $form->addError('Someone has already registered this email address, please use another one.');
      unset($_SESSION['linked_signup']);
      unset($_SESSION['linkedin_access_token']);
      unset($_SESSION['facebook_signup']);
      unset($_SESSION['google_signup']);
      unset($_SESSION['google_access_token']);
      unset($_SESSION['outlook_signup']);
      unset($_SESSION['outlook_access_token']);
      unset($_SESSION['vk_signup']);
      unset($_SESSION['vk_access_token']);
      unset($_SESSION['flickr_signup']);
      unset($_SESSION['flickr_access_token']);
      unset($_SESSION['yahoo_signup']);
      unset($_SESSION['yahoo_access_token']);
      unset($_SESSION['pinterest_signup']);
      unset($_SESSION['pinterest_access_token']);
      unset($_SESSION['instagram_signup']);
      unset($_SESSION['instagram_access_token']);
      unset($_SESSION['access_token']);
    }

    // Attempt to preload information
    if( !empty($_SESSION['google_signup']) ) {
      try {
        $apiInfo = Engine_Api::_()->getDbtable('google', 'sitelogin')->getApi();
        // General
        $email = $form->getEmailElementFieldName();
        if( ($emailEl = $form->getElement($email)) && !$emailEl->getValue() ) {
          $emailEl->setValue($apiInfo->email);
        }
        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($apiInfo->name);
          $usernameEl->setValue($username);
        }

        // Locale
        $localeObject = new Zend_Locale($apiInfo->locale);
        if( ($localeEl = $form->getElement('locale')) && !$localeEl->getValue() ) {
          $localeEl->setValue($localeObject->toString());
        }
        if( ($languageEl = $form->getElement('language')) && !$languageEl->getValue() ) {
          if( isset($languageEl->options[$localeObject->toString()]) ) {
            $languageEl->setValue($localeObject->toString());
          } else if( isset($languageEl->options[$localeObject->getLanguage()]) ) {
            $languageEl->setValue($localeObject->getLanguage());
          }
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }
    // Attempt to preload information
    if( !empty($_SESSION['linkedin_signup']) ) {
      try {
        $userDetails = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->fetch();
        $email = $form->getEmailElementFieldName();
        if( ($emailEl = $form->getElement($email)) && !$emailEl->getValue() ) {
          $emailEl->setValue($userDetails->emailAddress);
        }

        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->firstName, $userDetails->lastName);
          $usernameEl->setValue($username);
        }

        if( ($locationEl = $form->getElement('location')) && !$locationEl->getValue() ) {
          $locationEl->setValue(preg_replace('/[^A-Za-z]/', '', $userDetails->location->name));
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload information
    if( !empty($_SESSION['instagram_signup']) ) {
      try {
        $userDetailsdata = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
        $userDetails = $userDetailsdata->data;
        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->full_name);
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload information
    if( !empty($_SESSION['pinterest_signup']) ) {
      try {
        $userDetailsdata = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
        $userDetails = $userDetailsdata->data;
        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->first_name, $userDetails->last_name);
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload yahoo
    if( !empty($_SESSION['yahoo_signup']) ) {
      try {
        $userDetailsdata = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
        $userDetails = $userDetailsdata->profile;
        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          if( !empty($userDetails->givenName) || !empty($userDetails->familyName) ) {
            $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->givenName, $userDetails->familyName);
          } else {
            $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->nickname);
          }
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload vk
    if( !empty($_SESSION['vk_signup']) ) {
      try {
        $userDetailsdata = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();
        $userDetails = $userDetailsdata->response[0];
        if( isset($_SESSION['vk_email']) && !empty($_SESSION['vk_email']) ) {
          $email = $form->getEmailElementFieldName();
          if( ($emailEl = $form->getElement($email)) && !$emailEl->getValue() ) {
            $emailEl->setValue($_SESSION['vk_email']);
          }
        }
        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->first_name, $userDetails->last_name);
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload outlook
    if( !empty($_SESSION['outlook_signup']) ) {
      try {
        $userDetails = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->fetch();
        $email = $form->getEmailElementFieldName();
        if( ($emailEl = $form->getElement($email)) && !$emailEl->getValue() ) {
          $emailEl->setValue($userDetails->userPrincipalName);
        }

        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails->givenName, $userDetails->surname);
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }

    // Attempt to preload flickr
    if( !empty($_SESSION['flickr_signup']) ) {
      try {
        $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();

        if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
          $username = Engine_Api::_()->sitelogin()->getUserName($userDetails['name']);
          $usernameEl->setValue($username);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }
  }

  protected function _getSiteotpverifierBaseForm()
  {
    if( !$this->_otpPlugin ) {
      return;
    }
    $this->_form = $this->_otpPlugin->addFields($this->_form);
  }

  protected function _getSubscriptionsBaseForm()
  {
    if( !$this->_enableSubscription ) {
      return;
    }
    // Get Profile-type mapping setting
    $getMappingSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitesubscription.profile.mapping', '0');

    if( empty($getMappingSetting) || !$this->_form->profile_type || strpos($this->_form->profile_type->getType(), 'Form_Element_Select') === false ) {
      return;
    }


    // Get package id from subscription session 
    $subscription_session = new Zend_Session_Namespace('Sitesubscription_Plugin_Signup_Subscription');
    $package_id = $subscription_session->data['package_id'];

    if( $package_id == null ) {
      return;
    }
    $packagesTable = Engine_Api::_()->getDbtable('profiletypemapping', 'sitesubscription');
    $package = $packagesTable->select()->where('`package_id` = ?', $package_id)->limit(1)->query()->fetch();
    if( $package && !empty($package['option_id']) && $this->_form->profile_type->getMultiOption($package['option_id']) ) {
      $this->_form->removeElement($this->_form->profile_type);
      $this->_form->addElement('Hidden', 'profile_type', array(
        'order' => 56890,
        'value' => (string) $package['option_id']
      ));
    }
  }

  protected function _getSiteloginBaseForm()
  {
    if( !$this->_enableSitelogin ) {
      return;
    }
    if( !empty($_SESSION['outlook_signup']) || !empty($_SESSION['vk_signup']) ||
      !empty($_SESSION['yahoo_signup']) || !empty($_SESSION['flickr_signup']) ||
      !empty($_SESSION['pinterest_signup']) || !empty($_SESSION['instagram_signup']) || !empty($_SESSION['google_signup']) || !empty($_SESSION['linkedin_signup']) ) {

      $this->_form->removeElement('password');
      $this->_form->removeElement('passconf');
    }
  }

  protected function _processBaseOnOtp()
  {
    if( !$this->_otpPlugin ) {
      return;
    }
    $this->_otpPlugin->onProcess();
  }

  protected function _processBaseOnSitelogin()
  {
    if( !$this->_enableSitelogin ) {
      return;
    }
    $user = $this->_registry->user;
    // Attempt to connect google
    if( !empty($_SESSION['google_signup']) ) {
      try {
        $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
        $google = $googleTable->getApi();

        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $tokens = Zend_Json::decode($_SESSION['google_access_token']);

        $loginEnable = Engine_Api::_()->getDbtable('google', 'sitelogin')->googleIntegrationEnabled();

        if( !empty($loginEnable) && isset($google->id) && !empty($google->id) ) {
          $googleTable->insert(array(
            'user_id' => $user->getIdentity(),
            'google_id' => $google->id,
            'access_token' => $tokens['access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect linkedin
    if( !empty($_SESSION['linkedin_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->linkedinIntegrationEnabled();
        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
        if( isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token']) ) {
          $userDetails = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->fetch();
        }

        if( !empty($loginEnable) && $userDetails->id ) {
          $linkedinTable->insert(array(
            'user_id' => $user->getIdentity(),
            'linkedin_id' => $userDetails->id,
            'access_token' => $_SESSION['linkedin_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect instagram
    if( !empty($_SESSION['instagram_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
        $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
        if( isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token']) ) {
          $userDetailsdata = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
          $userDetails = $userDetailsdata->data;
        }
        if( !empty($loginEnable) && $userDetails->id ) {
          $instagramTable->insert(array(
            'user_id' => $user->getIdentity(),
            'instagram_id' => $userDetails->id,
            'access_token' => $_SESSION['instagram_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect pinterest
    if( !empty($_SESSION['pinterest_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->pinterestIntegrationEnabled();
        $pinterestTable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin');
        if( isset($_SESSION['pinterest_access_token']) && !empty($_SESSION['pinterest_access_token']) ) {
          $userDetailsdata = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
          $userDetails = $userDetailsdata->data;
        }
        if( !empty($loginEnable) && $userDetails->id ) {
          $pinterestTable->insert(array(
            'user_id' => $user->getIdentity(),
            'pinterest_id' => $userDetails->id,
            'access_token' => $_SESSION['pinterest_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect yahoo
    if( !empty($_SESSION['yahoo_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->yahooIntegrationEnabled();
        $yahooTable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin');
        if( isset($_SESSION['yahoo_access_token']) && !empty($_SESSION['yahoo_access_token']) ) {
          $userDetailsdata = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
          $userDetails = $userDetailsdata->profile;
        }
        if( !empty($loginEnable) && $userDetails->guid ) {
          $yahooTable->insert(array(
            'user_id' => $user->getIdentity(),
            'yahoo_id' => $userDetails->guid,
            'access_token' => $_SESSION['yahoo_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect vk
    if( !empty($_SESSION['vk_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('vk', 'sitelogin')->vkIntegrationEnabled();
        $vkTable = Engine_Api::_()->getDbtable('vk', 'sitelogin');
        if( isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token']) ) {
          $userDetailsdata = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();
          $userDetails = $userDetailsdata->response[0];
        }
        if( !empty($loginEnable) && $userDetails->uid ) {
          $vkTable->insert(array(
            'user_id' => $user->getIdentity(),
            'vk_id' => $userDetails->uid,
            'access_token' => $_SESSION['vk_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect outlook
    if( !empty($_SESSION['outlook_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->outlookIntegrationEnabled();
        $outlookTable = Engine_Api::_()->getDbtable('outlook', 'sitelogin');
        if( isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token']) ) {
          $userDetails = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->fetch();
        }
        if( !empty($loginEnable) && $userDetails->id ) {
          $outlookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'outlook_id' => $userDetails->id,
            'access_token' => $_SESSION['outlook_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }

    // Attempt to connect flickr
    if( !empty($_SESSION['flickr_signup']) ) {
      try {
        $loginEnable = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->flickrIntegrationEnabled();
        $flickrTable = Engine_Api::_()->getDbtable('flickr', 'sitelogin');
        if( isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token']) ) {
          $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();
        }
        if( !empty($loginEnable) && $userDetails['id'] ) {
          $flickrTable->insert(array(
            'user_id' => $user->getIdentity(),
            'flickr_id' => $userDetails['id'],
            'access_token' => $_SESSION['flickr_access_token'],
            'expires' => 0,
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
  }

}
