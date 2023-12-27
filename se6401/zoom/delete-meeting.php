<?php
require_once 'config.php';
 
$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
 
$db = new DB();
$arr_token = $db->get_access_token();
$accessToken = $arr_token->access_token;
 
$response = $client->request('DELETE', '/v2/meetings/72257967049', [
    "headers" => [
        "Authorization" => "Bearer $accessToken"
    ]
]);