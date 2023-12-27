<?php
require_once 'zoom/config.php';
class Sitebooking_Model_DbTable_Servicebookings extends Core_Model_Item_DbTable_Abstract 
{
  protected $_rowClass = "Sitebooking_Model_Servicebooking";
  
	public function getBookingsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getBookingsSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if( empty($params['limit']) )
    {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page');
      $paginator->setItemCountPerPage($page);
    }
    
    return $paginator;
  }

  public function getBookingsSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');

    $userTable = Engine_Api::_()->getItemtable('user');
    $userTableName = $userTable->info('name');

    $select = $bookingTable->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $bookingTableName.'.booking_date DESC' );

    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($bookingTableName.'.user_id = ?', $params['user_id']);
    }

    if( !empty($params['service_title']) || !empty($params['category']) )
    {
      $serviceTable = Engine_Api::_()->getItemtable('sitebooking_ser');
      $serviceTableName = $serviceTable->info('name');
      $title = $params['service_title'];
      $select = $select
              ->setIntegrityCheck(false)
              ->from($bookingTableName)
              ->joinLeft($serviceTableName, "$bookingTableName.ser_id = $serviceTableName.ser_id",array('title','category_id'))
              ->group("$bookingTableName.servicebooking_id");

      if( !empty($params['service_title']) ){
        $title = $params['service_title'];
        $select->where("$serviceTableName.title  LIKE '%$title%'");
      }
      if( !empty($params['category']) ){
        $category = $params['category'];
        $select->where("$serviceTableName.category_id = $category");
      }

    }

    if( !empty($params['status'])  )
    {
      $select->where($bookingTableName.'.status = ?', $params['status']);
    }
    
        if( !empty($params['ser_id']) )
    {
      $select->where($bookingTableName.'.ser_id = ?', $params['ser_id']);
    }


    if( !empty($params['pro_id']) )
    {
      $select->where($bookingTableName.'.pro_id = ?', $params['pro_id']);
    }

    if (!empty($params['category']) && $params['category'] != -1) {
      $select->where($serviceTableName . '.category_id = ?', $params['category']);
    }

    if( !empty($params['booking_date']) )
    {
      $bookingdate = date('Y-m-d', strtotime($params['booking_date']) );;
      // $bookingdate = str_replace("/","-",$bookingdate);
      $select->where("$bookingTableName.booking_date LIKE '%$bookingdate%'");
    }

    if( !empty($params['servicing_date']) )
    {
      $date = date('Y-m-d', strtotime($params['servicing_date']) );
      $select->where($bookingTableName.".servicing_date LIKE '%$date%'");

    } 
    
       $select->group($bookingTableName.'.duration');
       if(isset($params['groupby'])) {
           $select->group($bookingTableName.'.user_id');
       }
     
    //GROUP BY `duration`, `user_id`


    if( !empty($owner) ) {
      return $select;
    }
    
    return $select;
  }
  
  
  public function approve($order) {
    $booking_id = $order->source_id;
    $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking', $booking_id);
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
    
            //if($status === 'pending' && $status != $this->view->status){
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
                            
                            $createMeeting = $this->createMeetingCurlRequest2($postFields, $postUrl);
                            
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
                      
                        $servicebookingIds = json_decode($arr_2[0]['servicebooking_ids'], true);
                        $servicebooking_ids = array();
                        if(!$servicebookingIds) {
                            $servicebooking_ids[$row->servicebooking_id] = $row->servicebooking_id;
                        } 
                         $servicebookingIds[$bookingItem->servicebooking_id] = $bookingItem->servicebooking_id;
                         
                        $db1 = new DB();  
                        $db1->updateUserIds( json_encode($currentIds), $arr_2[0]['id']);
                        $db1->updateServiceIds( json_encode($servicebookingIds), $arr_2[0]['id']);
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
                            $createMeeting = $this->createMeetingCurlRequest2($postFields, $postUrl);
                            
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
                                //exit;
                            }
                        }
                    }
                }
                                 
                } 
            }
       // }
        
        $bookingItem->status = 'pending';
        $bookingItem->save();
      
       // $db->commit();
    // $db1 = new DB();    
    // $arr_1 = $db1->get_meeting($booking_id);
    // if(!count($arr_1)) {
    //     $select = $tableBooking->select()
    //                             ->from($bookingTableName)
    //                             ->where($bookingTableName . '.duration = ? ', $bookingItem->duration)
    //                             ->where($bookingTableName . '.servicebooking_id != ? ', $booking_id)
    //                             ->order("servicebooking_id ASC")
    //                             ->limit(1);
                         
    //     $row = $tableBooking->fetchRow($select);   
    //     if($row && $serviceItem->type == 2) {
    //         $db1 = new DB();    
    //         $arr_2 = $db1->get_meeting($row->servicebooking_id);
    //         if(count($arr_2)) { 
    //             $currentIds = json_decode($arr_2[0]['user_ids'], true);
    //             $user_ids = array();
    //             if(!$currentIds) {
    //                 $user_ids[$row->user_id] = $row->user_id;
    //             } 
    //             $currentIds[$bookingItem->user_id] = $bookingItem->user_id;
    //             $db1 = new DB();  
    //             $db1->updateUserIds( json_encode($currentIds), $arr_2[0]['id']);
    //         }
    //     } else {
    //         $postUrl = "https://".$_SERVER['HTTP_HOST']."/upgrade/zoom/create-meeting.php";
    //         $date = json_decode($bookingItem->duration);
    //         $meeting_urls = array();
    //         foreach ($date as $key => $value){
    //             $time_slots = explode(", ",$value);
    //             for ($i=0; $i < count($time_slots); $i++) {
    //                 $time_one = explode("-",$time_slots[$i]);
                    
    //                 $start_time = $key.'T'.$time_one[0];
                    
    //                 $meeting_start_time = $key.'T'.$time_one[0];
                    
    //                 $duration = ($serviceItem->duration/60);
                    
    //                 if($bookingItem->servicing_end_date) {
    //                     $end_start_time = $bookingItem->servicing_end_date.'T'.$time_one[0];
    //                     $postFields = array(
    //                     'servicebooking_id' => $bookingItem->servicebooking_id,
    //                     'user_id' => $bookingItem->user_id,
    //                     'owner_id' => $owner->owner_id,
    //                     "topic" => $bookingItem->problem_desc,
    //                     "type" => '8',
    //                     "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
    //                     "duration" => $duration, // 30 mins
    //                     "password" => '123456',
    //                     "recurrence" => array(
    //                       "end_date_time" => $end_start_time,
    //                         "end_times"=> 30,
    //                         "monthly_day"=> 1,
    //                         "monthly_week"=> 1,
    //                         "monthly_week_day"=> 1,
    //                         "repeat_interval"=> 1,
    //                         "type"=> 1,
    //                         "weekly_days"=> "1"
    //                     )
    //                     );
        
    //                 } else {
    //                     $postFields = array(
    //                     'servicebooking_id' => $bookingItem->servicebooking_id,
    //                     'user_id' => $bookingItem->user_id,
    //                     'owner_id' => $owner->owner_id,
    //                     "topic" => $bookingItem->problem_desc,
    //                     "type" => '2',
    //                     "start_time" => $bookingItem->servicing_date.'T'.$time_one[0],
    //                     "duration" => $duration, // 30 mins
    //                     "password" => '123456',
    //                     );
    //                 }
                    
    //                 $createMeeting = $this->createMeetingCurlRequest2($postFields, $postUrl);
    //                 if($createMeeting['success']){
    //                     $srt_time = $key.' '.$time_one[0];
    //                     $pushValue = array('join_url'=>$createMeeting['join_url'],'start_time'=>$srt_time);
    //                     array_push($meeting_urls,$pushValue);
    //                 } else {
    //                     if($createMeeting['msg']){
    //                         //echo $createMeeting['msg'];
    //                     } else {
    //                         ///echo 'Something Went Wrong, Please Try Again!';
    //                     }
    //                 }
    //             }
    //         }
    //     }                     
    // } 
    
    // $bookingItem->status = 'pending';
    // $bookingItem->save();
  }
  
  function createMeetingCurlRequest2($postFields, $postUrl){
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