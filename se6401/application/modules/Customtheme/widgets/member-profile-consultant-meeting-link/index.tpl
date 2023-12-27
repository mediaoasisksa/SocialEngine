
<?php if(1):?>
    <?php
    $date = $todayDate = date("Y-m-d");
    $day = strtolower(substr(date("l", strtotime($date)),0,3));
    $days[$date] = $day;
    $timeFrameValue = number_format(1)*1;
    for($i = 0;$i < $timeFrameValue-1; $i++){
    $date = date('Y-m-d', strtotime($date. ' + 1 day'));
    $day = strtolower(substr(date("l", strtotime($date)),0,3));
    $days[$date] = $day;
    }
    
    $this->duration = $duration = 1800;
    $date = date_create('2001-01-01');
    $time = array();
    date_time_set($date, 00, 00);
    $noOfTimeSlots = 86400/$duration;
    $timeFormateForUI = 'H:i';
    $timeFormate = 'H:i';
    for($i = 0;$i<$noOfTimeSlots;$i++)
    {
      $d1 = date_format($date,$timeFormate);
      date_add($date, date_interval_create_from_date_string($duration." seconds"));
      $time[] = $d1;
    }
    
    for($i = 0;$i<24;$i++)
    {
      $d1 = date_format($date,$timeFormate);
      date_add($date, date_interval_create_from_date_string("3600 seconds"));
      $timeOptions[] = $d1;
    }
    ?>
    <?php 
        
        if($this->subject()->getIdentity() == $this->dataConsulant['owner_id'] && $this->viewer()->getIdentity() != $this->dataConsulant['owner_id']): ?>
    <!-- Consultant Appointment-->
    <div class="sesbasic_bxs profile_mentor_consultant_appointment_main">
    	<div class="profile_tab_head">
    		<h2><?php echo $this->translate("Consultant Appointment");?></h2>
    	</div>
    	<div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting3('<?php echo $this->subject()->getIdentity();?>', 'attendee')"><span><?php echo $this->translate("Join Meeting")?></span></a></div>
    </div>
    
    <?php endif;?>
    
    	<?php 
        
        if(Engine_Api::_()->user()->getViewer()->getIdentity() == $this->dataConsulant['owner_id']): ?>
    <!-- My Appointment-->
    <div class="sesbasic_bxs profile_mentor_consultant_appointment_main">
    	<div class="profile_tab_head">
    		<h2><?php echo $this->translate("Live Chat");?></h2>
    	</div>
    	
    	<div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting3('<?php echo Engine_Api::_()->user()->getViewer()->getIdentity();?>', 'moderator')"><span><?php echo $this->translate("Start Meeting")?></span></a></div>
    	
    </div>
    <?php endif;?>
    

    <?php else:?>
    
    <?php endif;?>
    
        
      
    <script>
  
    
    function startMeeting3(id, role){
       scriptJquery.ajax({
                url: '<?php echo $this->url(array('module' => 'customtheme', 'controller' => 'index', 'action' => 'start-meeting1'), 'default', true) ?>',
                method: 'post',
                'data' : {
                    'servicebooking_id' : id,
                    'role': role,
                    'format' : 'json'
                },  
                success: function(responseJSON){
                    console.log(JSON.parse(responseJSON));
                    var r = JSON.parse(responseJSON);
                    if(r.success == true) {
                        window.open(r.message,'_blank');
                    } else {
                        alert(r.message);
                    }
                    
                }
            });
       
    }
    </script>
    
    <style>
        a.sesbasic_animation.custom_btn.custom_btn_primary.disabled {
    color: currentColor;
    cursor: not-allowed;
    opacity: 0.5;
    text-decoration: none;
}
    </style>