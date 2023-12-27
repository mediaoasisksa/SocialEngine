<?php
require_once 'config.php';

function get_meeting_url() {
    if(isset($_POST['user_id']) && $_POST['user_id']){
        $user_id = $_POST['user_id'];
        
        $db = new DB();
        $meetingUrl = $db->get_user_meeting($user_id);
         echo json_encode($meetingUrl);
        
        // if(count($meetingUrl)>0){
        //     echo '<pre>';
        //     print_r($meetingUrl); exit;
        // }
    }
    
    if(isset($_POST['pro_id']) && $_POST['pro_id']){
        $pro_id = $_POST['pro_id'];
        
        $db = new DB();
        $meetingUrl = $db->get_provider_meeting($pro_id);
         echo json_encode($meetingUrl);
        
        // if(count($meetingUrl)>0){
        //     echo '<pre>';
        //     print_r($meetingUrl); exit;
        // }
    }
 
}
 
get_meeting_url();