<?php

class Sitebooking_Plugin_Menus
{
  public function canCreateServices()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() ) {
      return false;

    }

    // Must be able to create services
    if( !Engine_Api::_()->authorization()->isAllowed('sitebooking_ser', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewServices()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view Services
    if( !Engine_Api::_()->authorization()->isAllowed('sitebooking_ser', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function canMakeAnAppointment(){
    $viewer = Engine_Api::_()->user()->getViewer();
        
      if( !$viewer->getIdentity() ) {
        return false;
      }

    return true;
  }

  public function canViewWishlist(){
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
        return false;
      }
    return true;
  }



  public function sitebookingServiceGutterEdit($row) 
  {
    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
        return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT SUBJECT
    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');

    //AUTHORIZATION CHECK
    if (!$service->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }
    return array(
      'class' => 'buttonlink icon_service_edit',
      'route' => "sitebooking_service_specific",
      'action' => 'edit',
      'params' => array(
        'pro_id' => $service->parent_id,
        'ser_id' => $service->getIdentity(),
      ),
    );
  }

  public function sitebookingServiceGutterDelete($row) 
  {
    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
        return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT SUBJECT
    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');

    //AUTHORIZATION CHECK
    if (!$service->authorization()->isAllowed($viewer, "delete")) {
        return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_service_delete',
        'route' => "sitebooking_service_specific",
        'action' => 'delete',
        'params' => array(
        'ser_id' => $service->getIdentity(),
        'pro_id' => $service->parent_id,
        ),
    );
  }

  // onMenuInitialize_SitebookingServiceGutterShare
  public function sitebookingServiceGutterShare($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_ser') ) {
      return false;
    }

    // Admin level setting
    $sharelinkcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.sharelink");

    if($sharelinkcoreSettings === "no")
      return false;
    
    $subject = Engine_Api::_()->core()->getSubject();
    if( !($subject instanceof Sitebooking_Model_Ser) ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['type'] = $subject->getType();
    $params['params']['id'] = $subject->getIdentity();
    $params['params']['format'] = 'smoothbox';
    return $params;
  }

  public function sitebookingServiceGutterReport($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    // Admin level setting
    $reportcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.report");

    if($reportcoreSettings === "no")
      return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( ($subject instanceof Sitebooking_Model_Ser) &&
        $subject->owner_id == $viewer->getIdentity() ) {
      return false;
    } else if( $subject instanceof User_Model_User &&
        $subject->getIdentity() == $viewer->getIdentity() ) {
      return false;
    }

    // Modify params
    $subject = Engine_Api::_()->core()->getSubject();
    $params = $row->params;
    $params['params']['subject'] = $subject->getGuid();
    return $params;
  }

  function sitebookingServiceGutterTellafriend($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_ser') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();
    if( !($subject instanceof Sitebooking_Model_Ser) ) {
      return false;
    }

    return array(
            'class' => 'buttonlink smoothbox icon_sitebooking_tellafriend',
            'route' => "sitebooking_service_specific",
            'action' => 'tell-a-friend',
            'params' => array(
            'ser_id' => $subject->getIdentity(),
            'pro_id' => $subject->parent_id,
            ),
        );
  }

  public function canCreateServiceproviders()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create service providers
    if( !Engine_Api::_()->authorization()->isAllowed('sitebooking_pro', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canManageServiceproviders()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create service providers
    if( !Engine_Api::_()->authorization()->isAllowed('sitebooking_pro', $viewer, 'create') ) {
      return false;
    }

    $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $tableProvider->info('name');

    $select = $tableProvider->select();
    $sql = $select->where($providerTableName . '.owner_id = ? ', $viewer['user_id']);
    $count = $tableProvider->fetchAll($sql); 
    if( count($count) <= 0 ) {
      return false;
    }

    return true;
  }

  public function canViewServiceProviders()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create service providers
    if( !Engine_Api::_()->authorization()->isAllowed('sitebooking_pro', $viewer, 'view') ) {
      return false;
    }
    // }

    return true;
  }

  public function sitebookingProviderGutterEdit($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
        return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT SUBJECT
    $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');

    //AUTHORIZATION CHECK
    if (!$provider->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }
    return array(
        'class' => 'buttonlink icon_provider_edit',
        'route' => "sitebooking_provider_specific",
        'action' => 'edit',
        'params' => array(
            'pro_id' => $provider->getIdentity(),
        ),
    );
  }

  public function sitebookingProviderGutterDisable($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
        return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT SUBJECT
    $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');

    //AUTHORIZATION CHECK
    if (!$provider->authorization()->isAllowed($viewer, "delete")) {
        return false;
    }
    
    // $params = $row->params;
    if( $provider->enabled == 1 ) {
      return array(
      'label' => 'Disable',
      'format' => 'smoothbox',
      'class' =>'buttonlink smoothbox icon_provider_delete',
      'route' => "sitebooking_provider_specific",
      'action' => 'disable',
      'params' => array(
        'pro_id' => $provider->getIdentity()
        )
    );
    } 
    else {
    return array(
      'label' => 'Enable',
      'format' => 'smoothbox',
      'class' =>'buttonlink smoothbox icon_provider_delete',
      'route' => "sitebooking_provider_specific",
      'action' => 'enable',
      'params' => array(
        'pro_id' => $provider->getIdentity()
        )
    );
    }
  }

  public function sitebookingProviderGutterShare($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }

    // Admin level setting
    $sharelinkcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.sharelink");

    if($sharelinkcoreSettings === "no")
      return false;
    
    $subject = Engine_Api::_()->core()->getSubject();
    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['type'] = $subject->getType();
    $params['params']['id'] = $subject->getIdentity();
    $params['params']['format'] = 'smoothbox';
    return $params;
  }

  public function sitebookingProviderGutterReport($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    // Admin level setting
    $reportcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.report");

    if($reportcoreSettings === "no")
      return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( ($subject instanceof Sitebooking_Model_Pro) &&
        $subject->owner_id == $viewer->getIdentity() ) {
      return false;
    } else if( $subject instanceof User_Model_User &&
        $subject->getIdentity() == $viewer->getIdentity() ) {
      return false;
    }

    // Modify params
    $subject = Engine_Api::_()->core()->getSubject();
    $params = $row->params;
    $params['params']['subject'] = $subject->getGuid();
    return $params;
  }

  function sitebookingProviderGutterTellafriend($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();
    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'class' => 'buttonlink smoothbox icon_sitebooking_tellafriend',
            'route' => "sitebooking_provider_specific",
            'action' => 'tell-a-friend',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            ),
        );
  }

  function sitebookingDashboardProviderEdit($row){

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }
    
    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'label' => $row->label,
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_provider_specific",
            'name' => 'sitebooking_dashboard_provider_edit',
            'action' => 'edit',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            ),
        );
  }

  function sitebookingDashboardServiceCreate($row){

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "create")) {
        return false;
    }

    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_service_create',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_service_general",
            'action' => 'create',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            ),
        );
    
  }

  function sitebookingDashboardManageService($row){

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }

  

    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }
