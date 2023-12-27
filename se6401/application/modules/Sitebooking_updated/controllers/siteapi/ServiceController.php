<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitebooking
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitebooking_ServiceController extends Siteapi_Controller_Action_Standard {
    public function init()
    {
        // only show to member_level if authorized
        if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'view')->isValid() )
            $this->respondWithError("unauthorized");
    }

    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceSearchForm());
    }

    public function manageAction()
    { 
        $values = $this->getRequestAllParams;

        // Check permission
        if(!$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'view')->isValid()) 
            $this->respondWithError("unauthorized");

        if (!isset($values['limit']))
            $values['limit'] = (int) $this->getRequestParam('limit', 20);

        // Set default page
        if (!isset($values['page']))
            $values['page'] = (int) $this->getRequestParam('page', 1);

        $viewer = Engine_Api::_()->user()->getViewer();

        $servicebookingTable = Engine_Api::_()->getDbTable('sers','sitebooking');
        $servicebookingSelect = $servicebookingTable->select()->where('owner_id = ?', $viewer->getIdentity());

        $paginator = Zend_Paginator::factory($servicebookingTable->fetchAll($servicebookingSelect));
        $paginator->setItemCountPerPage($values['limit']);
        $paginator->setCurrentPageNumber($values['page']);
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
        foreach($paginator as $service){
            $servicesArr = $service->toArray();
            $servicesArr['description_service'] = $servicesArr['description'];
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $service->getIdentity());
            if(!empty($favourite_id_temp)){
                $servicesArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
            }
            else
                $servicesArr['favourite_id'] = 0;
            $serviceCat = Engine_Api::_()->getItem('sitebooking_category', $service->category_id);
            $servicesArr['category_title'] = $serviceCat->category_name;
            $servicesArr['duration_period'] = Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($service->duration); 
            
            $provider = Engine_Api::_()->getItem('sitebooking_pro', $service->parent_id);

            $servicesArr['email'] = $provider->email;
            $servicesArr['website'] = $provider->website;
            $servicesArr['telephone_no'] = $provider->telephone_no;

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service); 
            $servicesArr = array_merge($servicesArr, $getContentImages);
            $tempMenu = array();
            if ($service->isOwner($viewer)) {
                if($this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'edit')->isValid())
                    $tempMenu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'service/edit/' . $service->getIdentity(),
                    );

                if($this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'delete')->isValid())
                    $tempMenu[] = array(
                                'name' => 'delete',
                                'label' => $this->translate('Delete'),
                                'url' => 'service/delete/' . $service->getIdentity()
                            );  
            }
            $servicesArr["menu"] = $tempMenu;
            $serviceArr[] = $servicesArr;
        }
        if(!empty($serviceArr)){
          $response['response'] = $serviceArr;
        }
        else
          $response['response'] = "";

        $this->respondWithSuccess($response);
    }

    public function indexAction()
    {
        $values = $this->getRequestAllParams;
        // Check permission
        if(!$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'view')->isValid()) 
            $this->respondWithError("unauthorized");

        if (!isset($values['limit']))
            $values['limit'] = (int) $this->getRequestParam('limit', 20);

        // Set default page
        if (!isset($values['page']))
            $values['page'] = (int) $this->getRequestParam('page', 1);

        $values['status'] = 1;
        $values['approved'] = 1;

        if(!empty($values['detectlocation']) && !empty($values['location']) && !empty($values['locationDistance'] )){
            $temp = json_decode($values['detectlocation'], true);
            $values['latitude'] = $temp['latitude'];
            $values['longitude'] = $temp['longitude'];
        }

        if (empty($values['location']) && !empty($values['locationDistance'])) {
          
            if (isset($values['city']) && !empty($values['city'])) {
                $values['location'].= $values['city'] . ',';
            } 
          
            if (isset($values['country']) && !empty($values['country'])) {
                $values['location'].= $values['country'];
            }
        }
       
        if(!empty($this->_getParam('category_id')))     
          $values['category'] = $this->_getParam('category_id');

        if(!empty($this->_getParam('first_level_category_id')))     
          $values['first_level_category_id'] = $this->_getParam('first_level_category_id');

        if(!empty($this->_getParam('second_level_category_id')))      
          $values['second_level_category_id'] = $this->_getParam('second_level_category_id');

        $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values);

        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage($values['limit']);
        $paginator->setCurrentPageNumber($values['page']);
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
        foreach($paginator as $service){
            $servicesArr = $service->toArray();
            $servicesArr['description_service'] = $servicesArr['description'];
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $service->getIdentity());
            if(!empty($favourite_id_temp)){
                $servicesArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
            }
            else
                $servicesArr['favourite_id'] = 0;
            $serviceCat = Engine_Api::_()->getItem('sitebooking_category', $service->category_id);
            $servicesArr['category_title'] = $serviceCat->category_name;

            $servicesArr['rating'] = ceil($servicesArr['rating']);

            $servicesArr['duration_period'] = Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($service->duration); 

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service); 
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

    public function serviceWishlistAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) 
            $this->respondWithError("unauthorized");              

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

        $paginator = Zend_Paginator::factory($sql);

        $paginator->setItemCountPerPage($values['limit']);
        $paginator->setCurrentPageNumber($values['page']);
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
        foreach($paginator as $service){
            $servicesArr = $service->toArray();
            $servicesArr['description_service'] = $servicesArr['description'];
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $service->getIdentity());
            if(!empty($favourite_id_temp)){
                $servicesArr['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
            }
            else
                $servicesArr['favourite_id'] = 0;
            $serviceCat = Engine_Api::_()->getItem('sitebooking_category', $service->category_id);
            $servicesArr['category_title'] = $serviceCat->category_name;

            $servicesArr['duration_period'] = Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($service->duration); 

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service); 
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

    public function createAction()
    {
        if( !$this->_helper->requireUser()->isValid() )
            $this->respondWithError("unauthorized");

        if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'create')->isValid()) 
            $this->respondWithError("unauthorized");

        $viewer = Engine_Api::_()->user()->getViewer();

        $autoApproveProvider = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'approve');
        
        $pro_id = $this->_getParam('pro_id');
        $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);

        $notApprove = 1;
        if($autoApproveProvider == 0 && $provider->approved == 0) {
          $notApprove = 0;
        } else {
          if($provider->approved == 0) { 
            $notApprove = 0;
          }
        }

        if( !$this->_helper->requireAuth()->setAuthParams($provider, $viewer, 'create')->isValid() ) 
            $this->respondWithError("unauthorized");
        try{
            $local_language = Zend_Registry::get('Zend_Translate')->getLocale();

            $local_language = explode('_', $local_language);
            $language = $local_language[0];
        }
        catch(Exception $e){

        }

        if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
          Engine_Api::_()->core()->setSubject($provider);
        }
        if($this->getRequest()->isGet()) {
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceForm();
            $this->respondWithSuccess($form);
        }

        // checki auto approving permission
        $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_ser', $viewer, 'approve');
        if($autoApprove == 1){
          $approved = 1;
        }else{
          $approved = 0;
        }

        //CHECKING SERVICE CREATION QUOTA

        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitebooking_ser', 'max');

        $table = Engine_Api::_()->getItemTable('sitebooking_ser');

        $service_overview_table = Engine_Api::_()->getDbtable('serviceoverviews', 'sitebooking');

        // Fetch the user, all services
        $result = $table->fetchAll($table->select()
          ->where('owner_id = ?', $viewer->user_id));
        $count = count($result);
        
        // If not post or form not valid, return
        if($this->getRequest()->isPost()) {

            if(!empty($quota) && ($count > $quota))
                $this->respondWithError("unauthorized");
            // Process
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $formValues = array();
                $values = $data = $_REQUEST;
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceForm();
                foreach ($getForm['form'] as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $formValues[$element['name']] = $_REQUEST[$element['name']];
                }
                // Start form validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'Sitebooking')->getServiceFormValidators();
                $formValues['validators'] = $validators;
                $validationMessage = $this->isValid($formValues);

                $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
                if(!empty($formValues['slug']))
                    $services = $serviceTable->fetchRow($serviceTable->select()->where('slug LIKE ?',$formValues['slug']));

                if(!empty($services)){
                    $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                    $validationMessage['slug'] = $this->translate('This URL is already taken, please try another');
                }

                if($formValues['category_id'] == "-1"){
                    $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                    $validationMessage['category'] = $this->translate('Please select category.');
                }

                // Form validation
                if (isset($values['category_id']) && empty($formValues['category_id']))
                    unset($values['category_id']);

                if (!@is_array($validationMessage) && isset($formValues['category_id'])) {
                    $categoryIds = array();
                    $categoryIds[] = $values['category_id'];
                    if (isset($values['first_level_category_id']) && !empty($values['first_level_category_id'])) {
                        $categoryIds[] = $formValues['first_level_category_id'] = $values['first_level_category_id'];
                    }
                    if (isset($values['second_level_category_id']) && !empty($values['second_level_category_id'])) {
                        $categoryIds[] = $formValues['second_level_category_id'] = $values['second_level_category_id'];
                    }

                    try {
                        $formValues['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryIds, 'profile_type');
                    } catch (Exception $ex) {

                    }

                    // Profile fields validation
                    if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                        // START FORM VALIDATION
                        $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitebooking')->getFieldsFormValidations($values);
                        $values['validators'] = $profileFieldsValidators;
                        $profileFieldsValidationMessage = $this->isValid($values);
                    }
                }

                if (is_array($validationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($validationMessage, $profileFieldsValidationMessage);

                else if (is_array($validationMessage))
                    $validationMessage = $validationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
                // End Form Validation
                if(!empty($formValues['description_service']))
                    $formValues['description'] = $formValues['description_service'];

                $row = $table->createRow();
                $row->setFromArray($formValues);
            
                //FOR ACTIVITY FEED
                $row->parent_id =  $this->_getParam('pro_id');
                $row->owner_id = $viewer->getIdentity();
                $row->parent_type = $provider->getType();
                $row->approved = $approved;


                if( !empty($_FILES['photo']) ) {
                    $row->setPhoto($_FILES['photo'], 1);
                }
                $row->save();

                $categoryIds = array();
                $categoryIds[] = $row->category_id;
                $categoryIds[] = $row->first_level_category_id;
                $categoryIds[] = $row->second_level_category_id;
                try {
                    $profile_type = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryIds, 0, 'profile_type');
                } catch (Exception $ex) {
                    //Blank Exception
                }

                // Profile fields saving
                $row->profile_type = (isset($profile_type) ? $profile_type : 0);
                $profileTypeField = null;
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                }

                if ($profileTypeField) {
                    $profileTypeValue = $row->profile_type;

                    if ($profileTypeValue) {
                        $profileValues = Engine_Api::_()->fields()->getFieldsValues($row);

                        $valueRow = $profileValues->createRow();
                        $valueRow->field_id = $profileTypeField->field_id;
                        $valueRow->item_id = $row->getIdentity();
                        $valueRow->value = $profileTypeValue;
                        $valueRow->save();
                    } else {
                        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');
                        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                            $profileTypeField = $topStructure[0]->getChild();
                            $options = $profileTypeField->getOptions();
                            if (count($options) == 1) {
                                $profileValues = Engine_Api::_()->fields()->getFieldsValues($row);
                                $valueRow = $profileValues->createRow();
                                $valueRow->field_id = $profileTypeField->field_id;
                                $valueRow->item_id = $row->getIdentity();
                                $valueRow->value = $options[0]->option_id;
                                $valueRow->save();
                            }
                        }
                    }
                    // Save the profile fields information.
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->setProfileFields($row, $data);
                }

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

                // Add tags
                $tags = preg_split('/[,]+/', $formValues['tags']);
                $row->tags()->addTagMaps($viewer, $tags);
            
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

            }  catch( Exception $e ) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }   
            $this->setRequestMethod();
            $this->_forward('view', 'service', 'sitebooking', array(
                'ser_id' => $row->getIdentity(),
            ));
        }
    }

    public function viewAction()
    { 
        $viewer = Engine_Api::_()->user()->getViewer();

        $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));
        $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');

        if( !$service ) {
            $this->respondWithError("unauthorized");
        }
        $provider = Engine_Api::_()->getItem($service->parent_type, $service->parent_id);

        // Check permission
        if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'view')->isValid() ) {
            $this->respondWithError("unauthorized");
        }

        $bodyParams['response'] = $service->toArray();
        $bodyParams['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
        $bodyParams['response']['description_service'] = $bodyParams['response']['description'];
        $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $service->getIdentity());
        if(!empty($favourite_id_temp)){
            $bodyParams['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
        }
        else
            $bodyParams['response']['favourite_id'] = 0;

        $bodyParams['response']['rating'] = ceil($bodyParams['response']['rating']);

        // Getting viewer like or not to content.
        $bodyParams['response'] ["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($service);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
        $bodyParams['response']["owner_title"] = !empty($service->getOwner()->getTitle()) ? $service->getOwner()->getTitle() : "" ;
        $bodyParams['response']["provider_name"] = !empty($provider->getTitle())? $provider->getTitle() : "";
        $serviceCat = Engine_Api::_()->getItem('sitebooking_category', $service->category_id);
        $bodyParams['response']['category_title'] = $serviceCat->category_name;
        $bodyParams['response']['duration_period'] = Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($service->duration); 

        if ($this->getRequestParam('profile_fields', true)) {
            $tempProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getInformation($service,true);

            if (isset($tempProfileFields) && !empty($tempProfileFields)) {
                $bodyParams['response']['profile_fields'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getInformation($service, true);

                if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order']) && $_REQUEST['field_order'] == 1) {

                    $bodyParams['response']['profile_fields'] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($bodyParams['response']['profile_fields']);
                }
            }
        } 
        // service overview
        $serviceOverviewTable = Engine_Api::_()->getDbTable('serviceoverviews','sitebooking');
        $item = $serviceOverviewTable->fetchRow($serviceOverviewTable->select()->where('ser_id = ?',$this->_getParam('ser_id')));
        $bodyParams['response']['longDescription'] = !empty($item->longDescription) ? $item->longDescription : "" ;

        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($service);

        if ($this->getRequestParam('tabs_menu', true))
            $bodyParams['profile_tabs'] = $this->_tabsMenus($service);

        $this->respondWithSuccess($bodyParams);  
    }

    public function _gutterMenus($subject) {
        $getParentHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseParentUrl = @trim($baseParentUrl, "/");
        $getHost = $getParentHost. DIRECTORY_SEPARATOR. $baseParentUrl;
        $viewer = Engine_Api::_()->user()->getViewer();
        if($subject->owner_id == $viewer->getIdentity()){
            if($this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'edit')->isValid())
                $menus[] = array(
                        'name' => 'edit',
                        'label' => $this->translate('Edit Service'),
                        'url' => 'service/edit/' . $subject->getIdentity()
                    );

            if($this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'delete')->isValid())
                $menus[] = array(
                        'name' => 'delete',
                        'label' => $this->translate('Delete Service'),
                        'url' => 'service/delete/' . $subject->getIdentity()
                    );

            $menus[] = array(
                'name' => 'contact_details',
                'label' => $this->translate('Contact Details'),
                'url' => $getHost . '/bookings/providers/contact/' . $subject->parent_id,
                'urlParams' => array(
                    "resource_type" => $subject->getType(),
                    "resource_id" => $subject->getIdentity()
                )
            );

        }

        // Share Page
        if (!empty($viewer->getIdentity())) {
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.sharelink",'yes') == yes) {
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

            if (($viewer->getIdentity() != $subject->owner_id)) {
                if(Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.report",'yes') == yes){
                    $menus[] = array(
                        'name' => 'report',
                        'label' => $this->translate('Report Service'),
                        'url' => 'report/create/subject/' . $subject->getGuid(),
                        'urlParams' => array(
                            "type" => $subject->getType(),
                            "id" => $subject->getIdentity()
                        )
                    );
                }
            }

            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $subject->getIdentity());
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
                    'url' => 'service/tellafriend/' . $subject->getIdentity()
                );

            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview') != 'service_none') {

                $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

                $review_row = $table->fetchRow($table->select()->where('resource_id = ?', $subject->getIdentity())
                    ->where('resource_type = ?', $subject->getType())
                    ->where('user_id = ?', $viewer->getIdentity()));

                $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
                $rated = $ratingTable->checkRated($subject->getIdentity(), $viewer->getIdentity());

                $rating_row = $ratingTable->getMyRating($subject->getIdentity(), $viewer->getIdentity());

                $serviceReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview');
                if($serviceReview == 'service_onlyRating') { 

                    if(!empty($rating_row->toArray())){

                    }
                    else
                    {
                        $menus[] = array(
                            'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview'),
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
                else
                {
                    if(!empty($review_row)){
                        $menus[] = array(
                            'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview'),
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
                            'review' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview'),
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

        $servicebookingTable = Engine_Api::_()->getDbTable('servicebookings','sitebooking');
        $scheduleRow = $servicebookingTable->fetchRow($servicebookingTable->select()->where('ser_id = ?',$subject->ser_id)->where('user_id = ?',$viewer->getIdentity()));

        $servicescheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
        $servicescheduleRow = $servicescheduleTable->fetchRow($servicescheduleTable->select()->where('ser_id = ?',$subject->ser_id));

        if(!empty($servicescheduleRow) && ($viewer->getIdentity() != $subject->owner_id) )
            $menus[] = array(
                    'name' => 'book_me',
                    'label' => $this->translate('Book Me'),
                    'url' => $getHost . '/bookings/service/book-service/' . $subject->getIdentity()
                );

        return $menus;
    }

    public function _tabsMenus($subject) {
        $tabsMenu = array();

        $tabsMenu[] = array(
            'name' => 'information',
            'label' => $this->translate('Info'),
            'url' => 'service/information/' . $subject->getIdentity()
        );
        $serviceOverviewTable = Engine_Api::_()->getDbTable('serviceoverviews','sitebooking');
        $item = $serviceOverviewTable->fetchRow($serviceOverviewTable->select()->where('ser_id = ?',$subject->getIdentity()));
        if(!empty($item))
            $tabsMenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'service/overview/' . $subject->getIdentity()
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
                        'resource_type' => 'sitebooking_ser',
                        'resource_id' => $subject->getIdentity(),
                    ),
        );
        return $tabsMenu;        
    }

    public function informationAction(){
        $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));
        $bodyParams['response'] = $service->toArray();

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
        $bodyParams['response']["owner_title"] = $service->getOwner()->getTitle();
        $bodyParams['response']["profile_fields"] = !empty(Engine_Api::_()->getApi('Siteapi_Core','sitebooking')->getSubjectFieldsInfo($service, array('noHeading' => true, 'category' => 'specific'))) ? Engine_Api::_()->getApi('Siteapi_Core','sitebooking')->getSubjectFieldsInfo($service, array('noHeading' => true, 'category' => 'general')) : "";

        $this->respondWithSuccess($bodyParams);
    }

    public function overviewAction(){
        $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!$service) {
          $this->respondWithError("unauthorized");
        }
        $bodyParams = array();
        $ser_id = $service->getIdentity();
        $serviceOverviewTable = Engine_Api::_()->getDbTable('serviceoverviews','sitebooking');
        $item = $serviceOverviewTable->fetchRow($serviceOverviewTable->select()->where('ser_id = ?',$ser_id));

        $bodyParams = $item->toArray();
        if(!empty($bodyParams)){
            $response['response'] = $bodyParams;
        }
        else
            $response['response'] = "";
        $this->respondWithSuccess($response);
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
            $ser_id = $this->_getParam('ser_id', $this->_getParam('ser_id', null));
            $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
            if (empty($service))
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
            $heading = ucfirst($service->getTitle());

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
            $slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.servicesingular', 'service');
            $objectLink = "/" . $slug_singular . '/view/' . $ser_id;
            $sender = $values['sender_name'];
            $message = $values['message'];
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SERVICE_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender_name' => $sender,
                    'service_title' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . $service->getHref(),
                    'sender_email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    public function deleteAction()
    {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        if( !$this->_helper->requireUser()->isValid() )
            $this->respondWithError('unauthorized');

        if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));

        if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'edit')->isValid() ) $this->respondWithError('unauthorized');

        $db = $service->getTable()->getAdapter();

        $db->beginTransaction();

        try {
            $service->delete();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch( Exception $e ) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function editAction()
    { 
        if( !$this->_helper->requireUser()->isValid() )
            $this->respondWithError('unauthorized');

        if( !$this->_helper->requireAuth()->setAuthParams('sitebooking_ser', null, 'edit')->isValid())$this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $pro_id = $this->_getParam('pro_id');
        $provider = Engine_Api::_()->getItem('sitebooking_pro',$pro_id);
    
        $ser_id = $this->_getParam('ser_id');

        $service = Engine_Api::_()->getItem('sitebooking_ser', $this->_getParam('ser_id'));
        $service_overview_table = Engine_Api::_()->getDbtable('serviceoverviews', 'sitebooking');
        $service_overview_row = $service_overview_table->fetchRow($service_overview_table->select()->where('ser_id = ?', $this->_getParam('ser_id')));

        $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitebooking')->defaultProfileId();
        $categoryIds = array();
        $categoryIds[] = $service->category_id;
        $categoryIds[] = $service->first_level_category_id;
        $categoryIds[] = $service->second_level_category_id;
        try{
            $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitebooking')->getProfileType($categoryIds, 0, 'profile_type');
        } catch (Exception $ex) {
            $previous_profile_type = $defaultProfileId;
        }

        if( !$this->_helper->requireAuth()->setAuthParams($service, $viewer, 'edit')->isValid() )
            $this->respondWithError('unauthorized');
        if ($this->getRequest()->isGet()) {  
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceForm($service,1);

            $form_fields['formValues'] = $service->toArray();
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($service);
            $form_fields['formValues'] = array_merge($form_fields['formValues'], $getContentImages);
            if ($this->getRequestParam('profile_fields', true)) {
                $tempProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getInformation($service,true);

                if (isset($tempProfileFields) && !empty($tempProfileFields)) {
                    $form_fields['formValues'] = array_merge($form_fields['formValues'],Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getInformation($service, true));
                }
            } 

            $hrs = explode('.',($service->duration / 3600));
            $form_fields['formValues']['hrs'] = $hrs[0];
            $mins = explode('.',((($service->duration / 60)) % 60));
            $form_fields['formValues']['mins'] = $mins[0];
            if(!empty($form_fields['formValues']['description']))
                $form_fields['formValues']['description_service'] = $form_fields['formValues']['description'];
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite('sitebooking_ser', $service->getIdentity());
            if(!empty($favourite_id_temp)){
                $form_fields['formValues']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
            }
            else
                $form_fields['formValues']['favourite_id'] = 0;

            $form_fields['formValues']['overview'] = $service_overview_row->toArray();

            $tagStr = '';
            foreach( $service->tags()->getTagMaps() as $tagMap ) {

              $tag = $tagMap->getTag();
              if( !isset($tag->text) ) continue;
              if( '' !== $tagStr ) $tagStr .= ', ';
              $tagStr .= $tag->text;
            }
            $form_fields['formValues']['tags'] = $tagStr;
            $this->respondWithSuccess($form_fields);
        }  

        $prevStatus = $service->status;

        // Check post/form
        if( $this->getRequest()->isPost() ) {
            // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $formValues = array();
                $data = $_REQUEST;
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->getServiceForm($service,1);
                foreach ($getForm['form'] as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $formValues[$element['name']] = $_REQUEST[$element['name']];
                }
                $values = $formValues;
                // Start form validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'Sitebooking')->getServiceFormValidators($formValues,1);
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);
                // End Form Validation

                $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
               
                if($formValues['category_id'] == "-1"){
                    $validationMessage = (is_array($validationMessage)) ? $validationMessage : array();
                    $validationMessage['category'] = $this->translate('Please select category.');
                }

                if (!@is_array($validationMessage) && isset($values['category_id'])) {

                    $categoryIds = array();
                    $categoryIds[] = $values['category_id'];
                    $categoryIds[] = $values['subcategory_id'];
                    $categoryIds[] = $values['subsubcategory_id'];

                    //@todo profile field work
                    try {
                        $values['profile_type'] = $data['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryIds, 0, 'profile_type');
                    } catch (Exception $ex) {
                        
                    }

                    // Start profile fields validations
                    if (isset($data['profile_type']) && !empty($data['profile_type'])) {
                        $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitebooking')->getFieldsFormValidations($values, $values['profile_type']);
                        $data['validators'] = $profileFieldsValidators;
                        $profileFieldsValidationMessage = $this->isValid($data);
                    }
                }
                if (is_array($validationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($validationMessage, $profileFieldsValidationMessage);

                else if (is_array($validationMessage))
                    $validationMessage = $validationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
                if(!empty($values['description_service']))
                    $values['description'] = $values['description_service'];
                
                $service->setFromArray($values);

                $service_overview_row->toArray()['longDescription'] = $values['longDescription'];

                $service->modified_date = date('Y-m-d H:i:s');
              
                // Add photo
                if( !empty($_FILES['photo']) ) {
                    $service->setPhoto($_FILES['photo'], 1);
                }

                // Save profile fields
                if (isset($values['category_id']) && !empty($values['category_id'])) {
                    if(!isset($data['first_level_category_id']) || empty($data['first_level_category_id']))
                        $data['first_level_category_id'] = 0;

                    if(!isset($data['second_level_category_id']) || empty($data['second_level_category_id']))
                        $data['second_level_category_id'] = 0;


                    $categoryIds = array();
                    $categoryIds[] = $service->category_id;
                    $categoryIds[] = $service->first_level_category_id;
                    $categoryIds[] = $service->second_level_category_id;
                    try {
                        $profile_type = $service->profile_type = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryIds, 'profile_type');
                    } catch (Exception $ex) {
                   
                    }
                    $service->first_level_category_id = $data['first_level_category_id'];
                    $service->second_level_category_id = $data['second_level_category_id'];
                    $service->save();

                    if ($service->profile_type != $previous_profile_type) {

                        $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values');
                        
                        $fieldvalueTable->delete(array('item_id = ?' => $service->getIdentity()));

                        Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search')->delete(array(
                            'item_id = ?' => $service->getIdentity(),
                        ));

                        if (!empty($service->profile_type) && !empty($previous_profile_type)) {
                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $service->getIdentity(),
                                'field_id' => $defaultProfileId,
                                'index' => 0,
                                'value' => $service->profile_type,
                            ));
                        }
                    }

                    // Save the profile fields information.
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitebooking')->setProfileFields($service, $data);
                }

                // send notification if status changed to published and auto-approving is on
                $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_ser', $viewer, 'approve');
                if($autoApprove == 1 && $values['status'] == 1 && $prevStatus == 0){
                    // Send mail and notifications to provider
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($viewer, $viewer, $service, 'sitebooking_service_approved');
                    Engine_Api::_()->sitebooking()->sendServiceAutoapproveMail($viewer,$service);
                }

                // Add activity only if Service is published
                if( $values['status'] == 1 && $prevStatus == 0 ) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $service, 'service_new');

                    // make sure action exists before attaching the Service to the activity
                    if( $action ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $service);
                    }
                }
                $service->save();
      
                $categoryId = 0;
                if(!empty($service->category_id))
                    $categoryId = $service->category_id;

                $service->profile_type = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getProfileType($categoryId, 'profile_type');

                $service->save();

                //Updating serviceoverview table  
                $service_overview_row->longDescription = $formValues['longDescription'];
                $service_overview_row->save();

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner_network', 'registered', 'everyone');

                $viewMax = array_search($_REQUESTT['view'], $roles);

                foreach( $roles as $i => $role ) {
                    $auth->setAllowed($service, $role, 'view', ($i <= $viewMax));
                }

                $commentMax = array_search($_REQUESTT['comment'], $roles);

                foreach( $roles as $i => $role ) {
                    $auth->setAllowed($service, $role, 'comment', ($i <= $commentMax));
                }

                //handle tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $service->tags()->setTagMaps($viewer, $tags);

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            }
            catch( Exception $e ) { 
              $db->rollBack();
              $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function uploadProfilePhotoAction()
    {
        try{
            $service_id = $this->_getParam('ser_id', 0);

            if (empty($service_id))
                $this->respondWithValidationError("parameter_missing", "service_id");

            $service = Engine_Api::_()->getItem('sitebooking_ser',$service_id);
            $viewer = Engine_Api::_()->user()->getViewer();

            if (!$this->_helper->requireUser()->isValid())
                $this->respondWithError('unauthorized');

            if( !$service->isOwner($viewer) )
                $this->respondWithError('unauthorized');

            if (empty($_FILES['photo']))
                $this->respondWithError('unauthorized','No file');
  
            $service->setPhoto($_FILES['photo'], 1);

            $this->successResponseNoContent('no_content', true);
        }
        catch(Exception $e){
            $this->respondWithValidationError('internal_server_error', $e);
        }
    }
        
}