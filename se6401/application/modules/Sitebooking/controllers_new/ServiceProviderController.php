<?php
class Sitebooking_ServiceProviderController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
     
    $this->_helper->content
        //->setNoRender()
        ->setEnabled();
  }
  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'create')->isValid()) return;

    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['owner_id'] = $viewer->getIdentity();

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

        //CHECKING SERVICE CREATION QUOTA

    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitebooking_pro', 'max');

    $table = Engine_Api::_()->getItemTable('sitebooking_pro');

    // Fetch the user, all service providers
    $this->view->result = $result = $table->fetchAll($table->select()
        ->where('owner_id = ?', $viewer->user_id));
    $this->view->count = count($result);
    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Create();

    // checking auto approving permission
    $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'approve');
    if($autoApprove == 1){
      $values['approved'] = 1;
    }else{
      $values['approved'] = 0;
    }

    // check duplicacy of slug
    if($this->_getParam('isAjax')){
      $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
      $slug = $this->_getParam('slug');
      $providers = $providerTable->fetchRow($providerTable->select()->where("slug LIKE '$slug'"));

      $flag = true;
        if(!empty($providers)){
          $flag = false;
        }
      $data['flag'] = $flag;
      return $this->_helper->json($data); 
    }

    if( !$this->getRequest()->isPost() ) {
        return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
    }

    // Process
    $provider = Engine_Api::_()->getItemTable('sitebooking_pro');
    $location = Engine_Api::_()->getItemTable('sitebooking_providerlocation');
    $db = $provider->getAdapter();
    $db->beginTransaction();

    try {
      $formValues = $form->getValues();

      $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
      $providers = $providerTable->fetchRow($providerTable->select()->where('slug LIKE ?',$formValues['slug']));

        if(!empty($providers)){
          return $form->addError('URL: This URL is already taken, please try another');
        }

      $values = array_merge($formValues,$values);
      $provider = $provider->createRow();
      $provider->setFromArray($values);
      $provider->save();


      if( !empty($formValues['photo']) ) {
        $provider->setPhoto($form->photo);
      }

      if( !empty($formValues['coverPhoto']) ) {
        $provider->setCoverPhoto($form->coverPhoto);
      }

      $tags = preg_split('/[,]+/', $values['tags']);
      $provider->tags()->addTagMaps($viewer, $tags);

      //location
      $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

      if($locationFieldcoreSettings === "yes") {
        $location = $location->createRow();
        $formValues = $form->getValues();
        $locationValues = $formValues['location_region'];
        $locationValues = json_decode($locationValues, true);
        $locationValues['pro_id'] = $provider->getIdentity();
        $location->setFromArray($locationValues);
        $location->save();
      }

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner_network', 'registered', 'everyone');

      $viewMax = array_search($_POST['view'], $roles);

      foreach( $roles as $i => $role ) {
          $auth->setAllowed($provider, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner_network', 'registered', 'everyone');

      $viewMax = array_search($_POST['comment'], $roles);

      foreach( $roles as $i => $role ) {
          $auth->setAllowed($provider, $role, 'comment', ($i <= $viewMax));
      }

      if( $formValues['status'] == 1) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $provider, 'provider_new');
        // make sure action exists before attaching the provider to the activity
        if( $action ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $provider);
        }
      }

      if($values['approved'] == 1 && $values['status'] == 1){
        // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($viewer, $viewer, $provider, 'sitebooking_provider_approved');
        Engine_Api::_()->sitebooking()->sendProviderAutoapproveMail($viewer,$provider);

      }
      $db->commit();
    }
    catch (Execption $e) {
      $db->rollBack();
      return $form->addError('Service Provider creation Failed, Please Try Again');
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid()) return;

    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->provider = $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }



    $location = Engine_Api::_()->getDbtable('providerlocations','sitebooking');
    $location = $location->fetchRow('pro_id = '.$this->_getParam('pro_id'));


    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    $this->view->pro_id = $this->_getParam('pro_id');
    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Edit();
    $form->populate($provider->toArray());
    $tagStr = '';
    foreach( $provider->tags()->getTagMaps() as $tagMap ) {
      $tag = $tagMap->getTag();
      if( !isset($tag->text) ) continue;
      if( '' !== $tagStr ) $tagStr .= ', ';
      $tagStr .= $tag->text;
    }
    $form->populate(array(
      'tags' => $tagStr,
    ));

    // hide status change if it has been already published
    if( $provider->status == "1" ) {
      $form->removeElement('status');
    }

    $preStatus = $provider->status;

    // check duplicacy of slug

    if($this->_getParam('isAjax')){
      $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
      $slug = $this->_getParam('slug');
      $providers = $providerTable->fetchRow($providerTable->select()->where("slug LIKE '$slug'"));

      $flag = true;
        if(!empty($providers) && $providers->pro_id != $this->_getParam('pro_id')){
          $flag = false;

        }
      $data['flag'] = $flag;
      return $this->_helper->json($data); 
    }

    if( !$this->getRequest()->isPost() ) {
        return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
      $providers = $providerTable->fetchAll($providerTable->select()->where('slug LIKE ?',$values['slug']));
      foreach($providers as $key => $value){
        if($value->pro_id != $pro_id){
          return $form->addError('URL: This URL is already taken, please try another');
        }

      }

      $provider->setFromArray($values);
      $provider->save();
      if( !empty($values['photo']) ) {
        $provider->setPhoto($form->photo);
      }

      if( !empty($values['coverPhoto']) ) {
        $provider->setCoverPhoto($form->coverPhoto);
      }
      // handle tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $provider->tags()->setTagMaps($viewer, $tags);

      
      if($values['location_region'] ){
        $locationValues = $values['location_region'];
        $locationValues = json_decode($locationValues, true);
        $location->setFromArray($locationValues);
        $location->save();
      }

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner_network', 'registered', 'everyone');

      $viewMax = array_search($_POST['view'], $roles);

      foreach( $roles as $i => $role ) {
          $auth->setAllowed($provider, $role, 'view', ($i <= $viewMax));
      }

      $commentMax = array_search($_POST['comment'], $roles);

      foreach( $roles as $i => $role ) {
          $auth->setAllowed($provider, $role, 'comment', ($i <= $commentMax));
      }

      if( $provider->status == 1 && $preStatus == 0) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $provider, 'provider_new');
        // make sure action exists before attaching the provider to the activity
        if( $action ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $provider);
        }
      }

      // send notification and mail if status changed to published and auto-approving is on
      $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'approve');
      if($autoApprove == 1 && $provider->status == 1 && $preStatus == 0){
        // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($viewer, $viewer, $provider, 'sitebooking_provider_approved');
        Engine_Api::_()->sitebooking()->sendProviderAutoapproveMail($viewer,$provider);

      }

      $db->commit();
    }
    catch (Execption $e) {
      $db->rollBack();
      return $form->addError('Your changes have not been saved., please try again');
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Search();
    $form->removeElement('location');
    $form->removeElement('locationDistance');
    $form->removeElement('city');
    $form->removeElement('country');

    $defaultValues = $form->getValues();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = $defaultValues;
    }
    $this->view->formValues = array_filter($values);

    $values['user_id'] = $viewer->getIdentity();
    $values['enabled'] = 1;

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitebooking_pro')->getProvidersPaginator($values);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    $items_per_page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page');
    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($items_per_page);
  }


  public function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'delete')->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->getRequest()->getParam('pro_id'));
    
    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    
    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Delete();

    if( !$this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $provider->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      $provider->delete();
      
      $db->commit();
    } catch( Exception $e ) {

      $db->rollBack();
      return $form->addError('Service provider has not been deleted, please try again');
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('This provider has been deleted successfully.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitebooking_provider_general', true),
      'messages' => Array($this->view->message)
    ));
  }


  

  public function overviewAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid()) return;


     $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Overview();
    // Process
    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');

    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);

    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }

    $this->_helper->content
        //->setNoRender()
        ->setEnabled();



    $values['pro_id'] = $pro_id;
    $values['owner_id'] = $viewer->getIdentity();

    $table = Engine_Api::_()->getItemTable('sitebooking_providersoverview');
    $overview = $table->fetchRow('pro_id = '.$pro_id);

    

    if($overview){
      $form->populate($overview->toArray());
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
    }
    $formValues = $form->getValues();

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      if(!$overview)
      {
        $values = array_merge($formValues,$values);
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $db->commit();
        return;
      }
      $values = array_merge($formValues,$values);
      $overview->setFromArray($values);
      $overview->save();
      $db->commit();
    }
    catch (Execption $e) {
      $db->rollBack();
      return $form->addError('Failed, please try again');
    }

    $form->addNotice('Your changes have been saved.');
  }
 
 function homeAction()
 {
    $this->_helper->content
          //->setNoRender()
          ->setEnabled()
          ;
 }

 function viewAction()
 {

  $viewer = Engine_Api::_()->user()->getViewer();
  $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

  $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));
  $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');

  if($provider->slug != $this->_getParam('slug')){
    return $this->_forward('notfound', 'error', 'core');
  }

  // Check permission
  if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'view')->isValid() ) 
    return;

  if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
    Engine_Api::_()->core()->setSubject($provider);
  }

  if( !$provider->isOwner($viewer) ) {
    $providerTable->update(array(
      'view_count' => new Zend_Db_Expr('view_count + 1'),
    ), array(
      'pro_id = ?' => $provider->getIdentity(),
    ));
  }
  
 }


