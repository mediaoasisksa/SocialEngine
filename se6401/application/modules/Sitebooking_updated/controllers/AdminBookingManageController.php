<?php
class Sitebooking_AdminBookingManageController extends Core_Controller_Action_Admin
{
  function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_booking_manage');
    
    $this->view->search = 0;
    $this->view->user = '';
    $this->view->service = '';
    $this->view->provider = '';
    $this->view->bookingdate = '';
    $this->view->servicingdate = '';
    $this->view->status = '';

    $this->view->timezone = Engine_Api::_()->user()->getViewer()->timezone;

    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $tableBooking = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $tableBooking->info('name');

    $tableService = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceTableName = $tableService->info('name');

    $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $tableProvider->info('name');

    //MAKE QUERY
    $select = $tableBooking->select()
              ->order($bookingTableName . '.booking_date DESC')
              ->setIntegrityCheck(false)
              ->from($bookingTableName)
              ->joinLeft($tableUserName, "$bookingTableName.user_id = $tableUserName.user_id", 'username')
              ->joinLeft($serviceTableName, "$bookingTableName.ser_id = $serviceTableName.ser_id", array('title as service_title','slug as service_slug'))
              ->joinLeft($providerTableName, "$bookingTableName.pro_id = $providerTableName.pro_id", array('title as provider_title','slug as provider_slug'))
              ->group("$bookingTableName.servicebooking_id");


    if(isset($_POST['search'])){

      if (!empty($_POST['bookingdate']) && !empty($_POST['bookingdate']['date'])) {
          $values['bookingdate'] = $_POST['bookingdate']['date'];
      } else {
          $values['bookingdate'] = '';
      }

      if (!empty($_POST['servicingdate']) && !empty($_POST['servicingdate']['date'])) {
          $values['servicingdate'] = $_POST['servicingdate']['date'];
      } else {
          $values['servicingdate'] = '';
      }

      $this->view->bookingdate = $values['bookingdate'];
      $this->view->servicingdate = $values['servicingdate'];    

      if (!empty($_POST['user'])) {
        $user = $this->view->user = $_POST['user'];
        $select->where("$tableUserName.username  LIKE '%$user%' OR $tableUserName.displayname  LIKE '%$user%'");
      }

     if (!empty($_POST['service'])) {
        $serviceTitle = $this->view->service = $_POST['service'];
        $select->where("$serviceTableName.title LIKE '%$serviceTitle%'");
      }

      if (!empty($_POST['provider'])) {
        $providerTitle = $this->view->provider = $_POST['provider'];
        $select->where("$providerTableName.title LIKE '%$providerTitle%'");
      }

      if (!empty($values['bookingdate'])) {
        $bookingdate = $this->view->bookingdate = $values['bookingdate'];
        $bookingdate = str_replace("/","-",$bookingdate);
        $date1 = strtotime($bookingdate);
        $bookingdate = date('Y-m-d', $date1);
        $select->where("$bookingTableName.booking_date LIKE '%$bookingdate%'");
      }

      if (!empty($values['servicingdate'])) {
        $servicingdate = $this->view->servicingdate = $values['servicingdate'];
        $servicingdate = str_replace("/","-",$servicingdate);
        $date1 = strtotime($servicingdate);
        $servicingdate = date('Y-m-d', $date1);
        $select->where("$bookingTableName.servicing_date LIKE '%$servicingdate%'");
      }
      
      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $select->where($bookingTableName . '.status = ? ', $_POST['status']);
      }

      $this->view->search = 1;
    }
    $this->view->formValues = array_filter($_POST);
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  function deleteAction(){
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->booking_id=$id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking', $id);
        $bookingItem->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        
      }
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This booking has been deleted Successfully.');
      $this->_forward('success', 'utility', 'core', array(
          'closeSmoothbox' => true,
          'parentRefresh' => true,
          'messages' => array($this->view->message),
          'format' => 'smoothbox'
      ));
    }
  }

  function changeStatusAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->booking_id = $booking_id = $this->_getParam('booking_id');
    $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking', $booking_id);
    $this->view->status = $bookingItem->status;
    $status = $this->_getParam('status');

    $owner = Engine_Api::_()->getItem('sitebooking_pro', $bookingItem->pro_id);
    $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);
    $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$bookingItem->ser_id);
    $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$bookingItem->pro_id);
    $providerOwner = Engine_Api::_()->getItem('user', $providerItem->owner_id);


    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $bookingItem->status = $status;
        $bookingItem->save();
      
        $db->commit();

        if($status === 'rejected' && $status != $this->view->status){  
          // Send mail and notifications for client
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_reject');
          Engine_Api::_()->sitebooking()->sendServiceRejectMail($user,$serviceItem,$owner);

        }
        elseif($status === 'pending' && $status != $this->view->status){ 
          // Send mail and notifications for client
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_accept');
          Engine_Api::_()->sitebooking()->sendServiceAcceptMail($user,$serviceItem,$owner); 

        }
        elseif($status === 'completed' && $status != $this->view->status){  
          // Send mail and notifications for client
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_complete');
          Engine_Api::_()->sitebooking()->sendServiceCompleteMail($user,$serviceItem,$owner);
        }
        elseif($status === 'booked' && $status != $this->view->status) {  
          // Send mail and notifications for provider
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($providerOwner, $user, $serviceItem, 'sitebooking_service_booking');
          Engine_Api::_()->sitebooking()->sendServiceBookingMail($providerOwner,$serviceItem,$user);
        }
        elseif($status === 'canceled' && $status != $this->view->status){  
          // Send mail and notifications for provider
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($providerOwner, $user, $serviceItem, 'sitebooking_service_cancel');
          Engine_Api::_()->sitebooking()->sendServiceCancelMail($providerOwner,$serviceItem,$user);
        }

      }
      catch( Exception $e )
      {
        $db->rollBack(); 
      }
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Booking status has been changed successfully.');
      $this->_forward('success', 'utility', 'core', array(
          'closeSmoothbox' => true,
          'parentRefresh' => true,
          'messages' => array($this->view->message),
          'format' => 'smoothbox'
      ));
    }
  }

}