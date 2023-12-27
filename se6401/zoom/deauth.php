<?php
require_once 'config.php';

try {
    
    $deauth_res = file_get_contents('php://input');
    
    $deauth_data = json_decode($deauth_res);

    	$post_data["client_id"] = CLIENT_ID;
    	$post_data["user_id"] = $deauth_data->payload->user_id;
    	$post_data["account_id"] = $deauth_data->payload->account_id;
    	$post_data["deauthorization_event_received"] = $deauth_data->payload;
    	$post_data["compliance_completed"] = true;
    	
		$json_post_data = json_encode($post_data);


		$authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);;

    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api.zoom.us/oauth/data/compliance',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$json_post_data,
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Basic '.$authorization,
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		
		$retention_data = json_decode($response);
		
		$db = new DB();
        $db->insert_deauth_data($deauth_res,$response);
        
        $removeUserData = removeUserData($retention_data);
    
} catch(Exception $e) {
    echo $e->getMessage();
}

function removeUserData($retention_data){
    $db = new DB();    
    $user_id = $db->get_userData($retention_data->user_id);
    
    if($user_id){
        $db = new DB();
        $db->delete_access_token($user_id);
        
        $db = new DB(); 
        $db->delete_meetings($user_id);
    }
}