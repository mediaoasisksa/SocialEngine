<?php

class Sitebooking_AdminServiceManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	$this->view->form = $form = new Sitebooking_Form_Admin_ServiceManage_Filter();
    
    //Navigation Bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_service_manage');

  	$this->view->search = 0;
    $this->view->provider = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->newlabel = '';
    $this->view->approved = '';
    $this->view->featured = '';
    $this->view->Hot = '';
    $this->view->status = '';
    $this->view->servicebrowse = '';
    $this->view->category_id = 0;
    $this->view->subcategory_id = 0;
    $this->view->subsubcategory_id = 0;

    $tableProviderName = Engine_Api::_()->getItemTable('sitebooking_pro')->info('name');

    $tableService = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceTableName = $tableService->info('name');

    //MAKE QUERY
    $select = $tableService->select()
              ->order($serviceTableName . '.creation_date DESC')
              ->setIntegrityCheck(false)
              ->from($serviceTableName)
              ->join($tableProviderName, "$serviceTableName.parent_id = $tableProviderName.pro_id",array('title as provider_title', "pro_id"))
              ->group("$serviceTableName.ser_id");

    if(isset($_GET['search'])){
      if (!empty($_GET['title'])) {
        $this->view->title = $_GET['title'];
        $select->where($serviceTableName . '.title  LIKE ?', '%' . $_GET['title'] . '%');
      }

      if (!empty($_GET['provider'])) {
        $this->view->provider = $_GET['provider'];
        $select->where($tableProviderName . '.title  LIKE ?', '%' . $_GET['provider'] . '%');
      }

      if (!empty($_GET['category_id']) && $_GET['category_id'] != -1) { 

        $this->view->category_id = $_GET['category_id'];
        $select->where($serviceTableName . '.category_id  = ?', $_GET['category_id']);
      }

      if (!empty($_GET['category_id']) && !empty($_GET['first_level_category_id']) && $_GET['category_id'] != -1 && $_GET['first_level_category_id'] != -1) {

        $this->view->subcategory_id = $_GET['first_level_category_id'];
        $select->where($serviceTableName . '.first_level_category_id  = ?', $_GET['first_level_category_id']);
      }

      if (!empty($_GET['category_id']) && !empty($_GET['first_level_category_id']) && !empty($_GET['second_level_category_id']) && $_GET['category_id'] != -1 && $_GET['first_level_category_id'] != -1 && $_GET['second_level_category_id'] != -1) { 

        $this->view->subsubcategory_id = $_GET['second_level_category_id'];
        $select->where($serviceTableName . '.second_level_category_id  = ?', $_GET['second_level_category_id']);
        echo $sql;
      }


      if (!empty($_GET['sponsored'])) {
          $this->view->sponsored = $_GET['sponsored'];
          $_GET['sponsored']--;
          $select->where($serviceTableName . '.sponsored = ? ', $_GET['sponsored']);
      }


      if (!empty($_GET['approved'])) {
          $this->view->approved = $_GET['approved'];
          $_GET['approved']--;
          $select->where($serviceTableName . '.approved = ? ', $_GET['approved']);
      }

      if (!empty($_GET['featured'])) {
          $this->view->featured = $_GET['featured'];
          $_GET['featured']--;
          $select->where($serviceTableName . '.featured = ? ', $_GET['featured']);
      }

      if (!empty($_GET['newlabel'])) {
          $this->view->newlabel = $_GET['newlabel'];
          $_GET['newlabel']--;
          $select->where($serviceTableName . '.newlabel = ? ', $_GET['newlabel']);
      }

      if (!empty($_GET['Hot'])) {
          $this->view->Hot = $_GET['Hot'];
          $_GET['Hot']--;
          $select->where($serviceTableName . '.Hot = ? ', $_GET['Hot']);
      }

      if (!empty($_GET['status'])) {
          $this->view->status = $_GET['status'];
          $_GET['status']--;
          $select->where($serviceTableName . '.status = ? ', $_GET['status']);
      }

      if (!empty($_GET['servicebrowse'])) {
          $this->view->eventbrowse = $_GET['servicebrowse'];
          $_GET['servicebrowse']--;
          if ($_GET['servicebrowse'] == 0) {
              $select->order($serviceTableName . '.view_count DESC');
          } else {
              $select->order($serviceTableName . '.ser_id DESC');
          }
      }
      $this->view->search = 1;

    }          
    $this->view->formValues = array_filter($_GET);

    $this->view->paginator = Zend_Paginator::factory($select);
    $items_per_page = 10;
    $this->view->paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  
  function multiDeleteAction()
  {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $service = Engine_Api::_()->getItem('sitebooking_ser', $value);

          $service->delete();

        }        
      }      
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->ser_id=$id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $service = Engine_Api::_()->getItem('sitebooking_ser', $id);
        $service->delete();
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'closeSmoothbox' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This service has been deleted successfully.')),
          'format' => 'smoothbox'
      ));
    }
  }


  function approvedAction()
  {
    $ser_id = $this->_getParam('ser_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $owner = Engine_Api::_()->getItem('user', $service->owner_id);
    try {
      if($service->approved == "1"){

        $service->approved = "0";

        if(!empty($ser_id)) {
          $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
          $bookingTableName = $bookingTable->info('name');

          //rejecting this service
          $select = $bookingTable->select();
          $sql = $select->where($bookingTableName . ".ser_id = ?", $ser_id)
                 ->where($bookingTableName . ".status = 'booked' OR $bookingTableName.status = 'pending'");

          $bookingData = $bookingTable->fetchAll($sql);

          foreach( $bookingData as $item ) {
            $item->status = "rejected";
            $item->save();
          }
        }

      // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $service, 'sitebooking_service_disapproved');
        Engine_Api::_()->sitebooking()->sendServiceDisapproveMail($owner,$service,$viewer);

      }
      else{
        $service->approved = "1";

        // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $service, 'sitebooking_service_approved');
        Engine_Api::_()->sitebooking()->sendServiceApproveMail($owner,$service,$viewer);
      }
      $service->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/service-manage');
  }

  function featuredAction()
  {
    $ser_id = $this->_getParam('ser_id');      
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
      if($service->featured == "1"){
        $service->featured = "0";

      }
      else{
        $service->featured = "1";
      }
      $service->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/service-manage');
  }

  function sponsoredAction()
  {
    $ser_id = $this->_getParam('ser_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
      if($service->sponsored == "1"){
        $service->sponsored = "0";

      }
      else{
        $service->sponsored = "1";
      }
      $service->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/service-manage');
  }

  function newlabelAction()
  {
    $ser_id = $this->_getParam('ser_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
      if($service->newlabel == "1"){
        $service->newlabel = "0";

      }
      else{
        $service->newlabel = "1";
      }
      $service->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/service-manage');
  }

  function hotAction()
  {
    $ser_id = $this->_getParam('ser_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $service = Engine_Api::_()->getItem('sitebooking_ser', $ser_id);
      if($service->hot == "1"){
        $service->hot = "0";

      }
      else{
        $service->hot = "1";
      }
      $service->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/service-manage');
  }
}