function tellAFriendAction()
{

  $this->_helper->layout->setLayout('default-simple');

  if( !$this->_helper->requireUser()->isValid() ) return;
  if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'view')->isValid()) return;
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET PROVIDER ID AND PROVIDER OBJECT
    $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
    if (empty($provider))
      return $this->_forward('notfound', 'error', 'core');

    //FORM GENERATION
   
    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_TellAFriend(); 
 
     if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Send');
      $this->view->form->setAttrib('id', 'form_provider_tellAFriend');
      Zend_Registry::set('setFixedCreationFormId', '#form_provider_tellAFriend');
      $this->view->form->removeElement('provider_send');
      $this->view->form->removeElement('provider_cancel');
      $this->view->form->removeDisplayGroup('provider_buttons');
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
      $this->view->form->removeElement('provider_send');
      $this->view->form->removeElement('provider_cancel');
      $form->setTitle('');
    }
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['provider_reciver_emails']);

      if (!empty($values['provider_send_me'])) {
        $reciver_ids[] = $values['provider_sender_email'];

      }
      $sender_email = $values['provider_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }

      $sender = $values['provider_sender_name'];
      $message = $values['provider_message'];
      $heading = ucfirst($provider->getTitle());

      foreach ($reciver_ids as $reciver_id) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_id, 'PROVIDER_TELLAFRIEND_EMAIL', array(
            'host' => $_SERVER['HTTP_HOST'],
            'sender_name' => $sender,
            'provider_title' => $heading,
            'message' => '<div>' . $message . '</div>',
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . $provider->getHref(),
            'sender_email' => $sender_email,
            'queue' => true
        ));
      }
      if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
        $this->_forward('success', 'utility', 'core', array(          
          'parentRedirect' => $provider->getHref(),          
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
  

  public function rateAction()

  {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $rating = $this->_getParam('rating');
        $pro_id =  $this->_getParam('pro_id');

        $table = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $table->setRating($pro_id, $user_id, $rating);

            $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);

            $provider->rating = $table->getRating($provider->getIdentity());
            $total = $table->ratingCount($provider->getIdentity());

            $provider->rating_count = $total;

            $provider->save();

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

  function availableAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    
    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);
    $this->view->providerTimeZone = $timezone = $provider->timezone; 
    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }

    $this->view->starttime = '';
      $this->view->endtime = '';

    $this->_helper->content
        //->setNoRender()
        ->setEnabled();
    $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
    $this->view->serviceRows = $serviceRows = $serviceTable->fetchAll($serviceTable->select()->where("parent_id = $pro_id"))->toArray();
    
    if(empty($serviceRows[0]['ser_id'])){
      return;
    }

    $this->view->serviceTitle = $serviceRows[0]['title'];
    $this->view->servicePhotoId = $serviceRows[0]['photo_id'];

    $values['pro_id'] =  $pro_id;
    $values['owner_id'] = $viewer->getIdentity();

    $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
    $this->view->isAjax = $this->_getParam('isAjax', null);
    if($this->_getParam('isAjax')){
      $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$this->_getParam('ser_id')));
      $this->view->service = $service = Engine_Api::_()->getItem('sitebooking_ser',$this->_getParam('ser_id'));
      $this->view->serviceTitle  = $service->title;
      $this->view->ser_id = $service->getIdentity();
      $this->view->servicePhotoId = $service->photo_id;
    }else{
      $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$serviceRows[0]['ser_id']));
      $this->view->ser_id = $serviceRows[0]['ser_id'];
    }


    if(!empty( $_POST['save'] )){
        
      $this->view->ser_id = $values['ser_id'] = $_POST['ser_id'] ? $_POST['ser_id'] : $this->view->ser_id;
      $this->view->starttime = $_POST['starttime'];
      $this->view->endtime = $_POST['endtime'];
      
  
      $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$values['ser_id']));
      $formvalues = array();
      $this->view->service = $service = Engine_Api::_()->getItem('sitebooking_ser',$values['ser_id']);
      $this->view->serviceTitle = $service->title;
      $this->view->servicePhotoId = $service->photo_id;

      $avail = array();
      $c = 1;

      unset($_POST['ser_id']);
      unset($_POST['starttime']);
      unset($_POST['endtime']);
      unset($_POST['save']);
      if(!empty($_POST['mon_offday']))
        unset($_POST['mon_offday']);
      if(!empty($_POST['tue_offday']))
        unset($_POST['tue_offday']);
      if(!empty($_POST['wed_offday']))
        unset($_POST['wed_offday']);
      if(!empty($_POST['thu_offday']))
        unset($_POST['thu_offday']);
      if(!empty($_POST['fri_offday']))
        unset($_POST['fri_offday']);
      if(!empty($_POST['sat_offday']))
        unset($_POST['sat_offday']);
      if(!empty($_POST['sun_offday']))
        unset($_POST['sun_offday']);

      foreach ($_POST as $key => $value) {
        $date1 = date_create(null, timezone_open($timezone));
        date_time_set($date1, (int)explode(":",$value)[0], (int)explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open('UTC'));
        $d2 = date_format($date2, 'Y-m-d');
        $utcTimeSlot = date_format($date2, 'H:i');

        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);

        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $avail[$day.'_'.$value] = $utcTimeSlot;
        }

      }

      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = 0;
      $mon = $tue = $wed = $thu = $fri = $sat = $sun = array();
      foreach ($avail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $mon['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tue['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wed['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thu['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $fri['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $sat['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sun['sun_'.$c7] = $value; 
        }
      }      

      $values['monday'] = json_encode($mon);
      $values['tuesday'] = json_encode($tue);
      $values['wednesday'] = json_encode($wed);
      $values['thursday'] = json_encode($thu);
      $values['friday'] = json_encode($fri);
      $values['saturday'] = json_encode($sat);
      $values['sunday'] = json_encode($sun);

      if(!$scheduleRow){
        $scheduleRow = $scheduleTable->createRow();
        $scheduleRow->setFromArray($values);
        $scheduleRow->save();

      }
      else{
        $scheduleRow->setFromArray($values);
        $scheduleRow->save();
      }

    }

    //populate time duration
    $this->view->monday = array();
    $this->view->tuesday = array();
    $this->view->wednesday = array();
    $this->view->thursday = array();
    $this->view->friday = array();
    $this->view->saturday = array();
    $this->view->sunday = array(); 
    if($scheduleRow){
      $monday = json_decode($scheduleRow->monday, true);
      $tuesday = json_decode($scheduleRow->tuesday, true);
      $wednesday = json_decode($scheduleRow->wednesday, true);
      $thursday = json_decode($scheduleRow->thursday, true);
      $friday = json_decode($scheduleRow->friday, true);
      $saturday = json_decode($scheduleRow->saturday, true);
      $sunday = json_decode($scheduleRow->sunday, true);

      $data['demo'] = 'demo';
      if(!empty($monday))
        $data = array_merge($data,$monday);
      if(!empty($tuesday))
        $data = array_merge($data,$tuesday);
      if(!empty($wednesday))
        $data = array_merge($data,$wednesday);
      if(!empty($thursday))
        $data = array_merge($data,$thursday);
      if(!empty($friday))
        $data = array_merge($data,$friday);
      if(!empty($saturday))
        $data = array_merge($data,$saturday);
      if(!empty($sunday))
        $data = array_merge($data,$sunday);

      unset($data['demo']);
      $popAvail = Array();

      foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int)explode(":",$value)[0], (int)explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($timezone));
        $d2 = date_format($date2, 'Y-m-d');

        $utcTimeSlot = date_format($date2, 'H:i');

        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);
        
        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){ 
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }

      }

      $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = array();
      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = 0;
      foreach ($popAvail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $monday['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tuesday['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wednesday['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thursday['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $friday['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $saturday['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sunday['sun_'.$c7] = $value; 
        }
      }

      $this->view->monday = $monday;
      $this->view->tuesday = $tuesday;
      $this->view->wednesday = $wednesday;
      $this->view->thursday = $thursday;
      $this->view->friday = $friday;
      $this->view->saturday = $saturday;
      $this->view->sunday = $sunday;
    }
    
    
    // //echo $serviceRows[1]['ser_id'];die;
     if($serviceRows[0]['ser_id'] == $this->view->ser_id) {
         $scheduleRow1 = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$serviceRows[1]['ser_id']));
     } else {
         $scheduleRow1 = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$serviceRows[0]['ser_id']));
     }
     
    
    //     //populate time duration
    // $this->view->monday1 = array();
    // $this->view->tuesday1 = array();
    // $this->view->wednesda1 = array();
    // $this->view->thursday1 = array();
    // $this->view->friday1 = array();
    // $this->view->saturday1 = array();
    // $this->view->sunday1 = array(); 
    // if($scheduleRow1){
    //   $monday1 = json_decode($scheduleRow1->monday, true);
    //   $tuesday1 = json_decode($scheduleRow1->tuesday, true);
    //   $wednesday1 = json_decode($scheduleRow1->wednesday, true);
    //   $thursday1 = json_decode($scheduleRow1->thursday, true);
    //   $friday1 = json_decode($scheduleRow1->friday, true);
    //   $saturday1 = json_decode($scheduleRow1->saturday, true);
    //   $sunday1 = json_decode($scheduleRow1->sunday, true);

    //   $data1['demo'] = 'demo';
    //   if(!empty($monday1))
    //     $data1 = array_merge($data1,$monday);
    //   if(!empty($tuesday1))
    //     $data1 = array_merge($data1,$tuesday);
    //   if(!empty($wednesday1))
    //     $data1 = array_merge($data1,$wednesday);
    //   if(!empty($thursday1))
    //     $data1 = array_merge($data1,$thursday);
    //   if(!empty($friday1))
    //     $data1 = array_merge($data1,$friday);
    //   if(!empty($saturday1))
    //     $data1 = array_merge($data1,$saturday);
    //   if(!empty($sunday1))
    //     $data1 = array_merge($data1,$sunday);

    //   unset($data1['demo']);
    //   $popAvail1 = Array();

    //   foreach ($data1 as $key1 => $value1) {
    //     $date11 = date_create(null, timezone_open('UTC'));
    //     date_time_set($date11, (int)explode(":",$value1)[0], (int)explode(":",$value1)[1]);
    //     $d11 =  date_format($date11, 'Y-m-d');
    //     $date21 = date_timezone_set($date11, timezone_open($timezone));
    //     $d21 = date_format($date21, 'Y-m-d');

    //     $utcTimeSlot1 = date_format($date21, 'H:i');

    //     $s11 = date_create($d11);
    //     $s21 = date_create($d21);
    //     $diff1=date_diff($s11,$s21);
    //     $dayDiff1 =  $diff1->format("%R%a days");
    //     $x1 = explode("_",$key1);
        
    //     if($x1[0] === 'mon' && $value1 != 'mon'){
    //       $day1 = strtolower(substr(date('l', strtotime('monday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'tue' && $value1 != 'tue'){
    //       $day1 = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'wed' && $value1 != 'wed'){
    //       $day1 = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'thu' && $value1 != 'thu'){
    //       $day1 = strtolower(substr(date('l', strtotime('thursday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'fri' && $value1 != 'fri'){
    //       $day1 = strtolower(substr(date('l', strtotime('friday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'sat' && $value1 != 'sat'){ 
    //       $day1 = strtolower(substr(date('l', strtotime('saturday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }
    //     if($x1[0] === 'sun' && $value1 != 'sun'){
    //       $day1 = strtolower(substr(date('l', strtotime('sunday '.$dayDiff1)),0,3));
    //       $popAvail1[$day1.'_'.$value1] = $utcTimeSlot1;
    //     }

    //   }

    //   $monday1 = $tuesday1 = $wednesday1 = $thursday1 = $friday1 = $saturday1 = $sunday1 = array();
    //   $c11 = $c21 = $c31 = $c41 = $c51 = $c61 = $c71 = 0;
    //   foreach ($popAvail1 as $key => $value){
    //     $x2 = explode("_",$key);
    //     if($x2[0] === 'mon'){
    //       $c11++;
    //       $monday1['mon_'.$c11] = $value; 
    //     }
    //     if($x2[0] === 'tue'){
    //       $c21++;
    //       $tuesday1['tue_'.$c21] = $value; 
    //     }
    //     if($x2[0] === 'wed'){
    //       $c31++;
    //       $wednesday1['wed_'.$c31] = $value; 
    //     }
    //     if($x2[0] === 'thu'){
    //       $c41++;
    //       $thursday1['thu_'.$c41] = $value; 
    //     }
    //     if($x2[0] === 'fri'){
    //       $c51++;
    //       $friday1['fri_'.$c51] = $value; 
    //     }
    //     if($x2[0] === 'sat'){
    //       $c61++;
    //       $saturday1['sat_'.$c61] = $value; 
    //     }
    //     if($x2[0] === 'sun'){
    //       $c71++;
    //       $sunday1['sun_'.$c71] = $value; 
    //     }
    //   }

    //   $this->view->monday1 = $monday1;
    //   $this->view->tuesday1 = $tuesday1;
    //   $this->view->wednesday1 = $wednesday1;
    //   $this->view->thursday1 = $thursday1;
    //   $this->view->friday1 = $friday1;
    //   $this->view->saturday1 = $saturday1;
    //   $this->view->sunday1 = $sunday1;
    // }
    
          //populate time duration
    $this->view->monday1 = array();
    $this->view->tuesday1 = array();
    $this->view->wednesday1 = array();
    $this->view->thursday1 = array();
    $this->view->friday1 = array();
    $this->view->saturday1 = array();
    $this->view->sunday1 = array(); 
    if($scheduleRow1){
      $monday = json_decode($scheduleRow1->monday, true);
      $tuesday = json_decode($scheduleRow1->tuesday, true);
      $wednesday = json_decode($scheduleRow1->wednesday, true);
      $thursday = json_decode($scheduleRow1->thursday, true);
      $friday = json_decode($scheduleRow1->friday, true);
      $saturday = json_decode($scheduleRow1->saturday, true);
      $sunday = json_decode($scheduleRow1->sunday, true);
        unset($data);
      $data['demo'] = 'demo';
      if(!empty($monday))
        $data = array_merge($data,$monday);
      if(!empty($tuesday))
        $data = array_merge($data,$tuesday);
      if(!empty($wednesday))
        $data = array_merge($data,$wednesday);
      if(!empty($thursday))
        $data = array_merge($data,$thursday);
      if(!empty($friday))
        $data = array_merge($data,$friday);
      if(!empty($saturday))
        $data = array_merge($data,$saturday);
      if(!empty($sunday))
        $data = array_merge($data,$sunday);

      unset($data['demo']);
      $popAvail = Array();

      foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int)explode(":",$value)[0], (int)explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($timezone));
        $d2 = date_format($date2, 'Y-m-d');

        $utcTimeSlot = date_format($date2, 'H:i');

        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);
        
        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){ 
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }

      }

      $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = array();
      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = 0;
      foreach ($popAvail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $monday['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tuesday['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wednesday['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thursday['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $friday['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $saturday['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sunday['sun_'.$c7] = $value; 
        }
      }

      $this->view->monday1 = $monday;
      $this->view->tuesday1 = $tuesday;
      $this->view->wednesday1 = $wednesday;
      $this->view->thursday1 = $thursday;
      $this->view->friday1 = $friday;
      $this->view->saturday1 = $saturday;
      $this->view->sunday1 = $sunday;
    }
      
      //$this->view->monday1 = array_merge($this->view->monday1, $this->view->monday);
     
  }
  


  public function bookedAction(){
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);
    $this->view->timezone = $provider->timezone;


    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }
    
    // authentication for booking request tab in my provider tab
    $this->view->message = 0;
    if($provider->owner_id != $viewer->user_id) {
      $this->view->message = 1;
    }

    $this->_helper->content
        //->setNoRender()
        ->setEnabled();



    if($this->_getparam('isAjax')){

      $this->view->bookingId = $bookingId = $this->_getParam('booking_id');
      $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking',$bookingId);
      $action = $this->_getParam('action_type');

      $owner = Engine_Api::_()->getItem('sitebooking_pro', $bookingItem->pro_id);
      $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);
      $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$bookingItem->ser_id);


      if($action === 'reject'){  
        $bookingItem->status = 'rejected';
        $bookingItem->save();
        $status = 'rejected';
        // Send mail and notifications for client
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_reject');

        Engine_Api::_()->sitebooking()->sendServiceRejectMail($user,$serviceItem,$owner);

      }
      elseif($action === 'accept'){  
      $bookingItem->status = 'pending';
      $bookingItem->save();
      $status = 'pending';
      // Send mail and notifications for client
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_accept');
      Engine_Api::_()->sitebooking()->sendServiceAcceptMail($user,$serviceItem,$owner);

      }
      elseif($action === 'complete'){  
      $bookingItem->status = 'completed';
      $bookingItem->save();
      $status = 'completed';
      // Send mail and notifications for client
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_complete');
      Engine_Api::_()->sitebooking()->sendServiceCompleteMail($user,$serviceItem,$owner);
      }
    }


    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_BookingSearch();
    $params['pro_id'] = $pro_id;
    $params['page'] = $this->_getParam('page',1);

    $form->populate($_POST);

    if(!empty($_POST['find']) ){
      $_POST['servicing_date'] = $_POST['servicing_date']['date'];
      $_POST['booking_date'] = $_POST['booking_date']['date'];
      $params = array_merge($_POST,$params);

    }
    $this->view->formValues = array_filter($_POST);
    $this->view->zoomMeetingUrl = $zoomMeetingUrl = $this->getMeetingCurlRequest($params);
    $this->view->bookedItems = $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
  }

  public function statusAction(){
    if( !$this->_helper->requireUser()->isValid() ) return;
      
     $this->view->bookingId = $bookingId = $this->_getParam('booking_id');

      $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking',$bookingId);
      $action = $this->_getParam('action_type');

      $owner = Engine_Api::_()->getItem('sitebooking_pro', $bookingItem->pro_id);
      $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);
      $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$bookingItem->ser_id);


      if($action === 'reject'){  
        $bookingItem->status = 'rejected';
        $bookingItem->save();
        $status = 'rejected';
        // Send mail and notifications for client
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_reject');

        Engine_Api::_()->sitebooking()->sendServiceRejectMail($user,$serviceItem,$owner);

      }
      elseif($action === 'accept'){  
      $bookingItem->status = 'pending';
      $bookingItem->save();
      $status = 'pending';
      // Send mail and notifications for client
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_accept');
      Engine_Api::_()->sitebooking()->sendServiceAcceptMail($user,$serviceItem,$owner);

      }
      elseif($action === 'complete'){  
      $bookingItem->status = 'completed';
      $bookingItem->save();
      $status = 'completed';
      // Send mail and notifications for client
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_complete');
      Engine_Api::_()->sitebooking()->sendServiceCompleteMail($user,$serviceItem,$owner);
      }
  
      return $this->_helper->json($status);
  }

  public function contactAction(){

    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->pro_id = $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
      
    }

    $this->_helper->content
        //->setNoRender()
        ->setEnabled();

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Contact();
    $form->populate($provider->toArray());


    if( !$this->getRequest()->isPost() ) {
        return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
    }

    $db = Engine_Api::_()->getItemTable('sitebooking_pro')->getAdapter();
    $db->beginTransaction();
    try{
      $values = $form->getValues();

      $provider->setFromArray($values);
      $provider->save();
      $db->commit();
    }
    catch (Execption $e) {
      $db->rollBack();
      return $form->addError('Contact updatation Failed, Please Try Again');
    }
    $form->addNotice('Your changes have been saved.');

  }

  function contactUsAction(){
    $providerId = $this->_getparam('pro_id');

    $this->view->providerItem = $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$providerId);
  }

  public function providerWishlistAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitebooking_main', array(), 'sitebooking_main_wishlist');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitebooking_main_wishlist', array(), 'sitebooking_main_provider_wishlist');

    if( !$this->_helper->requireUser()->isValid() ) return;        

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();        

    $favouriteObject = Engine_Api::_()->getDbtable('favourites', 'seaocore');
    $favouriteTableName = $favouriteObject->info('name');

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $providerTableName = $providerTable->info('name');

    $select = $providerTable->select();
    $select->setIntegrityCheck(false)
    ->from($providerTableName)
    ->join($favouriteTableName, "$providerTableName.pro_id = $favouriteTableName.resource_id", array('*','creation_date as favouriteCreationDate'));

    $sql = $select->where("engine4_seaocore_favourites" . ".resource_type = 'sitebooking_pro'")->where("engine4_seaocore_favourites" . ".poster_id = " . "$viewer_id" )->where("engine4_seaocore_favourites" . ".poster_type = 'user'");

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

    $id = $this->_getParam('pro_id');

    if (!empty($id)) {
      $providerItem = Engine_Api::_()->getItem('sitebooking_pro', $id);
    }

    if(empty($id)) {
      return $form->addError('Something went wrong, please try again');
    }

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Disable();

    if( !$this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    
    $db = $providerItem->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //enabe/disable button work 
      if(!empty($this->_getParam('pro_id'))) {

        $providerItem->enabled = 0;
        $providerItem->save();

        // disable provider all services
        $select = $serviceTable->select();
        $sql = $select->where($serviceTableName . ".parent_id = ?", $id);
        $serviceData = $serviceTable->fetchAll($sql);

        foreach( $serviceData as $item ) {
          $item->enabled = 0;
          $item->save();
        }

        //rejecting all booked services
        $select = $bookingTable->select();
        $sql = $select->where($bookingTableName . ".pro_id = ?", $id)
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
      return $form->addError('Failed! Service provider has not been disabled, please try again');
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('This provider has been successfully disabled along with all its services.');
    return $this->_forward('success' ,'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRefresh' => true,
      'messages' => Array($this->view->message),
      'format' => 'smoothbox'
    ));
  }

  public function enableAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $id = $this->_getParam('pro_id');

    if (!empty($id)) {
      $providerItem = Engine_Api::_()->getItem('sitebooking_pro', $id);
    }

    if(empty($id)) {
      return $form->addError('Something went wrong, Please Try Again');
    }

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Enable();

    if( !$this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    

    $db = $providerItem->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      //enabe/disable button work 
      if(!empty($this->_getParam('pro_id'))) {

        $providerItem->enabled = 1;
        $providerItem->save();

        // enable provider all services
        $select = $serviceTable->select();
        $sql = $select->where($serviceTableName . ".parent_id = ?", $id);
        $serviceData = $serviceTable->fetchAll($sql);

        foreach( $serviceData as $item ) {
          $item->enabled = 1;
          $item->save();
        }   
      }
      
      $db->commit();
    } catch( Exception $e ) {

      $db->rollBack();
      return $form->addError('Failed! Service provider has not been disabled, please try again');
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('This provider has been successfully enabled along with all its services.');
    return $this->_forward('success' ,'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRefresh' => true,
      'messages' => Array($this->view->message),
      'format' => 'smoothbox'
    ));
  }
  
  function getMeetingCurlRequest($postFields){
        // Curl request for create zoom url
        //
        
        $ch = curl_init();
        $postUrl = "https://".$_SERVER['HTTP_HOST']."/zoom/get-zoom-meeting-url.php";
        
        curl_setopt($ch, CURLOPT_URL,$postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,
        // "servicebooking_id=value1&postvar2=value2&postvar3=value3");
        
        // In real life you should use something like:
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                 http_build_query($postFields));
        
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        
        
        curl_close ($ch);
        
        $res = json_decode($server_output);
        
        // Further processing ...
           return $res;
  }
  
}
?>