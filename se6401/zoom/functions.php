<?php
require_once 'config.php';

// Zoom Index
// function index() {
if (isset($_GET['code']) && !empty($_GET['code'])) {
    callback($_GET['code']);
} else {
    loginToZoom();
}
// }

// Zoom Login Page
function loginToZoom() {
    include 'index.php';
}

// Zoom Callback Page
function callback($code) {
	try {
	    $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
	 
	    $response = $client->request('POST', '/oauth/token', [
	        "headers" => [
	            "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
	        ],
	        'form_params' => [
	            "grant_type" => "authorization_code",
	            "code" => $code,
	            "redirect_uri" => REDIRECT_URI
	        ],
	    ]);
	 
	    $token = json_decode($response->getBody()->getContents(), true);
	 
	    $db = new DB();
	 
	    // if($db->is_table_empty()) {
        $db->update_access_token(json_encode($token));
        create_meeting();
	    // }
	} catch(Exception $e) {
	    echo $e->getMessage();
	}
}

 
function create_meeting() {
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
 
    $db = new DB();
    $arr_token = $db->get_access_token();
    $accessToken = $arr_token->access_token;
 
    try {
        $response = $client->request('POST', '/v2/users/me/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "topic" => "Let's learn Laravel",
                "type" => 2,
                "start_time" => "2020-05-05T20:30:00",
                "duration" => "30", // 30 mins
                "password" => "123456"
            ],
        ]);
 
        $data = json_decode($response->getBody());
        echo '<pre>';
        print_r($data);
        echo "Join URL: ". $data->join_url;
        echo "<br>";
        echo "Meeting Password: ". $data->password;

        $role = 0;
        // $attendee = generate_signature(CLIENT_ID,CLIENT_SECRET,$data->id,0);
        $signature = generate_signature(JWT_API_KEY,JWT_API_SECRET,$data->id,$role);
        echo "<br>Signature - ".$signature;
        generate_meeting_url( "testing", $data->id, $data->password, $role, $signature);
    } catch(Exception $e) { 
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
 
            $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
            $response = $client->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
                ],
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token
                ],
            ]);
            $db->update_access_token($response->getBody());
 
            create_meeting();
        } else {
            echo $e->getMessage();
        }
    }
}
 
function generate_signature ( $api_key, $api_secret, $meeting_number, $role){

	$time = time() * 1000 - 30000;//time in milliseconds (or close enough)
	
	$data = base64_encode($api_key . $meeting_number . $time . $role);
	
	$hash = hash_hmac('sha256', $data, $api_secret, true);
	
	$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
	
	//return signature, url safe base64 encoded
	return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
}

 
function generate_meeting_url( $name, $meeting_id, $password, $role, $signature){
	$url = "http://localhost/ilm_study/zoom_project/sample-app-web/CDN/meeting.html?name=".$name."&mn=".$meeting_id."&email=&pwd=".$password."&role=".$role."&lang=en-US&signature=".$signature."&china=0&apiKey=".JWT_API_KEY;
	// http://localhost/ilm_study/zoom_project/sample-app-web/CDN/meeting.html?name=Q0ROMS45LjBXaW4xMCNjaHJvbWUvODguMC40MzI0LjEwNA%3D%3D&mn=89764202528&email=&pwd=123456&role=0&lang=en-US&signature=dzc3OGJuNEtTQUtENEpZTGhIOFJ4Zy44OTc2NDIwMjUyOC4xNjEyNDQxOTA5OTIyLjAuNk5WRXNFVTM3bDVzOVlta2lTTTRSUXhPUVh1YzcyOFlsSVhaMVVhWG9uZz0&china=0&apiKey=w778bn4KSAKD4JYLhH8Rxg
	//return signature, url safe base64 encoded
	echo "<br>";
	echo "<p> Please hit the url to launch meeting - <a href='".$url."'>Click here</a>";
}

?>