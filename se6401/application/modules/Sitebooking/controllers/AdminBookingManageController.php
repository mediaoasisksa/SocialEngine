<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once 'zoom/config.php';
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
        $select->where("$bookingTableName.booking_date LIKE '%$bookingdate%'");
      }

      if (!empty($values['servicingdate'])) {
        $servicingdate = $this->view->servicingdate = $values['servicingdate'];
        $servicingdate = str_replace("/","-",$servicingdate);
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

    $tableBooking = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $tableBooking->info('name');

    $tableService = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceTableName = $tableService->info('name');

    $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $tableProvider->info('name');

    

    //MAKE QUERY




    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
          
        if($status === 'pending' && $status != $this->view->status){
            if($serviceItem->type == 1) {
                $postUrl = "https://".$_SERVER['HTTP_HOST']."/zoom/create-meeting.php";
                $date = json_decode($bookingItem->duration);
                $meeting_urls = array();
                foreach ($date as $key => $value){
                    $time_slots = explode(", ",$value);
                        for ($i=0; $i < count($time_slots); $i++) {
                            $time_one = explode("-",$time_slots[$i]);
                
                            $start_time = $key.'T'.$time_one[0];
                            
                            $meeting_start_time = $key.'T'.$time_one[0];
                            
                            $duration = ($serviceItem->duration/60);
                    
                            if($bookingItem->servicing_end_date) {
                                $end_start_time = $bookingItem->servicing_end_date.'T'.$time_one[0];
                                $postFields = array(
                                'servicebooking_id' => $bookingItem->servicebooking_id,
                                'user_id' => $bookingItem->user_id,
                                'owner_id' => $owner->owner_id,
                                "topic" => $bookingItem->problem_desc,
                                "type" => '8',
                                "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
                                "duration" => $duration, // 30 mins
                                "password" => '123456',
                                "recurrence" => array(
                                       "end_date_time" => $end_start_time,
                                        "end_times"=> 30,
                                        "monthly_day"=> 1,
                                        "monthly_week"=> 1,
                                        "monthly_week_day"=> 1,
                                        "repeat_interval"=> 1,
                                        "type"=> 1,
                                        "weekly_days"=> "1"
                                    )
                                );
                            } else {
                                $postFields = array(
                                'servicebooking_id' => $bookingItem->servicebooking_id,
                                'user_id' => $bookingItem->user_id,
                                'owner_id' => $owner->owner_id,
                                "topic" => $bookingItem->problem_desc,
                                "type" => '2',
                                "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
                                "duration" => $duration, // 30 mins
                                "password" => '123456',
                                );
                            }
                            
                            $createMeeting = $this->createMeetingCurlRequest($postFields, $postUrl);
                            
                            if($createMeeting['success']){
                                $srt_time = $key.' '.$time_one[0];
                                $pushValue = array('join_url'=>$createMeeting['join_url'],'start_time'=>$srt_time);
                                array_push($meeting_urls,$pushValue);
                            } else {
                                if($createMeeting['msg']){
                                    echo $createMeeting['msg'];
                                } else {
                                    echo 'Something Went Wrong, Please Try Again!';
                                }
                                exit;
                            }
                        }
                    }
                } else {
                        $db1 = new DB();    
        $arr_1 = $db1->get_meeting($booking_id);
        if(!count($arr_1)) {
                $select = $tableBooking->select()
                                        ->from($bookingTableName);

                $select->where($bookingTableName . '.duration = ? ', $bookingItem->duration)
                ->where($bookingTableName . '.servicebooking_id != ? ', $booking_id)
                      ->order("servicebooking_id ASC")
                             ->limit(1);
                                 
                $row = $tableBooking->fetchRow($select);   
                
                if($row && $serviceItem->type == 2) {
                    $db1 = new DB();    
                    $arr_2 = $db1->get_meeting($row->servicebooking_id);
                    if(count($arr_2)) { 
                        
                        $currentIds = json_decode($arr_2[0]['user_ids'], true);
                        $user_ids = array();
                        if(!$currentIds) {
                            $user_ids[$row->user_id] = $row->user_id;
                        } 
                         $currentIds[$bookingItem->user_id] = $bookingItem->user_id;
                        // // if(isset($currentIds[$row->user_id])) {
                        // //     $user_ids = $currentIds;
                        // // } else if(isset($currentIds[$bookingItem->user_id])) {
                        // //     $user_ids = $currentIds;
                        // // } else {
                        // //     $user_ids[$bookingItem->user_id] = $bookingItem->user_id;
                        // // }
                        
                        
                        //print_r($currentIds);die;
                        
                        //$user_ids[$arr_2[0]['user_id']] = $arr_2[0]['user_id'];
                        $db1 = new DB();  
                        $db1->updateUserIds( json_encode($currentIds), $arr_2[0]['id']);
                        
                    }
                } else {
                    $postUrl = "https://".$_SERVER['HTTP_HOST']."/zoom/create-meeting.php";
                    $date = json_decode($bookingItem->duration);
                    $meeting_urls = array();
                    foreach ($date as $key => $value){
                        $time_slots = explode(", ",$value);
                        for ($i=0; $i < count($time_slots); $i++) {
                            $time_one = explode("-",$time_slots[$i]);
                            
                                $start_time = $key.'T'.$time_one[0];
                                
                                $meeting_start_time = $key.'T'.$time_one[0];
                    
                                $duration = ($serviceItem->duration/60);
                                
                                if($bookingItem->servicing_end_date) {
                                    $end_start_time = $bookingItem->servicing_end_date.'T'.$time_one[0];
                                    $postFields = array(
                                    'servicebooking_id' => $bookingItem->servicebooking_id,
                                    'user_id' => $bookingItem->user_id,
                                    'owner_id' => $owner->owner_id,
                                    "topic" => $bookingItem->problem_desc,
                                    "type" => '8',
                                    "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
                                    "duration" => $duration, // 30 mins
                                    "password" => '123456',
                                    "recurrence" => array(
                                           "end_date_time" => $end_start_time,
                                            "end_times"=> 30,
                                            "monthly_day"=> 1,
                                            "monthly_week"=> 1,
                                            "monthly_week_day"=> 1,
                                            "repeat_interval"=> 1,
                                            "type"=> 1,
                                            "weekly_days"=> "1"
                                        )
                                    );

                                } else {
                                    $postFields = array(
                                    'servicebooking_id' => $bookingItem->servicebooking_id,
                                    'user_id' => $bookingItem->user_id,
                                    'owner_id' => $owner->owner_id,
                                    "topic" => $bookingItem->problem_desc,
                                    "type" => '2',
                                    "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
                                    "duration" => $duration, // 30 mins
                                    "password" => '123456',
                                    );
                                }
                               
                            $createMeeting = $this->createMeetingCurlRequest($postFields, $postUrl);
                            
                            if($createMeeting['success']){
                                $srt_time = $key.' '.$time_one[0];
                                $pushValue = array('join_url'=>$createMeeting['join_url'],'start_time'=>$srt_time);
                                array_push($meeting_urls,$pushValue);
                            } else {
                                if($createMeeting['msg']){
                                    echo $createMeeting['msg'];
                                } else {
                                    echo 'Something Went Wrong, Please Try Again!';
                                }
                                exit;
                            }
                        }
                    }
                }
                                 
                } 
            }
        }
        
        $bookingItem->status = $status;
        $bookingItem->save();
      
        $db->commit();

        // if($status === 'rejected' && $status != $this->view->status){  
        //   // Send mail and notifications for client
        //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_reject');
        //   Engine_Api::_()->sitebooking()->sendServiceRejectMail($user,$serviceItem,$owner);

        // }
        // elseif($status === 'pending' && $status != $this->view->status){ 
        //   // Send mail and notifications for client
        //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_accept');
        //   Engine_Api::_()->sitebooking()->sendServiceAcceptMail($user,$serviceItem,$owner,$meeting_urls); 

        // }
        // elseif($status === 'completed' && $status != $this->view->status){  
        //   // Send mail and notifications for client
        //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $owner, $serviceItem, 'sitebooking_service_complete');
        //   Engine_Api::_()->sitebooking()->sendServiceCompleteMail($user,$serviceItem,$owner);
        // }
        // elseif($status === 'booked' && $status != $this->view->status) {  
        //   // Send mail and notifications for provider
        //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($providerOwner, $user, $serviceItem, 'sitebooking_service_booking');
        //   Engine_Api::_()->sitebooking()->sendServiceBookingMail($providerOwner,$serviceItem,$user);
        // }
        // elseif($status === 'canceled' && $status != $this->view->status){  
        //   // Send mail and notifications for provider
        //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($providerOwner, $user, $serviceItem, 'sitebooking_service_cancel');
        //   Engine_Api::_()->sitebooking()->sendServiceCancelMail($providerOwner,$serviceItem,$user);
        // }

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
  
  function createMeetingCurlRequest($postFields, $postUrl){
        // Curl request for create zoom url
        //
        
        $ch = curl_init();
        
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
         //print_r($server_output);exit;
        
        curl_close ($ch);
        
        $res = json_decode($server_output);
        
        // Further processing ...
        if ($res->success) {
           return array('success'=>true,'join_url'=>$res->join_url);
        } else {
           return array('success'=>false, 'msg'=>$res->msg);
        }
  }

}