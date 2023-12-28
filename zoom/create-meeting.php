<?php
require_once 'config.php';
 
function create_meeting() {    
    if(isset($_POST['user_id']) && $_POST['user_id']){
        $user_id = $_POST['user_id'];
        $owner_id = $_POST['owner_id'];
        $servicebooking_id = $_POST['servicebooking_id'];
        $topic = $_POST['topic'];
        $type = $_POST['type'];
        $start_time = $_POST['start_time'];
        $duration = $_POST['duration'];
        $password = $_POST['password'];
        $recurrence = isset($_POST['recurrence']) ? $_POST['recurrence'] : '';
        
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
 
    $db = new DB();    
    $arr_token = $db->get_access_token($owner_id);
    if($arr_token && isset($arr_token->access_token)){
        $accessToken = $arr_token->access_token;
    } else {
        $res = array('success'=>false,'msg'=>'Service provider’s Zoom account is not linked');
       echo json_encode($res); exit;
    }
    
    $check_meeting = $db->check_meeting($user_id,$servicebooking_id,$start_time);
//   if($check_meeting && isset($check_meeting['join_url'])){
//       $res = array('join_url'=>$check_meeting['join_url'],'success'=>true);
//       echo json_encode($res); exit;
//   }
   
   $db = new DB(); 
   $zoom_user_id = $db->get_zoom_user_id($owner_id);
 
    try {
        
         if($recurrence) {
            
        // //     $response = $client->request('GET', '/v2/users/'.$zoom_user_id.'/meetings', [
        // //     "headers" => [
        // //         "Authorization" => "Bearer $accessToken"
        // //     ],
        // //     'json' => [
        // //         "topic" => $topic,
        // //         "type" => $type,
        // //         "start_time" => $start_time,
        // //         "duration" => $duration, // 30 mins
        // //         "password" => $password,
        // //         "recurrence" => $recurrence
        // //     ],
        // // ]); 
        
        // // $data = json_decode($response->getBody());
        
        
                  $response = $client->request('POST', '/v2/users/'.$zoom_user_id.'/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "topic" => $topic,
                "type" => $type,
                "start_time" => $start_time,
                "duration" => $duration, // 30 mins
                "password" => $password,
                "recurrence" => $recurrence
            ],
        ]); 
         } else {
                    $response = $client->request('POST', '/v2/users/'.$zoom_user_id.'/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "topic" => $topic,
                "type" => $type,
                "start_time" => $start_time,
                "duration" => $duration, // 30 mins
                "password" => $password
            ],
        ]);
        }

 
        $data = json_decode($response->getBody());
        //print_r($data);die;
        $storeMeetingData = $db->store_meeting_data($data,$user_id,$owner_id,$servicebooking_id, $start_time);
        // echo '<pre>';
        // print_r($data);exit;
        // echo "Join URL: ". $data->join_url;
        // echo "<br>";
        // echo "Meeting Password: ". $data->password;
        $res = array('join_url'=>$data->join_url,'success'=>true);
         echo json_encode($res);
 
    } catch(Exception $e) { 
        if( 401 == $e->getCode()) {
            $db = new DB(); 
            $refresh_token = $db->get_refersh_token($owner_id);
 
            $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
            $response = $client->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET),
                    "Content-type" => "application/x-www-form-urlencoded"
                ],
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token
                ],
            ]);
            
            $token = json_decode($response->getBody(), true);
            $token['user_id']=$owner_id;
            $db = new DB(); 
            $db->update_access_token(json_encode($token),$owner_id);
            
            // $db->update_access_token($response->getBody());
 
            create_meeting();
        } else {
            echo $e->getMessage();
        }
    }
        
    }
}
 
create_meeting();