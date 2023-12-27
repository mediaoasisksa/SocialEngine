<?php

include __DIR__ . "/vendor/autoload.php";

use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;

$apiUrl = "https://1guarantee.com/bigbluebutton/";
$salt = "JzSv19yx0gwdwwQbe0L5n65VaucYMXeC2KcszXUjdp4";
$meetingId = "test_01"; 
$goFurther = false;
$isMeetingRunning = false;

$bbb = new BigBlueButton($apiUrl, $salt);

// Let's first check if the meeting is already running or not.

$meetingRunningParams = new IsMeetingRunningParameters($meetingId);

try {
    $response = $bbb->isMeetingRunning($meetingRunningParams);

    if($response->success()){
        $isMeetingRunning = $response->isRunning();
        $goFurther = true;
    }else{
        echo $response->getMessage() .  "\n";
    }
    
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}

if (!$goFurther) {
    return;
}

if (!$isMeetingRunning) {
    // So, meeting isn't running. We'll create a new meeting.
    $meetingName = "My Test Meeting";
    $attendee_password = "student";
    $moderator_password = "teacher";

    // https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Parameters/CreateMeetingParameters.php
    $createMeetingParams = new CreateMeetingParameters($meetingId, $meetingName);
    $createMeetingParams->setAttendeePassword($attendee_password);
    $createMeetingParams->setModeratorPassword($moderator_password);
    $createMeetingParams->setMaxParticipants(10);
    $createMeetingParams->addPresentation("http://classroom2.mynaparrot.es/mynadefault.pdf");
    $createMeetingParams->setLogoutURL("https://mynaparrot.com/");
    $createMeetingParams->setAllowModsToUnmuteUsers(true);
    $createMeetingParams->setDuration(60); // duration in minutes. 0 = unlimited

    // Optional. Metadata is used to store customized information. This data can be retrieved during get recordings.
    //$createMeetingParams->addMeta("library", "littleredbutton/bigbluebutton-api-php");
    //$createMeetingParams->addMeta("php_version", "7.4");

    try {
        $createMeetingResponse = $bbb->createMeeting($createMeetingParams);
        if ($createMeetingResponse->success()) {
            //https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Responses/CreateMeetingResponse.php
            //echo $createMeetingResponse->getInternalMeetingId();
            $isMeetingRunning = true;
        }else{
            echo $createMeetingResponse->getMessage() .  "\n";
            $goFurther = false;
        }
    }catch (\Exception $e){
        echo $e->getMessage() . "\n";
        $goFurther = false;
    }
}

if (!$goFurther) {
    return;
}

if($isMeetingRunning){
    $displayname = "Jibon"; // your name
    $password = "teacher"; // This password can either be a moderator password or an attendee password. If you use the moderator password, the user will join as a moderator; otherwise, the user will join as an attendee. 

    //https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Parameters/JoinMeetingParameters.php
    $joinMeetingParams = new JoinMeetingParameters($meetingId, $displayname, $password);
    //$joinMeetingParams->setUserId($userid); // This option is useful if you don't want the same user to be able to join from multiple devices. A unique user id or value must be sent in this case. 
    $joinMeetingParams->setJoinViaHtml5(true);
    $joinMeetingParams->setRedirect(true);

    //https://github.com/littleredbutton/bigbluebutton-api-php/blob/master/src/Parameters/UserDataParameters.php
    //https://docs.bigbluebutton.org/2.2/customize.html#passing-custom-parameters-to-the-client-on-join
    $joinMeetingParams->addUserData("bbb_record_video", true);

    $joinUrl = $bbb->getJoinMeetingURL($joinMeetingParams);
echo $joinUrl;die;
    header('Location:' . $joinUrl);
}