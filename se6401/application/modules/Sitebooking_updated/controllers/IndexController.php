<?php

class Sitebooking_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  public function bookServiceAction(){
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitebooking_main');

    $viewer = Engine_Api::_()->user()->getViewer();
    $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$this->_getparam('ser_id'));
    $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$serviceItem->parent_id);
    $serviceOwner = Engine_Api::_()->getItem('user',$serviceItem->getOwner());

    if($serviceItem->enabled == 0 || $providerItem->enabled == 0)
      return $this->_forward('notfound', 'error', 'core');

    if($viewer->getIdentity() == $serviceItem->owner_id){
      return $this->_forward('requireauth', 'error', 'core');
    }

  	$this->view->ser_id = $values['ser_id'] = $ser_id = $this->_getparam('ser_id');
    $values['user_id'] = $viewer->getIdentity();

    $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
    $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$this->_getParam('ser_id')));

    $serviceTable = Engine_Api::_()->getDbtable('sers','sitebooking');
    $serviceTableName = $serviceTable->Info('name');

    $providerTable = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $providerTable->info('name');

    $select = $serviceTable->select();
    $select
      ->setIntegrityCheck(false)
      ->from($providerTableName, array('title as provider_title','slug as provider_slug','photo_id as provider_photo_id','timezone'))
      ->join($serviceTableName, "$providerTableName.pro_id = $serviceTableName.parent_id", array('*'));
    $select->where($serviceTableName.'.ser_id = ?',$ser_id);
    $this->view->service = $service = $serviceTable->fetchAll($select);

    $values['pro_id'] = $service[0]->parent_id;

    $this->view->duration = $duration = $service[0]->duration;
    $this->view->viewrTimezone = $viewer->timezone;
    $this->view->timeFrameValue = $timeFrameValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.bookingtimeframe",'3');


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

      foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($viewer->timezone));
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

      $this->view->monday = $timeSlot['mon'] = $monday;
      $this->view->tuesday = $timeSlot['tue'] = $tuesday;
      $this->view->wednesday = $timeSlot['wed'] = $wednesday;
      $this->view->thursday = $timeSlot['thu'] = $thursday;
      $this->view->friday = $timeSlot['fri'] = $friday;
      $this->view->saturday = $timeSlot['sat'] = $saturday;
      $this->view->sunday = $timeSlot['sun'] = $sunday;

    }
    $this->view->timeSlot = $timeSlot;
  	if( !empty($_POST) ) {
      $bookingTable = Engine_Api::_()->getItemTable('sitebooking_servicebooking');

      $db = $bookingTable->getAdapter();
      $db->beginTransaction();

      try {
        $servDuration = Engine_Api::_()->sitebooking()->timezoneConvertUsingDuration($viewer->timezone,'UTC',$_POST['duration'],$duration);
        $servicingDate = '';
        foreach ($servDuration as $key => $value) {
          $servicingDate = $servicingDate.', '.$key;
        }
        $_POST['duration'] = json_encode($servDuration);
        $_POST['servicing_date'] = substr($servicingDate,2,strlen($servicingDate));
        $values = array_merge($_POST,$values);

        $bookingTableRow = $bookingTable->createRow();
        $bookingTableRow->setFromArray($values);
        $bookingTableRow->save();

        //insert boooking count in provider table and service table
        $serviceItem->no_of_bookings = $serviceItem->no_of_bookings+1;
        $serviceItem->save();
        $providerItem->no_of_bookings = $providerItem->no_of_bookings+1;
        $providerItem->save();

        // Send notifications for provider
        $owner = Engine_Api::_()->getItem('user', $bookingTableRow->user_id);
        $user = Engine_Api::_()->getItem('user', $providerItem->owner_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_booking');
        Engine_Api::_()->sitebooking()->sendServiceBookingMail($user,$serviceItem,$owner);
        
        $db->commit();
       return $this->_helper->_redirector->gotoRoute(array('module'=>'sitebooking','controller' => 'index','action' => 'booked-services','booking_id'=> $bookingTableRow->getIdentity()),'sitebooking_booking_general',true);
      }
      catch (Execption $e) {
        $db->rollBack();
      }
    }

  }
  
  public function bookedServicesAction(){
   if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $params['user_id'] = $viewer->getIdentity();
    $this->_helper->content
        //->setNoRender()
        ->setEnabled();
    $this->view->timezone = $viewer->timezone;
    $this->view->booking_id = $this->_getParam('booking_id');

    $params['page'] = $this->_getParam('page',1);
    
    $this->view->bookedItems = $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
  }

  public function statusAction(){
    if( !$this->_helper->requireUser()->isValid() ) return;
    $this->view->bookingId = $bookingId = $this->_getParam('booking_id');

      $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking',$bookingId);
      $action = $this->_getParam('action_type');

      $owner = Engine_Api::_()->user()->getViewer();
      $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$bookingItem->ser_id);
      $user = Engine_Api::_()->getItem('user', $serviceItem->owner_id);

    if( $this->getRequest()->isPost() )
    {
      if($action === 'cancel' && $bookingItem->status ==='booked'){  
      $bookingItem->status = 'canceled';
      $bookingItem->save();
      $status = 'canceled';
      // Send mail and notifications for client
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_cancel');
        Engine_Api::_()->sitebooking()->sendServiceCancelMail($user,$serviceItem,$owner);

      }
      // $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Booking request has been canceled successfully.');
      $this->_forward('success', 'utility', 'core', array(
        'closeSmoothbox' => true,
        'parentRefresh'=> true,
        'messages' => array($this->view->message),
        'format' => 'smoothbox'
      ));
    }
  
  }

  public function contactAction()
  {
    $bookingId = $this->_getparam('booking_id');
    if( !$this->_helper->requireUser()->isValid() ) return;
    $this->view->bookedItem = $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking',$bookingId);

    $this->view->username = Engine_Api::_()->getItem('user',$bookingItem->user_id)->getTitle();
  }
  

}
