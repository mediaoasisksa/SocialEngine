<?php
require_once 'config.php';
  
try {
    if(isset($_GET['code']) && isset($_GET['user_id'])){
    // $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
 
    // $response = $client->request('POST', '/oauth/token', [
    //     "headers" => [
    //         "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
    //     ],
    //     'form_params' => [
    //         "grant_type" => "authorization_code",
    //         "code" => $_GET['code'],
    //         "redirect_uri" => REDIRECT_URI.'?user_id='.$_GET['user_id']
    //     ],
    // ]);
    
    
    $postFields = array(
        'grant_type' => "authorization_code",
        'code' => $_GET['code'],
        "redirect_uri" => REDIRECT_URI.'?user_id='.$_GET['user_id'],
        );
        
    $authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
    
    $postUrl = 'https://zoom.us/oauth/token';
    $postUrl .= "?grant_type=authorization_code&code=".$_GET['code']."&redirect_uri=".REDIRECT_URI.'?user_id='.$_GET['user_id'];
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $postUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS => json_encode($postFields),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.$authorization,
            'Content-Type: application/json',
            'Accept: application/json'
            ),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
 
    $token = json_decode($response, true);
    $getZoomUserData = getZoomUserData($token['access_token']);
    
    
    $user_id=$_GET['user_id'];
    $token['user_id']=$user_id;
    
    // echo "<pre>";
    // print_r($token);
    // print_r($getZoomUserData);
    // die();
    
    $db = new DB();
        $db->update_access_token(json_encode($token),$user_id, $getZoomUserData);
        header("Location: https://consl2.com/admin/user/manage");
        // echo "Access token inserted successfully.";
    }else{
        return null;
    }
} catch(Exception $e) {
    echo $e->getMessage();
}


function getZoomUserData($access_token){
    $url = 'https://api.zoom.us/v2/users/me';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer '.$access_token
        ),
      ));
    
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}