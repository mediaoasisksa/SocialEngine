<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 10099 2013-10-19 14:58:40Z ivan $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Plugin_Signup_Account extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';
  protected $_formClass = 'User_Form_Signup_Account';
  protected $_script = array('signup/form/account.tpl', 'user');
  protected $_adminFormClass = 'User_Form_Admin_Signup_Account';
  protected $_adminScript = array('admin-signup/account.tpl', 'user');
  public $email = null;

  public function onView()
  {
    if (!empty($_SESSION['facebook_signup']) ||
      !empty($_SESSION['twitter_signup'])) {

      // Attempt to preload information
      if (!empty($_SESSION['facebook_signup'])) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($facebook && $settings->core_facebook_enable) {
            // Get email address
            $apiInfo = $facebook->api('/me?fields=name,gender,email,locale');
            // General
            $form = $this->getForm();

            if (($emailEl = $form->getElement('email')) && !$emailEl->getValue()) {
              $emailEl->setValue($apiInfo['email']);
            }
            if (($usernameEl = $form->getElement('username')) && !$usernameEl->getValue()) {
              $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $apiInfo['name']));
            }

            // Locale
            $localeObject = new Zend_Locale($apiInfo['locale']);
            if (($localeEl = $form->getElement('locale')) && !$localeEl->getValue()) {
              $localeEl->setValue($localeObject->toString());
            }
            if (($languageEl = $form->getElement('language')) && !$languageEl->getValue()) {
              if (isset($languageEl->options[$localeObject->toString()])) {
                $languageEl->setValue($localeObject->toString());
              } else if (isset($languageEl->options[$localeObject->getLanguage()])) {
                $languageEl->setValue($localeObject->getLanguage());
              }
            }
          }
        } catch (Exception $e) {
          // Silence?
        }
      }

      // Attempt to preload information
      if (!empty($_SESSION['twitter_signup'])) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($twitter && $settings->core_twitter_enable) {
            $accountInfo = $twitter->account->verify_credentials();

            // General
            $this->getForm()->populate(array(
              //'email' => $apiInfo['email'],
              'username' => preg_replace('/[^A-Za-z]/', '', $accountInfo->name), // $accountInfo->screen_name
              // 'timezone' => $accountInfo->utc_offset, (doesn't work)
              'language' => $accountInfo->lang,
            ));
          }
        } catch (Exception $e) {
          // Silence?
        }
      }
    }

    if (isset($_SESSION['Payment_Plugin_Signup_Subscription'])) {
      try {
        $packageId = $_SESSION['Payment_Plugin_Signup_Subscription']['data']['package_id'];
        $package = Engine_Api::_()->getItem('payment_package', $packageId);
        if (empty($package)) {
          return;
        }

        $profileTypeIds = Engine_Api::_()->getDbtable('mapProfileTypeLevels', 'authorization')
          ->getMappedProfileTypeIds($package->level_id);
        if (empty($profileTypeIds)) {
          return;
        }

        $form = $this->getForm();
        if (count($profileTypeIds) == 1) {
          $form->removeElement('profile_type');
          // Hidden Profile Types
          $form->addElement('Hidden', 'profile_type', array(
            'value' => $profileTypeIds[0]['profile_type_id']
          ));
          return;
        }

        $profileTypes = Engine_Api::_()->getDbtable('options', 'authorization')->getAllProfileTypes();
        $profileTypeOptions = array('' => '');
        foreach ($profileTypes as $profileType) {
          $showOption  = false;
          foreach($profileTypeIds as $profileTypeId) {
            if ($profileType->option_id === $profileTypeId['profile_type_id']) {
              $showOption = true;
            }
          }
          if ($showOption) {
            $profileTypeOptions[$profileType->option_id] = $profileType->label;
          }
        }
        $form->getElement('profile_type')->setMultiOptions($profileTypeOptions);
      } catch (Exception $ex) {
          // Silence?
      }
    }
  }
  
  public function onSubmit(Zend_Controller_Request_Abstract $request) {
    // Form was not valid
    if(!$this->getForm()->isValid($request->getPost()) ) {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }
    //verification code mail send
    $stepTable = Engine_Api::_()->getDbTable('signup', 'user');
    $stepRow = $stepTable->fetchRow($stepTable->select()->where('class = ?', 'User_Plugin_Signup_Otp'));
    if($stepRow->enable) {
      $email = $_POST[$this->getForm()->getEmailElementFieldName()];
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
    }
    parent::onSubmit($request);
  }

  public function onProcess()
  {
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
    $data = $this->getSession()->data;

    // Add email and code to invite session if available
    $inviteSession = new Zend_Session_Namespace('invite');
    if (isset($data['email'])) {
      $inviteSession->signup_email = $data['email'];
    }
    if (isset($data['code'])) {
      $inviteSession->signup_code = $data['code'];
    }

    if ($random) {
      $data['password'] = Engine_Api::_()->user()->randomPass(10);
    }

    if (!empty($data['language'])) {
      $data['locale'] = $data['language'];
    }

    // Create user
    // Note: you must assign this to the registry before calling save or it
    // will not be available to the plugin in the hook
    $this->_registry->user = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
    $user->setFromArray($data);
    $user->save();
    
    if($data['profile_type'] == 13) {
        $user->level_id = 6;
        $user->save();
    }
    
    if($data['profile_type'] == 13) {
        $field_id1 = 36;
        $field_id2 = 37;
        
        
            // Process
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
        $valuesss['price'] = $data['price'];
        $valuesss['description'] = $data['description'];
        $valuesss['slug'] = "service-" . $provider->getIdentity();
        $valuesss['category_id'] = $data['consulatant_category_id'];
        $valuesss['duration'] = $data['duration'] * 60;
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
          
          
        $table = Engine_Api::_()->getItemTable('sitebooking_ser');
        $valuesss = array();
        $valuesss['title'] =  "Mentor Service";
        $valuesss['price'] = 300;
        $valuesss['description'] = $data['description'];
        $valuesss['slug'] = "mentor-service-" . $provider->getIdentity();
        $valuesss['category_id'] = $data['consulatant_category_id'];
        $valuesss['type'] = 2;
        $valuesss['duration'] = $data['duration'] * 60;
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
          
          
          $db->commit();
        }
        catch (Execption $e) {
          $db->rollBack();
          
        }
    } else if($data['profile_type'] == 114) {
        $field_id1 = 43;
        $field_id2 = 44;
        
        
            // Process
    $provider = Engine_Api::_()->getItemTable('sitebooking_pro');
    $location = Engine_Api::_()->getItemTable('sitebooking_providerlocation');
    $db = $provider->getAdapter();
    $db->beginTransaction();

    try {
          //$formValues = $form->getValues();
    
        //   $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
        //   $providers = $providerTable->fetchRow($providerTable->select()->where('slug LIKE ?',$formValues['slug']));
    
        //     if(!empty($providers)){
        //       return $form->addError('URL: This URL is already taken, please try another');
        //     }
            // $valuess = array();
            // $valuess['title'] = $data['fname'] . ' ' . $data['lname'];
            // $valuess['slug'] = "provider-" . $user->getIdentity();
            // $valuess['designation'] = $data['specialist'];
            // $valuess['description'] = "Mentoring Service";
            // $valuess['status'] =1;
            // $valuess['timezone'] =$data['timezone'];
            // $valuess['location'] =$data['country'];
            // $valuess['city'] =$data['city'];
            // $valuess['view'] ="everyone";
            // $valuess['comment'] ="registered";
            // $valuess['owner_id'] =$user->getIdentity();
            // $valuess['approved'] =1;
            // $provider = $provider->createRow();
            // $provider->setFromArray($valuess);
            // $provider->save();
    
   
    //Array ( [title] => dsadfasdf [description] => asdfasd [price] => 125 [category_id] => 1 [first_level_category_id] => [second_level_category_id] => [duration] => 1800 [view] => everyone [comment] => registered [photo] => [commission] => 1 [fields] => Array ( [0_0_1] => ) [slug] => dsadfasdf )
    
    //Array ( [title] => Consultation by Name [slug] => Consultation by Name [tags] => saddasda [designation] => Doctor [description] => Consultation by Name [photo] => [coverPhoto] => [status] => 1 [timezone] => Europe/Moscow [location] => asdadasd [view] => everyone [comment] => registered [location_region] => [owner_id] => 1678 [approved] => 1 )
    
        //   if( !empty($formValues['photo']) ) {
        //     $provider->setPhoto($form->photo);
        //   }
    
        //   if( !empty($formValues['coverPhoto']) ) {
        //     $provider->setCoverPhoto($form->coverPhoto);
        //   }
    
        //   $tags = preg_split('/[,]+/', $values['tags']);
        //   $provider->tags()->addTagMaps($viewer, $tags);
    
        //   //location
        //   $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');
    
        //   if($locationFieldcoreSettings === "yes") {
        //     $location = $location->createRow();
        //     $formValues = $form->getValues();
        //     $locationValues = $formValues['location_region'];
        //     $locationValues = json_decode($locationValues, true);
        //     $locationValues['pro_id'] = $provider->getIdentity();
        //     $location->setFromArray($locationValues);
        //     $location->save();
        //   }
    
        //   // Auth
        //   $auth = Engine_Api::_()->authorization()->context;
        //   $roles = array('owner_network', 'registered', 'everyone');
    
        //   $viewMax = array_search($valuess['view'], $roles);
    
        //   foreach( $roles as $i => $role ) {
        //       $auth->setAllowed($provider, $role, 'view', ($i <= $viewMax));
        //   }
    
        //   $roles = array('owner_network', 'registered', 'everyone');
    
        //   $viewMax = array_search($valuess['comment'], $roles);
    
        //   foreach( $roles as $i => $role ) {
        //       $auth->setAllowed($provider, $role, 'comment', ($i <= $viewMax));
        //   }
    
    
    //  $table = Engine_Api::_()->getItemTable('sitebooking_ser');
    
    //  $valuesss = array();
    //         $valuesss['title'] =  $data['fname'] . ' ' . $data['lname'];;
    //         $valuesss['price'] = $data['price'];
    //         $valuesss['description'] = "Mentoring Service";
    //         $valuesss['slug'] = "service-" . $provider->getIdentity();
    //         $valuesss['category_id'] = 1;
    //         $valuesss['duration'] = $data['duration'] * 60;
    //         $valuesss['view'] = "everyone";
    //         $valuesss['comment'] ="registered";
    //         $valuesss['owner_id'] = $user->getIdentity();
    //         $valuesss['approved'] = 1;
    //         $valuesss['parent_type'] = 'sitebooking_pro';
    //         $valuesss['parent_id'] = $provider->getIdentity();
    //         $valuesss['status'] = 1;
            
        //     $providerS = $table->createRow();
        //   $providerS->setFromArray($valuesss);
        //   $providerS->save();
          
        //      $auth = Engine_Api::_()->authorization()->context;
        //   $roles = array('owner_network', 'registered', 'everyone');
    
        //   $viewMax = array_search($valuesss['view'], $roles);
    
        //   foreach( $roles as $i => $role ) {
        //       $auth->setAllowed($providerS, $role, 'view', ($i <= $viewMax));
        //   }
    
        //   $roles = array('owner_network', 'registered', 'everyone');
    
        //   $viewMax = array_search($valuesss['comment'], $roles);
    
        //   foreach( $roles as $i => $role ) {
        //       $auth->setAllowed($providerS, $role, 'comment', ($i <= $viewMax));
        //   }
          $db->commit();
        }
        catch (Execption $e) {
          $db->rollBack();
          
        }
        }else {
          $field_id1 = 47;
        $field_id2 = 48;
    }
    $values = Engine_Api::_()->fields()->getTable('user', 'values');
    $valueRow = $values->createRow();
    $valueRow->field_id = 1;
    $valueRow->item_id = $user->getIdentity();
    $valueRow->value = $data['profile_type'];
    $valueRow->privacy = 'everyone';
    $valueRow->save();
    $valueRow = $values->createRow();
    $valueRow->field_id = $field_id1;
    $valueRow->item_id = $user->getIdentity();
    $valueRow->value = $data['fname'];
    $valueRow->privacy = 'everyone';
    $valueRow->save();

    $valueRow = $values->createRow();
    $valueRow->field_id = $field_id2;
    $valueRow->item_id = $user->getIdentity();
    $valueRow->value = $data['lname'];
    $valueRow->privacy = 'everyone';
    $valueRow->save();
    $user->description = $data['description'];
    if(isset($data['jobtitle'])) {
         $user->jobtitle = $data['jobtitle'];
    }
    
    if(isset($data['qualifications'])) {
         $user->qualifications = $data['qualifications'];
    }
    
    if(isset($data['history'])) {
         $user->history = $data['history'];
    }
    $user->save();
        
           
    
    if($data['profile_type'] == 17) {
        if(isset($data['qualifications'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 69;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['qualifications'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
       if(isset($data['jobtitle'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 68;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['jobtitle'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
      if(isset($data['twitter'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 73;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['twitter'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
        
              if(isset($data['linkedin'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 72;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['linkedin'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['studentorprofessional'])) {
        if($data['studentorprofessional'] == "Professional") {
            $value = 21;
        } else if($data['studentorprofessional'] == "Student") {
            $value = 22;
        } 
        $valueRow = $values->createRow();
        $valueRow->field_id = 67;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value =  $value;
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['educationlevel'])) {
        if($data['educationlevel'] == "highschool") {
            $value = 23;
        } else if($data['educationlevel'] == "bachelordegree") {
            $value = 24;
        } else if($data['educationlevel'] == "masterdegree") {
            $value = 25;
        } else if($data['educationlevel'] == "doctorate") {
            $value = 26;
        } else {
             $value = 27;
        }
        $valueRow = $values->createRow();
        $valueRow->field_id = 70;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value =  $value;
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
   
        if(isset($data['educationinstitute'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 71;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['educationinstitute'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
            if(isset($data['gender'])) {
        if($data['gender'] == "male") {
            $value = 18;
        } else if($data['gender'] == "female") {
            $value = 19;
        } else {
             $value = 20;
        }
        $valueRow = $values->createRow();
        $valueRow->field_id = 49;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value =  $value;
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    if(isset($data['contactno'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 64;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['contactno'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['country'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 65;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['country'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['city'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 66;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['city'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    

    } else {
        
              if(isset($data['qualifications'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 58;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['qualifications'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
       if(isset($data['jobtitle'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 57;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['jobtitle'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
      if(isset($data['twitter'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 75;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['twitter'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
        
              if(isset($data['linkedin'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 74;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['linkedin'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['gender'])) {
        if($data['gender'] == "male") {
            $value = 14;
        } else if($data['gender'] == "female") {
            $value = 15;
        } else {
             $value = 16;
        }
        $valueRow = $values->createRow();
        $valueRow->field_id = 38;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value =  $value;
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    if(isset($data['contactno'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 59;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['contactno'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['specialist'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 58;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['specialist'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
        if(isset($data['country'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 60;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['country'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    
    if(isset($data['city'])) {
        $valueRow = $values->createRow();
        $valueRow->field_id = 62;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $data['city'];
        $valueRow->privacy = 'everyone';
        $valueRow->save();
    }
    }
    
    Engine_Api::_()->user()->setViewer($user);
    
     if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $filename = $storage->createFile($this->getForm()->file, array(
              'parent_id' => $user->getIdentity(),
              'parent_type' => 'user_cv',
              'user_id' => $user->getIdentity(),
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $user->file_id = $filename->file_id;
          $user->save();
        }
    
    // if(isset($_FILES['cvupload'])) { 
    //   $target_dir = "public/admin/cv/";
    //     $file_name = $_FILES['cvupload']['name'];
    //     $file_tmp = $_FILES['cvupload']['tmp_name'];
    
    //     if (move_uploaded_file($file_tmp, $target_dir.$file_name)) {
    //         //echo "<h1>File Upload Success</h1>";            
    //     }
    //     else {
    //         //echo "<h1>File Upload not successfull</h1>";
    //     }  
    // }
     
    
    // if(isset($data['cvupload'])) { 
    //     // Check method
    // // if( !$this->getRequest()->isPost() ) {
    // //   return;
    // // }
    // // // Prepare
    // // if( empty($_FILES['cvupload']) ) {
    // //   $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
    // //   return;
    // // }

    // // Prevent evil files from being uploaded
    // // $disallowedExtensions = array('php');
    // // $parts = explode(".", $_FILES['cvupload']['name']);
    // // $name = end($parts);
    // // if( is_array($name) && in_array($name, $disallowedExtensions) ) {
    // //   $this->view->error = 'File type or extension forbidden.';
    // //   return;
    // // }
    
    // // $fileNameExist = Engine_Api::_()->getDbTable('files', 'core')->getFileNameExist($parts[0]);
    // // if(isset($fileNameExist) && !empty($fileNameExist)) {
    // //   $this->view->error = 'File already exists. Please trying to upload another file.';
    // //   return;
    // // }
    // $path = "/home/consul2/public_html/public/core_file";
    // $info = $_FILES['cvupload'];
    // $targetFile = $path . '/' . $info['name'];
    // $vals = array();
    
    // $filesTable = Engine_Api::_()->getDbtable('files', 'core');
    // $storage = Engine_Api::_()->getItemTable('storage_file');

    // $db = Engine_Db_Table::getDefaultAdapter();
    // $db->beginTransaction();
    // try {
    //   $row = $filesTable->createRow();
    //   $row->setFromArray(array());
    //   $row->save();
      
    //   if(!empty($info['name'])) {
    //     $extension = pathinfo($info['name']);
    //     $extension = $extension['extension'];
    //     $row->name = $parts[0];
    //     $row->extension = $extension;
    //     $row->save();

    //     $storageObject = $storage->createFile($info, array(
    //       'parent_id' => $row->getIdentity(),
    //       'parent_type' => 'core_file',
    //       'user_id' => $user->getIdentity(),
    //     ));
    //     // Remove temporary file
    //     @unlink($info['tmp_name']);
    //     $row->storage_path = $storageObject->storage_path;
    //     $row->storage_file_id = $storageObject->file_id;
    //     $row->save();
    //   }
    //   $db->commit();
    // } catch(Exception $e) {
    //   //$db->rollBack();
    //   //throw $e;
    // }
    // }
    
//      if(isset($data['cvupload'])) {
//             $path = "/home/consul2/public_html/public/core_file";
    
   
//  ///home/consul2/public_html/public/admin
//     // Prevent evil files from being uploaded
//     $disallowedExtensions = array('php');
//     $parts = explode(".", $_FILES['cvupload']['name']);
//     $name = end($parts);
   
    


//     $info = $_FILES['cvupload'];
//     $targetFile = $path . '/' . $info['name'];
//     $vals = array();
    
//     $filesTable = Engine_Api::_()->getDbtable('files', 'core');
//     $storage = Engine_Api::_()->getItemTable('storage_file');

//     $db = Engine_Db_Table::getDefaultAdapter();
//     $db->beginTransaction();
//     try {
//       $row = $filesTable->createRow();
//       $row->setFromArray(array());
//       $row->save();
      
//       if(!empty($info['name'])) {
//         $extension = pathinfo($info['name']);
//         $extension = $extension['extension'];
//         $row->name = $parts[0];
//         $row->extension = $extension;
//         $row->save();

//         $storageObject = $storage->createFile($info, array(
//           'parent_id' => $row->getIdentity(),
//           'parent_type' => 'core_file',
//           'user_id' => $user->getIdentity(),
//         ));
//         //print_r($storageObject);die;
//         // Remove temporary file
//         @unlink($info['tmp_name']);
//         $row->storage_path = $storageObject->storage_path;
//         $row->storage_file_id = $storageObject->file_id;
//         $row->save();
//       }
//       $db->commit();
//     } catch(Exception $e) {
//       //$db->rollBack();
//       //throw $e;
//     }
//         }
    // Increment signup counter
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

    if ($user->verified && $user->enabled) {
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
    if ($random) {
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
        if ($emailadmin) {
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

        $mailType = ($random ? 'core_verification_password' : 'core_verification');

        $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'verify',
          'token' => Engine_Api::_()->user()->getVerifyToken($user->getIdentity()),
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
      $this->_registry->mailParams = $mailParams;
      $this->_registry->mailType = $mailType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }

    if (!empty($mailAdminType)) {
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
    if (!empty($_SESSION['facebook_signup'])) {
      try {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if ($facebook && $settings->core_facebook_enable) {
          $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            //'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        }
      } catch (Exception $e) {
        // Silence
        if ('development' == APPLICATION_ENV) {
          echo $e;
        }
      }
    }

    // Attempt to connect twitter
    if (!empty($_SESSION['twitter_signup'])) {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if ($twitter && $twitterOauth && $settings->core_twitter_enable) {
          $accountInfo = $twitter->account->verify_credentials();
          $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $accountInfo->id,
            'twitter_token' => $twitterOauth->getToken(),
            'twitter_secret' => $twitterOauth->getTokenSecret(),
          ));
        }
      } catch (Exception $e) {
        // Silence?
        if ('development' == APPLICATION_ENV) {
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
    if ($values['inviteonly'] == 1) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }
  
    protected function _getPath($key = 'path')
  {
    return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
  }

  protected function _getRelPath($path, $basePath = null)
  {
    if( null === $basePath ) $basePath = $this->_basePath;
    $path = realpath($path);
    $basePath = realpath($basePath);
    $relPath = trim(str_replace($basePath, '', $path), '/\\');
    return $relPath;
  }
  
  protected function _checkPath($path, $basePath)
  {
    // Sanitize
    //$path = preg_replace('/^[a-z0-9_.-]/', '', $path);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    // Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);
    
    // Check if this is a parent of the base path
    if( $basePath != $path && strpos($basePath, $path) !== false ) {
      return $this->_helper->redirector->gotoRoute(array());
    }

    return $path;
  }
}
