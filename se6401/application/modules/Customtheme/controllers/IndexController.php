<?php
include "bbb/vendor/autoload.php";
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
class CustomTheme_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  
  public function startMeetingAction() {
        $serviceBookingId = $this->_getParam('servicebooking_id');
        $role = $this->_getParam('role');
        $bookingItem = Engine_Api::_()->getItem('sitebooking_servicebooking', $serviceBookingId);
        $owner = Engine_Api::_()->getItem('sitebooking_pro', $bookingItem->pro_id);
        $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);
        $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$bookingItem->ser_id);
        $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$bookingItem->pro_id);
        $providerOwner = Engine_Api::_()->getItem('user', $providerItem->owner_id);
        $duration = $bookingItem->duration;
        $dd = json_decode($duration, true); 
        $timeDD  = $dd[$bookingItem->servicing_date];
        
        $add = '';
        if($serviceItem->type == 2) {
            $add = "_" . explode("-",$timeDD)[0];
        }
        
        $tableBooking = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
        $bookingTableName = $tableBooking->info('name');
        
        $tableService = Engine_Api::_()->getDbtable('sers', 'sitebooking');
        $serviceTableName = $tableService->info('name');
        
        $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
        $providerTableName = $tableProvider->info('name');
        
        $apiUrl = "https://1guarantee.com/bigbluebutton/";
        $salt = "JzSv19yx0gwdwwQbe0L5n65VaucYMXeC2KcszXUjdp4";
        $meetingId = "consultant-ser-". $bookingItem->ser_id . $add; 
        $goFurther = false;
        $isMeetingRunning = false;
        
        $bbb = new BigBlueButton($apiUrl, $salt);
        
    //     $getMeetingInfoParameters = new GetMeetingInfoParameters($meetingId);
    //     $response =  $bbb->getMeetingInfo($getMeetingInfoParameters);
    // print_r($response);die;
    //     if($response['returncode'] == "FAILED") {
    //         echo $response['message']; exit();
    //     }

        // Let's first check if the meeting is already running or not.
        $meetingRunningParams = new IsMeetingRunningParameters($meetingId);
        
        try {
            $response = $bbb->isMeetingRunning($meetingRunningParams);
            
            if($response->success()){
                $isMeetingRunning = $response->isRunning();
                $goFurther = true;
            }else{
                echo json_encode(array("success" => false, "message" =>  $response->getMessage()));
                exit();
                
            }
            
        } catch (\Exception $e) {
            echo json_encode(array("success" => false, "message" =>  $e->getMessage()));
        }
        
        if (!$goFurther) {
            echo json_encode(array("success" => false, "message" =>  "meeting have no attendees..."));exit();
        }

        if (!$isMeetingRunning && $role == 'moderator') {
            // So, meeting isn't running. We'll create a new meeting.
            
            $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($providerOwner);
            //print_r($values->toArray());die;
            // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
            foreach($values->toArray() as $value) {
                if($value['field_id'] == 36) {
                    $fname = $value['value'];
                } else if($value['field_id'] == 47) {
                    $fname = $value['value'];
                } elseif($value['field_id'] ==37) {
                    $lname = $value['value'];
                } else if($value['field_id'] == 48) {
                    $lname = $value['value'];
                }
            }
            
            if($fname) {
                $meetingName1 = $fname . ' '  . $lname;
            } else {
                $meetingName1 = $providerOwner->getTitle();
            }
             
             

            $meetingName = $meetingName1 . " Meeting Room";
            $attendee_password = '123456';
            $moderator_password = "12345678";
        
            // https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Parameters/CreateMeetingParameters.php
            $createMeetingParams = new CreateMeetingParameters($meetingId, $meetingName);
            $createMeetingParams->setAttendeePassword($attendee_password);
            $createMeetingParams->setModeratorPassword($moderator_password);
            $createMeetingParams->setMaxParticipants(10);
            $createMeetingParams->setAllowModsToUnmuteUsers(true);
            $createMeetingParams->setDuration(30); // duration in minutes. 0 = unlimited
            
        
            // Optional. Metadata is used to store customized information. This data can be retrieved during get recordings.
            //$createMeetingParams->addMeta("library", "littleredbutton/bigbluebutton-api-php");
            //$createMeetingParams->addMeta("php_version", "7.4");
        
            try {
                $createMeetingResponse = $bbb->createMeeting($createMeetingParams);
                if ($createMeetingResponse->success()) {
                    //https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Responses/CreateMeetingResponse.php
                    //echo $createMeetingResponse->getInternalMeetingId();
                    $isMeetingRunning = true;
                } else{
                   
                    echo json_encode(array("success" => false, "message" =>  $createMeetingResponse->getMessage() ));exit();
                    
                }
            } catch (\Exception $e){
                 echo json_encode(array("success" => false, "message" =>  $e->getMessage() ));exit();
            }
        }
                     $valuess = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($user);
            //print_r($values->toArray());die;
            // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
            foreach($valuess->toArray() as $value1) {
                if($value1['field_id'] == 36) {
                    $fname = $value1['value'];
                } else if($value1['field_id'] == 47) {
                    $fname = $value1['value'];
                } elseif($value1['field_id'] ==37) {
                    $lname = $value1['value'];
                } else if($value1['field_id'] == 48) {
                    $lname = $value1['value'];
                }
            }
            
            if($fname) {
                $meetingName2 = $fname . ' '  . $lname;
            } else {
                $meetingName2 = $user->getTitle();
            }
          
        if($isMeetingRunning){
            if($role == 'moderator') {
                $displayname = $meetingName1; // your name
                $password = "12345678"; // This password can either be a moderator password or an attendee password. If you use the moderator password, the user will join as a moderator; otherwise, the user will join as an attendee. 
                $userId = "12345" . $providerOwner->getIdentity();
            } else {
                $displayname = $meetingName2; // your name
                $password = "123456"; // This password can either be a moderator password or an attendee password. If you use the moderator password, the user will join as a moderator; otherwise, the user will join as an attendee. 
                $userId = "123" . $user->getIdentity();
            }
            
            $joinMeetingParams = new JoinMeetingParameters($meetingId, $displayname, $password);
            $joinMeetingParams->setUserId($userId); // This option is useful if you don't want the same user to be able to join from multiple devices. A unique user id or value must be sent in this case. 
            $joinMeetingParams->setJoinViaHtml5(true);
            $joinMeetingParams->setRedirect(true);
            $joinMeetingParams->addUserData("bbb_record_video", true);
            $joinUrl = $bbb->getJoinMeetingURL($joinMeetingParams);
           // print_r($joinUrl);die;
            echo json_encode(array("success" => true, "message" =>  $joinUrl ));exit();
        } else {
            echo json_encode(array("success" => false, "message" =>  "meeting has not started yet" ));exit();
        }
    }
}
