<?php
class DB {
    private $dbHost     = "localhost";
    private $dbUsername = "consl2_se622";
    private $dbPassword = "Lotus$12345";
    private $dbName     = "consl2_se622";
 
    public function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }
  
    public function is_table_empty($user_id) {
        // echo $user_id; die;
        $result = $this->db->query("SELECT id FROM engine4_token where user_id=".$user_id);
        if($result->num_rows) {
            return false;
        }
  
        return true;
    }
  
    public function get_access_token($user_id) {
        $sql = $this->db->query("SELECT access_token FROM engine4_token where user_id=".$user_id);
        $result = $sql->fetch_assoc();
        
        if($result && isset($result['access_token'])){
            return json_decode($result['access_token']);
        } else {
            return false;
        }
        
    }
    
    public function get_zoom_user_id($user_id) {
        $sql = $this->db->query("SELECT zoom_user_id FROM engine4_token where user_id=".$user_id);
        $result = $sql->fetch_assoc();
        return $result['zoom_user_id'];
    }
  
    public function get_refersh_token($user_id) {
        $result = $this->get_access_token($user_id);
        return $result->refresh_token;
    }
  
    public function update_access_token($token,$user_id, $getZoomUserData=null) {
        if($getZoomUserData){
            $zoomUser = json_decode($getZoomUserData);
            $zoomUserId = $zoomUser->id;
        } else {
            $zoomUserId = null;
        }
        
        if($this->is_table_empty($user_id)) {
            $this->db->query("INSERT INTO engine4_token(access_token, user_id, zoom_user_id, zoom_user_data) VALUES('$token','$user_id','$zoomUserId','$getZoomUserData')");
        } else {
            if($getZoomUserData && $zoomUserId){
                $this->db->query("UPDATE engine4_token SET access_token = '$token', user_id='$user_id', zoom_user_id='$zoomUserId', zoom_user_data='$getZoomUserData' WHERE user_id = '$user_id'");
            }else{
                $this->db->query("UPDATE engine4_token SET access_token = '$token', user_id='$user_id' WHERE user_id = '$user_id'");
            }
        }
    }
    
    public function store_meeting_data($data,$user_id,$owner_id,$servicebooking_id,$start_time) {
        $raw_data = json_encode($data); 
        $user_ids = array();
        $user_ids[$user_id] = $user_id;
        $user_ids = json_encode($user_ids);
        
        $servicebooking_ids = array();
        $servicebooking_ids[$servicebooking_id] = $servicebooking_id;
        $servicebooking_ids = json_encode($servicebooking_ids);
        $this->db->query("INSERT INTO zoom_meetings(servicebooking_id, user_id, owner_id, start_url, join_url, meeting_password, start_time, raw_data, user_ids, servicebooking_ids) VALUES('$servicebooking_id','$user_id','$owner_id','$data->start_url','$data->join_url','$data->password','$start_time','$raw_data', '$user_ids', '$servicebooking_ids')");
        return true;
    }
    
    public function check_meeting($user_id,$servicebooking_id,$start_time){
        $sql = $this->db->query("SELECT join_url FROM zoom_meetings where user_ids LIKE '%".$user_id."%' && servicebooking_id=".$servicebooking_id." && start_time LIKE '%".$start_time."%'");
        $result = $sql->fetch_assoc();
        return $result;
        
    }
    
    public function get_user_meeting($user_id){
        //echo "SELECT * FROM zoom_meetings where user_ids LIKE '%".$user_id."%'";die;
        $meetingUrl = array();
        $result = $this->db->query("SELECT * FROM zoom_meetings where user_ids LIKE '%".$user_id."%'");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($meetingUrl, $row);
            }
        }
        return $meetingUrl;
    }
    
    public function insert_deauth_data($deauthData,$retention_data){
        if($deauthData){
            $this->db->query("INSERT INTO zoom_deauth(deauth_data, retention_data) VALUES('$deauthData', '$retention_data')");
        }
    }
    
    public function get_userData($user_id) {
        $sql = $this->db->query("SELECT user_id FROM engine4_token where zoom_user_id LIKE '".$user_id."'");
        if ($sql->num_rows > 0) {
          $result = $sql->fetch_assoc();
          return $result['user_id'];
        } else {
            return null;
        }
    }
    
    public function delete_access_token($user_id){
        $sql = $this->db->query("DELETE FROM engine4_token WHERE user_id=".$user_id);
    }
    
    public function delete_meetings($user_id){
        $sql = $this->db->query("DELETE FROM zoom_meetings WHERE user_id=".$user_id);
    }
    
    public function get_provider_meeting($pro_id){
        $meetingUrl = array();
        $result = $this->db->query("SELECT zoom_meetings.*, engine4_sitebooking_servicebookings.pro_id FROM zoom_meetings INNER JOIN engine4_sitebooking_servicebookings ON zoom_meetings.servicebooking_id = engine4_sitebooking_servicebookings.servicebooking_id where engine4_sitebooking_servicebookings.pro_id = ".$pro_id);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($meetingUrl, $row);
            }
        }
        return $meetingUrl;
    }
    
    public function get_meeting($booking_id){
        $meetingUrl = array();
        
        $result = $this->db->query("SELECT zoom_meetings.* FROM zoom_meetings where zoom_meetings.servicebooking_id = ".$booking_id);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($meetingUrl, $row);
            }
        }
        return $meetingUrl;
    }
    
    public function updateUserIds($user_ids, $id) {
        
        $this->db->query("UPDATE zoom_meetings SET user_ids = '$user_ids' WHERE id = '$id'");
    }
    
    public function updateServiceIds($servicebooking_ids, $id) {
        
        $this->db->query("UPDATE zoom_meetings SET servicebooking_ids = '$servicebooking_ids' WHERE id = '$id'");
    }
}