///parent_parent_type/classroom/parent_parent_id/51
    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_service_manage',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_service_general",
            'action' => 'service-manage',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            'parent_parent_type'=>Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_type', null),
            'parent_parent_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_id', null),
            ),
        );
    
  }

  function sitebookingDashboardProviderOverview($row){

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }

    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_provider_overview',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_provider_specific",
            'action' => 'overview',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            ),
        );
    
  }

  function sitebookingDashboardProviderBooking($row){

    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();
    
    $provider = Engine_Api::_()->getItem('sitebooking_pro', $subject->getIdentity());
   
    //AUTHORIZATION CHECK
    if($provider->owner_id != $viewer->user_id) {
      return false;
    }

    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_provider_booking',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_provider_specific",
            'action' => 'booked',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            'parent_parent_type'=>Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_type', null),
            'parent_parent_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_id', null),
            ),
        );
    
  }

  function sitebookingDashboardProviderAvailable($row){

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }

    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }
    
    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_service_available',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_provider_specific",
            'action' => 'available',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            'parent_parent_type'=>Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_type', null),
            'parent_parent_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_parent_id', null),
            ),
        );
    
  }

  function sitebookingDashboardProviderContact($row){
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return false;
    }
    
    $subject = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$subject->authorization()->isAllowed($viewer, "edit")) {
        return false;
    }
    
    if( !($subject instanceof Sitebooking_Model_Pro) ) {
      return false;
    }

    return array(
            'label' => $row->label,
            'name' => 'sitebooking_dashboard_service_contact',
            'class' => 'ajax_dashboard_enabled',
            'route' => "sitebooking_provider_specific",
            'action' => 'contact',
            'params' => array(
            'pro_id' => $subject->getIdentity(),
            ),
        );
  }

}

?>