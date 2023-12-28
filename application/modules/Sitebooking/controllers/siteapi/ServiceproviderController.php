<?php
class Sitebooking_ServiceproviderController extends Siteapi_Controller_Action_Standard
{
  public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getSearchForm());
  }

  public function providerWishlistAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) 
      $this->respondWithError("unauthorized");     

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

    $paginator = Zend_Paginator::factory($sql);
    $paginator->setItemCountPerPage($values['limit']);
    $paginator->setCurrentPageNumber($values['page']);
    $response['totalItemCount'] = $paginator->getTotalItemCount();
    
    foreach($paginator as $provider){
      $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider);
      
      $providersArr = $provider->toArray();
      $providersArr['description_provider'] = $providersArr['description'];

      // work to fetch favourite_id of the resource for user.
      $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $provider->getIdentity());
      if(!empty($favourite_id_temp)){
        $providersArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
      }
      else
        $providersArr['favourite_id'] = 0;
      $providersArr = array_merge($providersArr, $getContentImages);

      $providerArr[] = $providersArr;
    }
    if(!empty($providerArr)){
      $response['response'] = $providerArr;
    }
    else
      $response['response'] = "";

    $this->respondWithSuccess($response);
  }

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check permission
    if(!$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'view')->isValid()) 
      $this->respondWithError("unauthorized");

    $values = $this->getRequestAllParams;
    if (!isset($values['limit']))
        $values['limit'] = (int) $this->getRequestParam('limit', 20);

    // Set default page
    if (!isset($values['page']))
        $values['page'] = (int) $this->getRequestParam('page', 1);
    $values['status'] = 1;
    $values['approved'] = 1;
    $response = array();
    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('sitebooking_pro')->getProvidersPaginator($values);
    $paginator->setItemCountPerPage($values['limit']);
    $paginator->setCurrentPageNumber($values['page']);
    $response['totalItemCount'] = $paginator->getTotalItemCount();
    $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'create')->isValid();
    foreach($paginator as $provider){
      $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider);
      
      $providersArr = $provider->toArray();
      $providersArr['description_provider'] = $providersArr['description'];

      // work to fetch favourite_id of the resource for user.
      $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $provider->getIdentity());
      if(!empty($favourite_id_temp)){
        $providersArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
      }
      else
        $providersArr['favourite_id'] = 0;
      $providersArr['rating'] = ceil($providersArr['rating']);
      $providersArr = array_merge($providersArr, $getContentImages);

      $providerArr[] = $providersArr;
    }
    if(!empty($providerArr)){
      $response['response'] = $providerArr;
    }
    else
      $response['response'] = "";

    $this->respondWithSuccess($response);
  }

  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'create')->isValid()) 
      $this->respondWithError('unauthorized');

    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError('unauthorized');

    $values = array();

    $viewer = Engine_Api::_()->user()->getViewer();
    $values['owner_id'] = $viewer->getIdentity();

    //CHECKING SERVICE CREATION QUOTA

    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitebooking_pro', 'max');

    $table = Engine_Api::_()->getItemTable('sitebooking_pro');

    // Fetch the user, all service providers
    $result = $table->fetchAll($table->select()
        ->where('owner_id = ?', $viewer->user_id));
    $count = count($result);
    if($this->getRequest()->isGet()) {
        $form['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceProviderForm();
        $this->respondWithSuccess($form);
    }
    // checking auto approving permission
    $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'approve');
    if($autoApprove == 1){
      $values['approved'] = 1;
    }else{
      $values['approved'] = 0;
    }

    if( $this->getRequest()->isPost() ) {
      if(!empty($quota) && ($count > $quota))
        $this->respondWithError("unauthorized");

      // Process
      $provider = Engine_Api::_()->getItemTable('sitebooking_pro');
      $location = Engine_Api::_()->getItemTable('sitebooking_providerlocation');
      $db = $provider->getAdapter();
      $db->beginTransaction();

      try {
        $formValues = array();
        $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceProviderForm();
        foreach ($getForm as $element) {
            if (isset($_REQUEST[$element['name']]))
                $formValues[$element['name']] = $_REQUEST[$element['name']];
        }
        // Start form validation
        $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'Sitebooking')->getFormValidators();
        $formValues['validators'] = $validators;
        $validationMessage = $this->isValid($formValues);
        // End Form Validation
        $formValues['view'] = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'auth_view');
        $formValues['comment'] = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'auth_comment');
        $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
        if(!empty($formValues['slug']))
          $providers = $providerTable->fetchRow($providerTable->select()->where('slug LIKE ?',$formValues['slug']));

        if(!empty($providers)){
            $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
            $validationMessage['slug'] = $this->translate('This URL is already taken, please try another');
        }

        if (!empty($validationMessage) && @is_array($validationMessage)) {
          $this->respondWithValidationError('validation_fail', $validationMessage);
        }
        if(!empty($formValues['description_provider']))
            $formValues['description'] = $formValues['description_provider'];

        $values = array_merge($formValues,$values);
    
        $provider = $provider->createRow();
        $provider->setFromArray($values);
        $provider->save();


        if( !empty($_FILES['photo']) ) {
          $provider->setPhoto($_FILES['photo'], 1);
        }

        $tags = preg_split('/[,]+/', $values['tags']);
        $provider->tags()->addTagMaps($viewer, $tags);

        //location
        $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

        if($locationFieldcoreSettings === "yes") {
          $location = $location->createRow();
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
        $this->respondWithValidationError('internal_server_error', $e->getMessage());
      }

      $this->setRequestMethod();
      $this->_forward('view', 'serviceprovider', 'sitebooking', array(
          'pro_id' => $provider->getIdentity(),
      ));
    }
  }

  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError("unauthorized");

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid()) $this->respondWithError("unauthorized");

    $pro_id = $this->_getParam('pro_id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() ) 
      $this->respondWithError("unauthorized");

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      Engine_Api::_()->core()->setSubject($provider);
    }

    $location = Engine_Api::_()->getDbtable('providerlocations','sitebooking');
    $location = $location->fetchRow('pro_id = '.$this->_getParam('pro_id'));

    if ($this->getRequest()->isGet()) {  
        $form_fields['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceProviderForm($provider,1);
        
        $form_fields['formValues'] = $provider->toArray();
        if(!empty($form_fields['formValues']['description']))
            $form_fields['formValues']['description_provider'] = $form_fields['formValues']['description'];

        $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $provider->getIdentity());
        if(!empty($favourite_id_temp)){
          $form_fields['formValues']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
        }
        else
          $form_fields['formValues']['favourite_id'] = 0;
        $tagStr = '';
        foreach( $provider->tags()->getTagMaps() as $tagMap ) {
          $tag = $tagMap->getTag();
          if( !isset($tag->text) ) continue;
          if( '' !== $tagStr ) $tagStr .= ', ';
          $tagStr .= $tag->text;
        }
        $form_fields['formValues']['tags'] = $tagStr;
        $this->respondWithSuccess($form_fields);
    }
    $preStatus = $provider->status;

    if( $this->getRequest()->isPost() ) {    
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $formValues = $values = array();
          $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceProviderForm();
          foreach ($getForm as $element) {
              if (isset($_REQUEST[$element['name']]))
                  $formValues[$element['name']] = $_REQUEST[$element['name']];
          }
        if(!empty($formValues['description_provider']))
            $formValues['description'] = $formValues['description_provider'];
        $values = $formValues;
        $providerTable = Engine_Api::_()->getDbtable('pros','sitebooking');
        $providers = $providerTable->fetchAll($providerTable->select()->where('slug LIKE ?',$values['slug']));
        foreach($providers as $key => $value){
            if($value->pro_id != $pro_id){
                $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                  $validationMessage['slug'] = $this->translate('This URL is already taken, please try another');
            }
        }
        if (!empty($validationMessage) && @is_array($validationMessage)) {
          $this->respondWithValidationError('validation_fail', $validationMessage);
        }

        $provider->setFromArray($values);
        $provider->save();
        if( !empty($_FILES['photo']) ) {
          $provider->setPhoto($_FILES['photo'], 1);
        }

        if( !empty($formValues['coverPhoto']) ) {
          $provider->setCoverPhoto($_FILES['coverPhoto'],1);
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
        $this->successResponseNoContent('no_content', true);
      }
      catch (Execption $e) {
          $db->rollBack();
          $this->respondWithValidationError('internal_server_error', $e->getMessage());
      }
    }
  }

  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError("unauthorized");
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check permission
    if(!$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'view')->isValid()) 
      $this->respondWithError("unauthorized");
    
    $values = $this->getRequestAllParams;
    if (!isset($values['limit']))
        $values['limit'] = (int) $this->getRequestParam('limit', 20);

    // Set default page
    if (!isset($values['page']))
        $values['page'] = (int) $this->getRequestParam('page', 1);
    $values['user_id'] = $viewer->getIdentity();
    $values['enabled'] = 1;

    $response = array();
    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('sitebooking_pro')->getProvidersPaginator($values);
    $paginator->setItemCountPerPage($values['limit']);
    $paginator->setCurrentPageNumber($values['page']);
    $response['totalItemCount'] = $paginator->getTotalItemCount();
    $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'create')->isValid();
    foreach($paginator as $provider){
      $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider);
      
      $providersArr = $provider->toArray();
      $providersArr['description_provider'] = $providersArr['description'];
      $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $provider->getIdentity());
      if(!empty($favourite_id_temp)){
        $providersArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
      }
      else
        $providersArr['favourite_id'] = 0;

      $providersArr = array_merge($providersArr, $getContentImages);
      $tempMenu = array();
      if ($provider->isOwner($viewer)) {
        if($this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid())
          $tempMenu[] = array(
              'label' => $this->translate('Edit'),
              'name' => 'edit',
              'url' => 'provider/edit/' . $provider->getIdentity(),
          );

          if(!empty($provider->enabled)){
              $tempMenu[] = array(
                'name' => 'disable',
                'label' => $this->translate('Disable'),
                'url' => 'provider/disable/' . $provider->getIdentity()
            );
          }
          else
          {
            $tempMenu[] = array(
                        'name' => 'enable',
                        'label' => $this->translate('Enable'),
                        'url' => 'provider/enable/' . $provider->getIdentity()
                    );
          }
      }
      $providersArr["menu"] = $tempMenu;
      $providerArr[] = $providersArr;
    }

    if(!empty($providerArr)){
      $response['response'] = $providerArr;
    }
    else
      $response['response'] = "";

    $this->respondWithSuccess($response);
  }


  public function deleteAction()
  {
    // Validate request methods
    $this->validateRequestMethod('DELETE');
    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError("unauthorized");

    if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'delete')->isValid()) $this->respondWithError("unauthorized");
    $viewer = Engine_Api::_()->user()->getViewer();
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->getRequest()->getParam('pro_id'));
    
    if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'edit')->isValid() )
      $this->respondWithError("unauthorized");

    $db = $provider->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      $provider->delete();
      
      $db->commit();
      $this->successResponseNoContent('no_content', true);
    } catch( Exception $e ) {
      $db->rollBack();
      $this->respondWithValidationError('internal_server_error', $e->getMessage());
    }

  }

  public function viewAction()
  { 
      $viewer = Engine_Api::_()->user()->getViewer();

      $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));
      $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');

      // Check permission
      if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'view')->isValid() ) 
        $this->respondWithError("unauthorized");

      if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
        Engine_Api::_()->core()->setSubject($provider);
      }
      $bodyParams['response'] = $provider->toArray();
      $bodyParams['response']['rating'] = ceil($bodyParams['response']['rating']);
      $bodyParams['response']['description_provider'] = $bodyParams['response']['description'];

      // work to fetch the favourite_id of the resource of user.
      $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $provider->getIdentity());
      if(!empty($favourite_id_temp)){
        $bodyParams['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
      }
      else
        $bodyParams['response']['favourite_id'] = 0;

      // Getting viewer like or not to content.
      $bodyParams['response'] ["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($provider);

      // Add images
      $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider);
      $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider, true);
      $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
      $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
      $bodyParams['response']["owner_title"] = $provider->getOwner()->getTitle();

      // Cover photo work
      $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
      if ($provider->cover_id) {
        $cover = Engine_Api::_()->getItemTable('storage_file')->getFile($provider->cover_id);
        $bodyParams['response']['cover_photo'] =  (strstr($cover->getHref(), 'http://') || strstr($cover->getHref(), 'https://')) ? $cover->getHref() : $getHost . $cover->getHref();
      }

      $providerOverviewTable = Engine_Api::_()->getDbTable('providersoverviews','sitebooking');
      $item = $providerOverviewTable->fetchRow($providerOverviewTable->select()->where('pro_id = ?',$this->_getParam('pro_id')));
      $bodyParams['response']['overview'] = !empty($item->overview) ? $item->overview : "" ;

      // Cover photo work
      $providerInfo['Location'] = $provider->location;
      $providerInfo['Designation'] = $provider->designation;
      if($provider->no_of_bookings != 0){
        $providerInfo['Number Of Bookings'] = $provider->no_of_bookings;
      }   
      $providerInfo['Description'] = $provider->description;
      $bodyParams['response']["info"] = $providerInfo;

      if( !$provider->isOwner($viewer) ) {
        $providerTable->update(array(
          'view_count' => new Zend_Db_Expr('view_count + 1'),
        ), array(
            'pro_id = ?' => $provider->getIdentity(),
        ));
      }
      
      if ($this->getRequestParam('gutter_menu', true))
        $bodyParams['gutterMenu'] = $this->_gutterMenus($provider);

      if ($this->getRequestParam('tabs_menu', true))
        $bodyParams['profile_tabs'] = $this->_tabsMenus($provider);

      $this->respondWithSuccess($bodyParams);  
  }

  public function informationAction(){
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));
    $bodyParams['response'] = $provider->toArray();

    // Add images
    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider);
    $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($provider, true);
    $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
    $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
    $bodyParams['response']["owner_title"] = $provider->getOwner()->getTitle();
    $providerInfo['Location'] = $provider->location;
    $providerInfo['Designation'] = $provider->designation;
    if($provider->no_of_bookings != 0){
      $providerInfo['Number Of Bookings'] = $provider->no_of_bookings;
    }   
    $providerInfo['Description'] = $provider->description;
    $bodyParams['response']["info"] = $providerInfo;

    $this->respondWithSuccess($bodyParams);
  }

  public function servicesAction(){
    $pro_id = $this->_getParam('pro_id');
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);

    $value = $this->getRequestAllParams;
      if (!isset($value['limit']))
          $value['limit'] = (int) $this->getRequestParam('limit', 20);

      // Set default page
      if (!isset($value['page']))
          $value['page'] = (int) $this->getRequestParam('page', 1);

    $values['parent_id'] = $provider->getIdentity();
    $service = Engine_Api::_()->getItemTable('sitebooking_ser');

      $sql = $service->select()->where('parent_id = ?',$values['parent_id'])
        ->where('approved = ?',1)
        ->where('enabled = ?',1)
        ->where('status = ?',1);

      $paginators = Zend_Paginator::factory($sql);
      $paginators->setItemCountPerPage($value['limit']);
      $paginators->setCurrentPageNumber($value['page']);
      $response['totalItemCount'] = $paginators->getTotalItemCount();
      $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
      foreach($paginators as $paginator){
            $servicesArr = $paginator->toArray();
            $servicesArr['description_service'] = $servicesArr['description'];
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $paginator->getIdentity());
            if(!empty($favourite_id_temp)){
                $servicesArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
            }
            else
                $servicesArr['favourite_id'] = 0;
            $serviceCat = Engine_Api::_()->getItem('sitebooking_category', $paginator->category_id);
            $servicesArr['category_title'] = $serviceCat->category_name;
            $servicesArr['duration_period'] = Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($paginator->duration); 
            
            $provider = Engine_Api::_()->getItem('sitebooking_pro', $paginator->parent_id);

            $servicesArr['email'] = $provider->email;
            $servicesArr['website'] = $provider->website;
            $servicesArr['telephone_no'] = $provider->telephone_no;

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($paginator); 
            $servicesArr = array_merge($servicesArr, $getContentImages);
            $serviceArr[] = $servicesArr;
      }
      if(!empty($serviceArr)){
        $response['response'] = $serviceArr;
      }
      else
        $response['response'] = "";

      $this->respondWithSuccess($response);
  }

  public function reviewAction(){
      $viewer = Engine_Api::_()->user()->getViewer();
      $resource_id = $this->_getParam('resource_id');
      $resource_type = $this->_getParam('resource_type');
      $user_id = $viewer->getIdentity();

      $value = $this->getRequestAllParams;
      if (!isset($value['limit']))
          $value['limit'] = (int) $this->getRequestParam('limit', 20);

      // Set default page
      if (!isset($value['page']))
          $value['page'] = (int) $this->getRequestParam('page', 1);

      //REVIEWS TABLE
        $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

        $select = $table->select()->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type);

        $reviewsPaginators = Zend_Paginator::factory($table->fetchAll($select));
       
        $response['review_count'] = $reviewsPaginators->getTotalItemCount();

        //resource item
        $resource_item = Engine_Api::_()->getItem($resource_type, $resource_id);

        //avgRating
        $response['avgRating'] = ceil($resource_item['rating']);
        //rating_count
        $response['rating_count'] = $resource_item['rating_count'];

        //REVIEW Table
        $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
          $reviewName = $review->info('name');

          // for service
        if($resource_type === 'sitebooking_ser') {

          $star_arr = array();

          $serviceRating = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

          $rating = $serviceRating->fetchRow($serviceRating->select()->where('ser_id = ?', $resource_id)
          ->where('user_id = ?', $user_id));

          $response['myRating'] = !empty($rating['rating']) ? $rating['rating'] : 0 ;

          //USER REVIEW
          $serviceRatingName = $serviceRating->info('name');

          //REVIEW
          $select = $review->select();
          $select
            ->setIntegrityCheck(false)
            ->from($reviewName)
            ->join($serviceRatingName, "$reviewName.user_id = $serviceRatingName.user_id");

          $sql = $reviewName.".resource_id = ".$resource_id." AND ".$reviewName.".resource_type = '".$resource_type."' AND ".$serviceRatingName.".ser_id = ".$resource_id;
          $select->group($reviewName.'.user_id');
          $select->where($sql);
          // END

          $paginators = Zend_Paginator::factory($select);
          $paginators->setItemCountPerPage($value['limit']);
          $paginators->setCurrentPageNumber($value['page']);
          $response['totalItemCount'] = $paginators->getTotalItemCount();
     
          foreach($paginators as $paginator){
           
            $serviceArr = $paginator->toArray();
            if(empty($serviceArr['review']))
              $serviceArr['review'] = "";

            $user = Engine_Api::_()->getItem('user', $paginator->user_id);
            $serviceArr['owner_title'] = !empty($user->getTitle()) ? $user->getTitle() : 'DELETED MEMBER' ;
            $servicesArr[] = $serviceArr;
          }
          if(!empty($servicesArr)){
            $response['response'] = $servicesArr;
          }
          else
            $response['response'] = ""; 
        }

        //for service provider
        if($resource_type === 'sitebooking_pro') {

          $star_arr = array();

          $providerRating = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');

          $rating = $providerRating->fetchRow($providerRating->select()->where('pro_id = ?', $resource_id)
          ->where('user_id = ?', $user_id));

          $response['myRating'] = !empty($rating['rating']) ? $rating['rating'] : 0 ;

          //USER REVIEW
          $providerRatingName = $providerRating->info('name');

          // REVIEW
          $select = $review->select();
          $select
            ->setIntegrityCheck(false)
            ->from($reviewName)
            ->join($providerRatingName, "$reviewName.user_id = $providerRatingName.user_id");

          $sql = $reviewName.".resource_id = ".$resource_id." AND ".$reviewName.".resource_type = '".$resource_type."' AND ".$providerRatingName.".pro_id = ".$resource_id;
          $select->group($reviewName.'.user_id');
          $select->where($sql);
          // END

          $paginators = Zend_Paginator::factory($select);
          $paginators->setItemCountPerPage($value['limit']);
          $paginators->setCurrentPageNumber($value['page']);
          $response['totalItemCount'] = $paginators->getTotalItemCount();
     
          foreach($paginators as $paginator){
            $providerArr = $paginator->toArray();
            if(empty($providerArr['review']))
              $providerArr['review'] = "";
            $user = Engine_Api::_()->getItem('user', $paginator->user_id);
            $providerArr['owner_title'] = !empty($user->getTitle()) ? $user->getTitle() : 'DELETED MEMBER' ;
            $providersArr[] = $providerArr;
          }
          if(!empty($providersArr)){
            $response['response'] = $providersArr;
          }
          else
            $response['response'] = "";   
        }
        $this->respondWithSuccess($response);
  }

  public function _tabsMenus($subject) {
    
    $tabsMenu[] = array(
        'name' => 'information',
        'label' => $this->translate('Info'),
        'url' => 'provider/information/' . $subject->getIdentity()
    );

    $providerOverviewTable = Engine_Api::_()->getDbTable('providersoverviews','sitebooking');
    $item = $providerOverviewTable->fetchRow($providerOverviewTable->select()->where('pro_id = ?',$subject->getIdentity()));

    if(!empty($item)){
      $tabsMenu[] = array(
        'name' => 'overview',
        'label' => $this->translate('Overview'),
        'url' => 'provider/overview/' . $subject->getIdentity()
      );
    }

    $service = Engine_Api::_()->getItemTable('sitebooking_ser');

    $sql = $service->select()->where('parent_id = ?',$subject->getIdentity())
        ->where('approved = ?',1)
        ->where('enabled = ?',1)
        ->where('status = ?',1);
    $servicesPaginators = Zend_Paginator::factory($service->fetchAll($sql));
    if($servicesPaginators->getTotalItemCount() > 0)
      $tabsMenu[] = array(
          'totalItemCount' => $servicesPaginators->getTotalItemCount(),
          'name' => 'services',
          'label' => $this->translate('Services'),
          'url' => 'provider/services/' . $subject->getIdentity()
      );

    $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

    $select = $table->select()->where('resource_id = ?', $subject->getIdentity())
        ->where('resource_type = ?', $subject->getType());

    $reviewsPaginators = Zend_Paginator::factory($table->fetchAll($select));
    $tabsMenu[] = array(
        'totalItemCount' => $reviewsPaginators->getTotalItemCount(),
        'name' => 'review',
        'label' => $this->translate('Review'),
        'url' => 'provider/review/' . $subject->getIdentity(),
        'urlParams' => array(
                    'resource_type' => 'sitebooking_pro',
                    'resource_id' => $subject->getIdentity(),
                ),
    );
    return $tabsMenu;        
  }

  public function overviewAction(){
        $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->_getParam('pro_id'));
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!$provider) {
          $this->respondWithError("unauthorized");
        }
        $bodyParams = array();
        $pro_id = $provider->getIdentity();
        $providerOverviewTable = Engine_Api::_()->getDbTable('providersoverviews','sitebooking');
        $item = $providerOverviewTable->fetchRow($providerOverviewTable->select()->where('pro_id = ?',$pro_id));

        $bodyParams = $item->toArray();
        if(!empty($bodyParams)){
            $response['response'] = $bodyParams;
        }
        else
            $response['response'] = "";
        $this->respondWithSuccess($response);
    }


  public function _gutterMenus($subject, $action = 'view') {
      $viewer = Engine_Api::_()->user()->getViewer();
      $getParentHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
      $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $baseParentUrl = @trim($baseParentUrl, "/");
      $getHost = $getParentHost. DIRECTORY_SEPARATOR. $baseParentUrl;

      if($subject->owner_id == $viewer->getIdentity()){
        if($this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid())
          $menus[] = array(
                      'name' => 'edit',
                      'label' => $this->translate('Edit'),
                      'url' => 'provider/edit/' . $subject->getIdentity()
                  );

        if(!empty($subject->enabled)){
          $menus[] = array(
                      'name' => 'disable',
                      'label' => $this->translate('Disable'),
                      'url' => 'provider/disable/' . $subject->getIdentity()
                  );
        }
        else
        {
          $menus[] = array(
                      'name' => 'enable',
                      'label' => $this->translate('Enable'),
                      'url' => 'provider/enable/' . $subject->getIdentity()
                  );
        }
        if(!empty($subject->enabled) && ($this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'create')->isValid()) )
          $menus[] = array(
                  'name' => 'addService',
                  'label' => $this->translate('Add Service'),
                  'url' => 'services/create',
                  'urlParams' => array(
                          'pro_id' => $subject->getIdentity(),
                      ),
              );

        $menus[] = array(
            'name' => 'booking_requests',
            'label' => $this->translate('Booking Requests'),
            'url' => $getHost . '/bookings/providers/booked/' . $subject->getIdentity(),
            'urlParams' => array(
                "resource_type" => $subject->getType(),
                "resource_id" => $subject->getIdentity()
            )
        );

        $menus[] = array(
            'name' => 'timing_and_availability',
            'label' => $this->translate('Set Timings & Availability'),
            'url' => $getHost . '/bookings/providers/available/' . $subject->getIdentity(),
            'urlParams' => array(
                "resource_type" => $subject->getType(),
                "resource_id" => $subject->getIdentity()
            )
        );

        $menus[] = array(
            'name' => 'contact_details',
            'label' => $this->translate('Contact Details'),
            'url' => $getHost . '/bookings/providers/contact/' . $subject->getIdentity(),
            'urlParams' => array(
                "resource_type" => $subject->getType(),
                "resource_id" => $subject->getIdentity()
            )
        );
      }

      // Share Page
      if (!empty($viewer->getIdentity()) && ($action == 'view')) {
        if(Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.sharelink",'yes') == yes) {
          $menus[] = array(
              'name' => 'share',
              'label' => $this->translate('Share This Page'),
              'url' => 'activity/share',
              'urlParams' => array(
                  "type" => $subject->getType(),
                  "id" => $subject->getIdentity()
              )
          );
        }

        $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $subject->getIdentity());
          if(!empty($favourite_id_temp)){
              $menus[] = array(
                'name' => 'unfavourite',
                'label' => $this->translate('Unfavourite'),
                'url' => 'favourite',
                'urlParams' => array(
                    "resource_type" => $subject->getType(),
                    "resource_id" => $subject->getIdentity()
                )
              );
          }
          else
              $menus[] = array(
                'name' => 'favourite',
                'label' => $this->translate('Add to Favourite'),
                'url' => 'favourite',
                'urlParams' => array(
                    "resource_type" => $subject->getType(),
                    "resource_id" => $subject->getIdentity()
                )
              );

          $menus[] = array(
                    'name' => 'tellafriend',
                    'label' => $this->translate('Tell a friend'),
                    'url' => 'provider/tellafriend/' . $subject->getIdentity()
              );

          if (!empty($viewer->getIdentity()) && ($action == 'view') && ($viewer->getIdentity() != $subject->owner_id)) {
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.report",'yes') == yes)
              $menus[] = array(
                  'name' => 'report',
                  'label' => $this->translate('Report Provider'),
                  'url' => 'report/create/subject/' . $subject->getGuid(),
                  'urlParams' => array(
                      "type" => $subject->getType(),
                      "id" => $subject->getIdentity()
                  )
              );
          }

          if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview') != 'provider_none') {
                  
            $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

            $review_row = $table->fetchRow($table->select()->where('resource_id = ?', $subject->getIdentity())
              ->where('resource_type = ?', $subject->getType())
              ->where('user_id = ?', $viewer->getIdentity())); 

            $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
            $rated = $ratingTable->checkRated($subject->getIdentity(), $viewer->getIdentity());

            $rating_row = $ratingTable->getMyRating($subject->getIdentity(), $viewer->getIdentity());

            $providerReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

            if($providerReview == 'provider_onlyRating'){
              if(!empty($rating_row->toArray())){
              
              }
              else
              {
                $menus[] = array(
                    'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview'),
                    'name' => 'review_create',
                    'label' => $this->translate('Rating'),
                    'url' => 'reviews/create',
                    'urlParams' => array(
                        "subject_type" => $subject->getType(),
                        "subject_id" => $subject->getIdentity()
                    )
                );
              }
            }  
            else{
              if(!empty($review_row)){
                $menus[] = array(
                    'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview'),
                    'name' => 'review_edit',
                    'label' => $this->translate('Update Review'),
                    'url' => 'reviews/edit',
                    'urlParams' => array(
                        "subject_type" => $subject->getType(),
                        "subject_id" => $subject->getIdentity()
                    )
                );
              }
              else{
                  $menus[] = array(
                      'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview'),
                      'name' => 'review_create',
                      'label' => $this->translate('Write a Review'),
                      'url' => 'reviews/create',
                      'urlParams' => array(
                          "subject_type" => $subject->getType(),
                          "subject_id" => $subject->getIdentity()
                      )
                  );
              }
            }   
          }
      }

    return $menus;
  }

  //ACTION FOR TELL A FRIEND ABOUT EVENT
  public function tellafriendAction() {

      //CHECK USER VALIDATION
      if (!$this->_helper->requireUser()->isValid())
          $this->respondWithError('unauthorized');

      //GET VIEWER
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();

      //GET FORM
      if ($this->getRequest()->isGet()) {
          $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitebooking')->getTellAFriendForm();
          $response['formValues'] = array(
              'sender_name' => $viewer->displayname,
              'sender_email' => $viewer->email
          );
          $this->respondWithSuccess($response, true);
      } else if ($this->getRequest()->isPost()) {
          //FORM VALIDATION
          //GET EVENT ID AND OBJECT
          $pro_id = $this->_getParam('pro_id', $this->_getParam('pro_id', null));
          $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
          if (empty($provider))
              $this->respondWithError('no_record');
          //GET FORM VALUES
          $values = $this->_getAllParams();
          $errorMessage = array();

          if (empty($values['sender_email']) && !isset($values['sender_email']))
              $errorMessage[] = $this->translate("Your Email field is required");

          if (empty($values['sender_name']) && !isset($values['sender_name']))
              $errorMessage[] = $this->translate("Your Name field is required");

          if (empty($values['message']) && !isset($values['message']))
              $errorMessage[] = $this->translate("Message field is required");

          if (empty($values['receiver_emails']) && !isset($values['receiver_emails']))
              $errorMessage[] = $this->translate("To field is required");

          if (isset($errorMessage) && count($errorMessage) > 0)
              $this->respondWithValidationError('validation_fail', $errorMessage);

          //EXPLODE EMAIL IDS
          $reciver_ids = explode(',', $values['receiver_emails']);
          if (!empty($values['send_me'])) {
              $reciver_ids[] = $values['sender_email'];
          }
          $sender_email = $values['sender_email'];
          $heading = ucfirst($provider->getTitle());

          //CHECK VALID EMAIL ID FORMAT
          $validator = new Zend_Validate_EmailAddress();
          $validator->getHostnameValidator()->setValidateTld(false);
          $errorMessage = array();

          if (!$validator->isValid($sender_email)) {
              $errorMessage[] = $this->translate('Invalid sender email address value');
              $this->respondWithValidationError('validation_fail', $errorMessage);
          }
          $errorMessage = array();
          foreach ($reciver_ids as $receiver_id) {
              $receiver_id = trim($receiver_id, ' ');
              ($reciver_ids);
              if (!$validator->isValid($receiver_id)) {
                  $errorMessage[] = $this->translate('Please enter correct email address of the receiver(s).');
                  $this->respondWithValidationError('validation_fail', $errorMessage);
              }
          }
          $slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providersingular', 'provider');
          $sender = $values['sender_name'];
          $message = $values['message'];
          try {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'PROVIDER_TELLAFRIEND_EMAIL', array(
                  'host' => $_SERVER['HTTP_HOST'],
                  'sender_name' => $sender,
                  'provider_title' => $heading,
                  'message' => '<div>' . $message . '</div>',
                  'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . $provider->getHref(),
                  'sender_email' => $sender_email,
                  'queue' => true
              ));
          } catch (Exception $ex) {
              $this->respondWithError('internal_server_error', $ex->getMessage());
          }
          $this->successResponseNoContent('no_content', true);
      }
  }

  public function disableAction()
  {
    $this->validateRequestMethod('POST');
    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError("unauthorized");
    $viewer = Engine_Api::_()->user()->getViewer();

    $id = $this->_getParam('pro_id');

    if (!empty($id)) {
      $providerItem = Engine_Api::_()->getItem('sitebooking_pro', $id);
    }

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');
    
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

        $bodyParams['response'] = $providerItem->toArray();
        $bodyParams['response']['rating'] = ceil($bodyParams['response']['rating']);
        $bodyParams['response']['description_provider'] = $bodyParams['response']['description'];

        // work to fetch the favourite_id of the resource of user.
        $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $providerItem->getIdentity());
        if(!empty($favourite_id_temp)){
          $bodyParams['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
        }
        else
          $bodyParams['response']['favourite_id'] = 0;

        // Getting viewer like or not to content.
        $bodyParams['response'] ["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($providerItem);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($providerItem);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($providerItem, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
        $bodyParams['response']["owner_title"] = $providerItem->getOwner()->getTitle();

        // Cover photo work
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        if ($providerItem->cover_id) {
          $cover = Engine_Api::_()->getItemTable('storage_file')->getFile($providerItem->cover_id);
          $bodyParams['response']['cover_photo'] =  (strstr($cover->getHref(), 'http://') || strstr($cover->getHref(), 'https://')) ? $cover->getHref() : $getHost . $cover->getHref();
        }

        $providerOverviewTable = Engine_Api::_()->getDbTable('providersoverviews','sitebooking');
        $item = $providerOverviewTable->fetchRow($providerOverviewTable->select()->where('pro_id = ?',$this->_getParam('pro_id')));
        $bodyParams['response']['overview'] = !empty($item->overview) ? $item->overview : "" ;

        $tempMenu = array();
        if ($providerItem->isOwner($viewer)) {
          if($this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid())
            $tempMenu[] = array(
                'label' => $this->translate('Edit'),
                'name' => 'edit',
                'url' => 'provider/edit/' . $providerItem->getIdentity(),
            );

            if(!empty($providerItem->enabled)){
                $tempMenu[] = array(
                  'name' => 'disable',
                  'label' => $this->translate('Disable'),
                  'url' => 'provider/disable/' . $providerItem->getIdentity()
              );
            }
            else
            {
              $tempMenu[] = array(
                          'name' => 'enable',
                          'label' => $this->translate('Enable'),
                          'url' => 'provider/enable/' . $providerItem->getIdentity()
                      );
            }
        }
        $bodyParams["menu"] = $tempMenu;

        // Cover photo work  

      }
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      $this->respondWithValidationError('internal_server_error', $e->getMessage());
    }
    $this->respondWithSuccess($bodyParams); 
  }

  public function enableAction()
  { 
    $this->validateRequestMethod('POST');
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $id = $this->_getParam('pro_id');

    if (!empty($id)) {
      $providerItem = Engine_Api::_()->getItem('sitebooking_pro', $id);
    }

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');  

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

        $bodyParams['response'] = $providerItem->toArray();
        $bodyParams['response']['rating'] = ceil($bodyParams['response']['rating']);
        $bodyParams['response']['description_provider'] = $bodyParams['response']['description'];

        // work to fetch the favourite_id of the resource of user.
        $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_pro', $providerItem->getIdentity());
        if(!empty($favourite_id_temp)){
          $bodyParams['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
        }
        else
          $bodyParams['response']['favourite_id'] = 0;

        // Getting viewer like or not to content.
        $bodyParams['response'] ["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($providerItem);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($providerItem);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($providerItem, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
        $bodyParams['response']["owner_title"] = $providerItem->getOwner()->getTitle();

        // Cover photo work
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        if ($providerItem->cover_id) {
          $cover = Engine_Api::_()->getItemTable('storage_file')->getFile($providerItem->cover_id);
          $bodyParams['response']['cover_photo'] =  (strstr($cover->getHref(), 'http://') || strstr($cover->getHref(), 'https://')) ? $cover->getHref() : $getHost . $cover->getHref();
        }

        $providerOverviewTable = Engine_Api::_()->getDbTable('providersoverviews','sitebooking');
        $item = $providerOverviewTable->fetchRow($providerOverviewTable->select()->where('pro_id = ?',$this->_getParam('pro_id')));
        $bodyParams['response']['overview'] = !empty($item->overview) ? $item->overview : "" ;

        $tempMenu = array();
        if ($providerItem->isOwner($viewer)) {
          if($this->_helper->requireAuth()->setAuthParams('sitebooking_pro', null, 'edit')->isValid())
            $tempMenu[] = array(
                'label' => $this->translate('Edit'),
                'name' => 'edit',
                'url' => 'provider/edit/' . $providerItem->getIdentity(),
            );

            if(!empty($providerItem->enabled)){
                $tempMenu[] = array(
                  'name' => 'disable',
                  'label' => $this->translate('Disable'),
                  'url' => 'provider/disable/' . $providerItem->getIdentity()
              );
            }
            else
            {
              $tempMenu[] = array(
                          'name' => 'enable',
                          'label' => $this->translate('Enable'),
                          'url' => 'provider/enable/' . $providerItem->getIdentity()
                      );
            }
        }
        $bodyParams["menu"] = $tempMenu;
        // Cover photo work  
      }
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      $this->respondWithValidationError('internal_server_error', $e->getMessage());
    }
    $this->respondWithSuccess($bodyParams);
  }
  
  public function removeCoverPhotoAction()
  {
    // Validate request methods
    $this->validateRequestMethod('DELETE');
    try{
      $provider_id = $this->_getParam('pro_id', 0);

      if (empty($provider_id))
        $this->respondWithValidationError("parameter_missing", "provider_id");

      $provider = Engine_Api::_()->getItem('sitebooking_pro',$provider_id);
      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$this->_helper->requireUser()->isValid())
        $this->respondWithError('unauthorized');

      if( !$provider->isOwner($viewer) )
        $this->respondWithError('unauthorized');

      $provider->cover_id = 0;
      $provider->save();
      $this->successResponseNoContent('no_content', true);
    }
    catch(Exception $e){
      $this->respondWithValidationError('internal_server_error', $e);
    }
  }

  public function removePhotoAction()
  {
    // Validate request methods
    $this->validateRequestMethod('DELETE');
    try{
        $provider_id = $this->_getParam('pro_id', 0);

        if (empty($provider_id))
          $this->respondWithValidationError("parameter_missing", "provider_id");

        if (!empty($provider_id)){
          $provider = Engine_Api::_()->getItem('sitebooking_pro',$provider_id);
          $provider_photo = $provider->photo_id;
          $viewer = Engine_Api::_()->user()->getViewer();

          if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

          if( !$provider->isOwner($viewer) )
            $this->respondWithError('unauthorized');

          $provider->photo_id = 0;
          $provider->save();
      }
      $this->successResponseNoContent('no_content', true);
    }
    catch(Exception $e){
      $this->respondWithValidationError('internal_server_error', $e);
    }
  }
  public function uploadProfilePhotoAction()
  {
    try{
      $provider_id = $this->_getParam('pro_id', 0);

      if (empty($provider_id))
        $this->respondWithValidationError("parameter_missing", "provider_id");

      $provider = Engine_Api::_()->getItem('sitebooking_pro',$provider_id);
      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$this->_helper->requireUser()->isValid())
        $this->respondWithError('unauthorized');

      if( !$provider->isOwner($viewer) )
        $this->respondWithError('unauthorized');

      if (empty($_FILES['photo']))
        $this->respondWithError('unauthorized','No file');
  
      $provider->setPhoto($_FILES['photo'], 1);

      $this->successResponseNoContent('no_content', true);
    }
    catch(Exception $e){
      $this->respondWithValidationError('internal_server_error', $e);
    }
  }

  public function getPhotoMenusAction()
  { 
      $provider_id = $this->_getParam('pro_id', 0);

      if (empty($provider_id))
        $this->respondWithValidationError("parameter_missing", "provider_id");

      $provider = Engine_Api::_()->getItem('sitebooking_pro',$provider_id);
      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$this->_helper->requireUser()->isValid())
        $this->respondWithError('unauthorized');

      if($provider->isOwner($viewer)){
        $coverMenu['coverPhotoMenu'][] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Upload Cover Photo'),
            'name' => 'upload_cover_photo',
            'url' => 'provider/upload-cover-photo/'. $provider_id,
        );
        if($provider->cover_id != 0)
          $coverMenu['coverPhotoMenu'][] = array(
              'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Cover Photo'),
              'name' => 'remove_cover_photo',
              'url' => 'provider/remove-cover-photo/'. $provider_id,
          );
        $coverMenu['profilePhotoMenu'][] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Upload Profile Photo'),
            'name' => 'upload_profile_photo',
            'url' => 'provider/upload-profile-photo/'. $provider_id,
        );
        if($provider->photo_id != 0)
          $coverMenu['profilePhotoMenu'][] = array(
              'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Profile Photo'),
              'name' => 'remove_profile_photo',
              'url' => 'provider/remove-photo/'. $provider_id,
          );
        
      }
      $coverMenu['coverPhotoMenu'][] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Cover Photo'),
            'name' => 'view_cover_photo',
            'url' => '',
      );
      $coverMenu['profilePhotoMenu'][] = array(
          'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Profile Photo'),
          'name' => 'view_profile_photo',
          'url' => '',
      );

      $this->respondWithSuccess($coverMenu);
  }

  public function uploadCoverPhotoAction()
  {
    // Validate request methods
    $this->validateRequestMethod('POST');

    $provider_id = $this->_getParam('pro_id', 0);

    if (empty($provider_id))
      $this->respondWithValidationError("parameter_missing", "provider_id");

    $provider = Engine_Api::_()->getItem('sitebooking_pro',$provider_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireUser()->isValid())
      $this->respondWithError('unauthorized');

    if( !$provider->isOwner($viewer) )
      $this->respondWithError('unauthorized');

    if (empty($_FILES['photo']))
      $this->respondWithError('unauthorized','No file');

    try {
        $provider->setCoverPhoto($_FILES['photo'], 1);

      $this->successResponseNoContent('no_content', true);
    }
    catch(Exception $e){
      $this->respondWithValidationError('internal_server_error', $e);
    }
  }
}
?>