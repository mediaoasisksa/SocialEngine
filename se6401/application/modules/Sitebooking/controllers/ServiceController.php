<?php

class Sitebooking_ServiceController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'view')->isValid() ) return;
  }

  public function indexAction()
  {
    $this->_helper->content->setEnabled();
  }

  public function createAction()
  {

    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'create')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $autoApproveProvider = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'approve');
    
    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);

    $this->view->notApprove = 1;
    if($autoApproveProvider == 0 && $provider->approved == 0) {
      $this->view->notApprove = 0;
    } else {
      if($provider->approved == 0) { 
        $this->view->notApprove = 0;
      }
    }

    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'create')->isValid() ) return;

    $local_language = $this->view->locale()->getLocale()->__toString();
    $local_language = explode('_', $local_language);
    $this->view->language = $local_language[0];

    $orientation = $this->view->layout()->orientation;
    if ($orientation == 'right-to-left') {
        $this->view->directionality = 'rtl';
    } else {
        $this->view->directionality = 'ltr';
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }
    $this->_helper->content->setEnabled();

    $this->view->status = $provider->status;

    $this->view->form = $form = new Sitebooking_Form_Service_Create();

    // checki auto approving permission
    $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_ser', $viewer, 'approve');
    if($autoApprove == 1){
      $approved = 1;
    }else{
      $approved = 0;
    }

    //CHECKING SERVICE CREATION QUOTA

    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitebooking_ser', 'max');

    $table = Engine_Api::_()->getItemTable('sitebooking_ser');

    $service_overview_table = Engine_Api::_()->getDbtable('serviceoverviews', 'sitebooking');

    // Fetch the user, all services
    $this->view->result = $result = $table->fetchAll($table->select()
      ->where('owner_id = ?', $viewer->user_id));
    $this->view->count = count($result);

    // check duplicacy of slug
    if($this->_getParam('isAjax')){
      $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
      $slug = $this->_getParam('slug');
      $services = $serviceTable->fetchRow($serviceTable->select()->where("slug LIKE '$slug'"));

      $flag = true;
      if(!empty($services)){
        $flag = false;
      }
      $data['flag'] = $flag;
      return $this->_helper->json($data); 
    }
    
    // If not post or form not valid, return
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
        // Create Service
        $formValues = $form->getValues();

        // $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
        // $services = $serviceTable->fetchRow($serviceTable->select()->where('slug LIKE ?',$formValues['slug']));

        //   if(!empty($services)){
        //     return $form->addError('URL: This URL is already taken, Please Try another');
        //   }

        if($formValues['category_id'] == "-1")
          return $form->addError('Please select category.');
        $formValues['slug'] = $formValues['title']; 
        
        print_r($formValues);die;
        $row = $table->createRow();
        $row->setFromArray($formValues);
        
        //FOR ACTIVITY FEED
        $row->parent_id =  $this->_getParam('pro_id');
        $row->owner_id = $viewer->getIdentity();
        $row->parent_type = $provider->getType();
        $row->approved = $approved;
        
        if($this->_getParam('parent_parent_type')) {
           $row->parent_parent_type = $this->_getParam('parent_parent_type'); 
           $row->parent_parent_id = $this->_getParam('parent_parent_id'); 
        }
         
        if($formValues['first_level_category_id'] == NULL)
          $row['first_level_category_id'] = 0;
        
        if($formValues['second_level_category_id'] == NULL)
          $row['second_level_category_id'] = 0;


        if( !empty($formValues['photo']) ) {
           $row->setPhoto($form->photo);
        }

        $row->save();

        //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
        $customfieldform = $form->getSubForm('fields');
        $customfieldform->setItem($row);
        $customfieldform->saveValues();
        
        $categoryId = $row->category_id;   
        $row->profile_type = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryId, 'profile_type');

        $row->save();

        //SAVING DATA IN ServiceOverview table
        $service_overview_row = $service_overview_table->createRow();
        $service_overview_row->longDescription = $formValues['longDescription'];
        $service_overview_row->ser_id = $row->getIdentity();
        $service_overview_row->save();  

        // Auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner_network', 'registered', 'everyone');

        $viewMax = array_search($_POST['view'], $roles);

        foreach( $roles as $i => $role ) {
          $auth->setAllowed($row, $role, 'view', ($i <= $viewMax));
        }

        $roles = array('owner_network', 'registered', 'everyone');

        $viewMax = array_search($_POST['comment'], $roles);

        foreach( $roles as $i => $role ) {
          $auth->setAllowed($row, $role, 'comment', ($i <= $viewMax));
        }

        // // Add tags
        // $tags = preg_split('/[,]+/', $formValues['tags']);
        // $row->tags()->addTagMaps($viewer, $tags);
        
        $formValues['status'] = 1;
        // Add activity only if Service is published
        if( $formValues['status'] == 1 ) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $row, 'service_new');

          // make sure action exists before attaching the Service to the activity
          if( $action ) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $row);
          }
        } 
        if($approved == 1 && $formValues['status'] == 1){
          // Send mail and notifications to provider
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($viewer, $viewer, $row, 'sitebooking_service_approved');
          Engine_Api::_()->sitebooking()->sendServiceAutoapproveMail($viewer,$row);

        }
        
        // Commit
        $db->commit();

    }   catch( Exception $e ) {

        $db->rollBack();
        return $form->addError('Service creation Failed, Please Try Again');
      }   
              
      //bookings/providers/available/51/parent_parent_type/classroom/parent_parent_id/51
    return $this->_helper->redirector->gotoRoute(array('action' => 'available','pro_id' => $this->_getParam('pro_id'), 'parent_parent_type' =>  $this->_getParam('parent_parent_type'), 'parent_parent_id' =>$this->_getParam('parent_parent_id')), 'sitebooking_provider_specific', true);
   // return $this->_helper->redirector->gotoRoute(array('action' => 'sitebooking_provider_specific', 'parent_parent_type' =>  $this->_getParam('parent_parent_type'), 'parent_parent_id' => ));
    
  }

  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'edit')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
        Engine_Api::_()->core()->setSubject($provider);
    }
    
    $this->_helper->content->setEnabled();
    $this->view->ser_id = $ser_id = $this->_getParam('ser_id');

    $this->view->ser_id = $this->_getParam('ser_id');

    $this->view->service = $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'edit')->isValid() ) return;

    // Prepare form
    $this->view->form = $form = new Sitebooking_Form_Service_Edit(array('item' => $service));
    

    $service_overview_table = Engine_Api::_()->getDbtable('serviceoverviews', 'sitebooking');

    $service_overview_row = $service_overview_table->fetchRow($service_overview_table->select()->where('ser_id = ?', $this->_getParam('ser_id')));

    //Run if overview table dont have longDescription data for specified service
    if($service_overview_row == NULL)
      //return $form->addError('Something Went Wrong, Please Try Again');

    // Populate form
    $form->populate($service->toArray());
   // $form->populate($service_overview_row->toArray());
    // $tagStr = '';
    // foreach( $service->tags()->getTagMaps() as $tagMap ) {

    //   $tag = $tagMap->getTag();
    //   if( !isset($tag->text) ) continue;
    //   if( '' !== $tagStr ) $tagStr .= ', ';
    //   $tagStr .= $tag->text;
    // }

    // $form->populate(array(
    //   'tags' => $tagStr,
    // ));

    if($this->_getParam('isAjax')){
      $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
      $slug = $this->_getParam('slug');
      $services = $serviceTable->fetchRow($serviceTable->select()->where("slug LIKE '$slug'"));
      $flag = true;
      if(!empty($services) && $services->ser_id != $this->_getParam('ser_id')){
        $flag = false;

      }
      $data['flag'] = $flag;
      return $this->_helper->json($data); 
    }

    // Check post/form
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $values = $form->getValues();

      $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
    //     $services = $serviceTable->fetchAll($serviceTable->select()->where('slug LIKE ?',$values['slug']));
    //     foreach($services as $key => $value){
    //       if($value->ser_id != $ser_id){
    //       //return $form->addError('URL: This URL is already taken, Please Try another');
    //     }

    //   }
     
      $service->setFromArray($values);

     // $service_overview_row->toArray()['longDescription'] = $values['longDescription'];

      $service->modified_date = date('Y-m-d H:i:s');

      // Add photo
      if( !empty($values['photo']) ) {
        $service->setPhoto($form->photo);
      }

      if($values['category_id'] == "-1")
        return $form->addError('Please select category.');

      

      // Add activity only if Service is published
       
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $service, 'service_new');

      // make sure action exists before attaching the Service to the activity
      if( $action ) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $service);
      }

      $service->save();

      //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
      $customfieldform = $form->getSubForm('fields');

      $customfieldform->setItem($service);
      $customfieldform->saveValues();
      
      $categoryId = 0;
      if(!empty($service->category_id))
        $categoryId = $service->category_id;

      $service->profile_type = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryId, 'profile_type');

      $service->save();

      //Updating serviceoverview table
      //$service_overview_row['longDescription'] = $values['longDescription'];
      //$service_overview_row->save();

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner_network', 'registered', 'everyone');

      $viewMax = array_search($_POST['view'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($service, $role, 'view', ($i <= $viewMax));
      }

      $commentMax = array_search($_POST['comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($service, $role, 'comment', ($i <= $commentMax));
      }

    //   //handle tags
    //   $tags = preg_split('/[,]+/', $values['tags']);
    //   $service->tags()->setTagMaps($viewer, $tags);

      $db->commit();

    }
    catch( Exception $e ) {  
      $db->rollBack();
      print_r($e->getMessage());die;
      return $form->addError('Service Edition Failed, Please Try Again');
    }

    $form->addNotice('Your changes have been saved.');
  }

  public function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'delete')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'edit')->isValid() ) return;

    $pro_id = $this->_getParam('pro_id');

    $this->view->form = $form = new Sitebooking_Form_Service_Delete();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $db = $service->getTable()->getAdapter();

    $db->beginTransaction();

    try {
      $service->delete();

      $db->commit();

    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your service has been deleted successfully.');
    return $this->_forward('success' ,'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'service-manage','pro_id' => $pro_id), 'sitebooking_service_general', true),
      'messages' => array($this->view->message),
      'format' => 'smoothbox'
    ));
  }

  public function serviceManageAction()
  {

    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid()) return;


    // $values['user_id'] = $viewer_id = $viewer->getIdentity();

    $this->view->pro_id = $values['pro_id'] = $this->_getParam('pro_id');

    $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));

    $provider = Engine_Api::_()->getItem('sitebooking_pro',$values['pro_id']);

    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

 
    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
        Engine_Api::_()->core()->setSubject($provider);
    }

    $this->view->timezone = $provider->timezone;

    $this->_helper->content->setEnabled();
    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');

    $values['enabled'] = 1;

    $sql = $serviceTable->getServicesSelect($values);

    // Get paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    $items_per_page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',10);
    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($items_per_page);
  }

  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->content->setEnabled();

    $this->view->ser_id = $this->_getParam('ser_id');

    $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));

    if($service->slug != $this->_getParam('slug')){
      return $this->_forward('notfound', 'error', 'core');
    }

    if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'view')->isValid() ) 
      return;

    if( $service ) {
      Engine_Api::_()->core()->setSubject($service);
    } 
  }

  public function rateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $ser_id =  $this->_getParam('ser_id');

    $table = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $table->setRating($ser_id, $user_id, $rating);

      $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);

      $service->rating = $table->getRating($service->getIdentity());
      $total = $table->ratingCount($service->getIdentity());

      $service->rating_count = $total;

      $service->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
      'total' => $total,
      'rating' => $rating,
    );
    return $this->_helper->json($data);
  }

  function tellAFriendAction()
  {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'view')->isValid()) return;
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET SERVICE ID AND SERVICE OBJECT
    $ser_id = $this->_getParam('ser_id');
    $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
    if (empty($service))
       return $this->_forward('notfound', 'error', 'core');

    //FORM GENERATION
     
    $this->view->form = $form = new Sitebooking_Form_Service_TellAFriend(); 
   
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Send');
      $this->view->form->setAttrib('id', 'form_service_tellAFriend');
      Zend_Registry::set('setFixedCreationFormId', '#form_service_tellAFriend');
      $this->view->form->removeElement('service_send');
      $this->view->form->removeElement('service_cancel');
      $this->view->form->removeDisplayGroup('service_buttons');
      $form->setTitle('');
    } 
    
    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }
    
    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
      Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Tell a friend'));
      Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Send'));
      $this->view->form->setAttrib('id', 'tellAFriendFrom');
      Zend_Registry::set('setFixedCreationFormId', '#tellAFriendFrom');
      $this->view->form->removeElement('service_send');
      $this->view->form->removeElement('service_cancel');
      $form->setTitle('');
    }
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['service_reciver_emails']);

    if (!empty($values['service_send_me'])) {
      $reciver_ids[] = $values['service_sender_email'];

    }
      $sender_email = $values['service_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

    if (!$validator->isValid($sender_email)) {
      return $form->addError('Invalid sender email address.');
    }
    foreach ($reciver_ids as $reciver_id) {
      $reciver_id = trim($reciver_id, ' ');
      if (!$validator->isValid($reciver_id)) {
        return $form->addError('Please enter correct email address of the receiver(s).');
      }
    }

      $sender = $values['service_sender_name'];
      $message = $values['service_message'];
      $heading = ucfirst($service->getTitle());

    foreach ($reciver_ids as $reciver_id) {

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_id, 'SERVICE_TELLAFRIEND_EMAIL', array(
        'host' => $_SERVER['HTTP_HOST'],
        'sender_name' => $sender,
        'service_title' => $heading,
        'message' => '<div>' . $message . '</div>',
        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . $service->getHref(),
        'sender_email' => $sender_email,
        'queue' => true
      ));
    }
    if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
      $this->_forward('success', 'utility', 'core', array(          
        'parentRedirect' => $service->getHref(),          
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
    else
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => false,
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')),
        'format' => 'smoothbox'
      ));
    }
  }

  function homeAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->_helper->content->setEnabled();

    // Make form
    // Note: this code is duplicated in the sitebooking.search-form widget
    $this->view->form = $form = new Sitebooking_Form_Service_Search();

    if( !$viewer->getIdentity() ) {
      $form->removeElement('show');
    }

    // Process form
    $defaultValues = $form->getValues();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = $defaultValues;
    }

    $this->view->search1 = 0;
    foreach ($values as $key => $value) {
      if(!empty($value)){
      $this->view->search1 = 1;
      }
    }

    $values['status'] = "1";
    $values['approved'] = "1";


    if(!empty($values['detectlocation']) ){
      $temp = json_decode($values['detectlocation'], true);

      $values['latitude'] = $temp['latitude'];
      $values['longitude'] = $temp['longitude'];
      unset($values['detectlocation']);
    }
    $this->view->assign($values);

    $customFieldValues = array_intersect_key($values, $form->getFieldElements());

    $values['status'] = "1";

    if(!empty($this->_getParam('first_level_category_id')))     
      $values['first_level_category_id'] = $this->_getParam('first_level_category_id');

    if(!empty($this->_getParam('second_level_category_id')))      
      $values['second_level_category_id'] = $this->_getParam('second_level_category_id');

    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values, $customFieldValues);

    $this->view->formValues = array_filter($values);
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $this->view->search1 = 1;
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    $items_per_page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',10);

    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($items_per_page); 
  }

  public function serviceWishlistAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_main', array(), 'sitebooking_main_wishlist');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_main_wishlist', array(), 'sitebooking_main_service_wishlist');

    if( !$this->_helper->requireUser()->isValid() ) return;                

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();        

    $favouriteObject = Engine_Api::_()->getDbtable('favourites', 'seaocore');
    $favouriteTableName = $favouriteObject->info('name');

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $providerTableName = $providerTable->info('name');


    $select = $serviceTable->select();

    $select
      ->setIntegrityCheck(false)
      ->from($serviceTableName)
      ->join($providerTableName, "$serviceTableName.parent_id = $providerTableName.pro_id", array('title as provider_title', 'photo_id as provider_photo_id', 'slug as provider_slug','telephone_no'))
      ->join($favouriteTableName, "$serviceTableName.ser_id = $favouriteTableName.resource_id", array('*','creation_date as favouriteCreationDate'));

    $sql = $select->where("engine4_seaocore_favourites" . ".resource_type = 'sitebooking_ser'")->where("engine4_seaocore_favourites" . ".poster_id = " . "$viewer_id" )->where("engine4_seaocore_favourites" . ".poster_type = 'user'")->where($serviceTableName . '.approved = 1');

    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    $items_per_page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',10);

    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($items_per_page); 
  }

  public function disableAction()
    {
      if( !$this->_helper->requireUser()->isValid() ) return;
      $viewer = Engine_Api::_()->user()->getViewer();

      $id = $this->_getParam('id');

      if (!empty($id)) {
        $serviceItem = Engine_Api::_()->getItem('sitebooking_ser', $id);
      }

      if(empty($id)) {
        return $form->addError('Something went wrong, Please Try Again');
      }

      $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
      $bookingTableName = $bookingTable->info('name');

      $this->view->form = $form = new Sitebooking_Form_Service_Disable();

      if( !$this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }
      
    $pro_id = $this->_getParam('pro_id');

      $db = $serviceItem->getTable()->getAdapter();
      $db->beginTransaction();

      try {

          //enabe/disable button work 
        if(!empty($this->_getParam('id'))) {

            $serviceItem = Engine_Api::_()->getItem('sitebooking_ser', $id);

            $serviceItem->enabled = 0;
            $serviceItem->save();

            //rejecting this service
            $select = $bookingTable->select();
            $sql = $select->where($bookingTableName . ".ser_id = ?", $id)
                   ->where($bookingTableName . ".status = 'booked' OR $bookingTableName.status = 'pending'");

            $bookingData = $bookingTable->fetchAll($sql);

            foreach( $bookingData as $item ) {
              $item->status = "rejected";
              $item->save();
            }

        }
        
          $db->commit();
      } catch( Exception $e ) {

        $db->rollBack();
        return $form->addError('Failed! This Service is not disabled, Please Try Again');
      }

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This service has been successfully disabled.');
      return $this->_forward('success' ,'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'service-manage','pro_id' => $pro_id), 'sitebooking_service_general', true),
        'messages' => Array($this->view->message)
      ));
    }


  public function enableAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $id = $this->_getParam('id');
    $pro_id = $this->_getParam('pro_id');

    if (!empty($id)) {
      $serviceItem = Engine_Api::_()->getItem('sitebooking_ser', $id);
    }

    if(empty($id)) {
      return $form->addError('Something went wrong, Please Try Again');
    }

    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');

    $this->view->form = $form = new Sitebooking_Form_Service_Enable();

    if( !$this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    

    $db = $serviceItem->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      //enabe/disable button work 
      if(!empty($this->_getParam('id'))) {

        $serviceItem->enabled = 1;
        $serviceItem->save();

      } 
      
      $db->commit();
    } catch( Exception $e ) {

      $db->rollBack();
      return $form->addError('Failed! Service provider has not been disabled, please try again');
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('This service has been successfully enabled.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'service-manage','pro_id' => $pro_id), 'sitebooking_service_general', true),
      'messages' => Array($this->view->message)
    ));
  }

}

?>