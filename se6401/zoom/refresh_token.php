<?php
require_once 'config.php';
 
function refresh_token() {
     $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
     
 
    $db = new DB();
    // $arr_token = $db->get_access_token();
    // $accessToken = $arr_token->access_token;
 

    echo "<br>".$refresh_token = $db->get_refersh_token();
    // print_r($refresh_token);die();

    //$client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
    $response = $client->request('POST', '/oauth/token', [
        "headers" => [
            "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
        ],
        'form_params' => [
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ],
    ]);
    print_r($response);die();
    $db->update_access_token($response->getBody());

    return 'Token Updated';
       
    }

 
refresh_token();