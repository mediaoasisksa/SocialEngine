
<?php
$date = date("Y-m-d");
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

<!-- Mentor Subscriptions-->
<div class="sesbasic_bxs profile_mentor_subscriptions">
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscriptions");?></h2>
	</div>
	<div class="profile_mentor_subscriptions_listing">

		<div class="profile_mentor_subscriptions_item">
			<article>
				<div class="_left">
					<div class="itemthumb">
           	<span class="_userimg"><img src="/application/themes/sescompany/images/nophoto_user_thumb_profile.png" alt=""></span>
          	<span class="_username"><a href="javascript:void(0);">Chris Naismith</a></span>
          </div>
          <div class="iteminfo">
          	<span class="category custom_tag"><a href="javascript:void(0);">Marketing strategy</a></span>
          	<div class="_subcription">
          		<span><?php echo $this->translate("Subscription:");?></span>
          		<span class="_active"><?php echo $this->translate("Active");?></span>
          	</div>
          	<div class="_valid">
          		<span class="sesbasic_text_light"><?php echo $this->translate("Valid till:");?></span>
          		<span class="_date">22/09/2022</span>
          	</div>
          </div>
				</div>
				<div class="_right">
					<div class="_rightcont">
						<div class="_icon">
							<i class="_zoom"></i>
						</div>
						<div class="_rightinfo">
							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_online"><?php echo $this->translate("Online")?></span></div>
							<div class="_time"><b><?php echo $this->translate("Meeting Time:")?></b> <span><?php echo $this->translate("10.00 AM")?></span></div>
						</div>
						<div class="_btn">
							<a href="javascript:void(0);" class="sesbasic_animation custom_btn custom_btn_primary"><span>Join Meeting</span></a>
						</div>
					</div>
				</div>
			</article>
		</div>

		<div class="profile_mentor_subscriptions_item">
			<article>
				<div class="_left">
					<div class="itemthumb">
           	<span class="_userimg"><img src="/application/themes/sescompany/images/nophoto_user_thumb_profile.png" alt=""></span>
          	<span class="_username"><a href="javascript:void(0);">Chris Naismith</a></span>
          </div>
          <div class="iteminfo">
          	<span class="category custom_tag"><a href="javascript:void(0);">Marketing strategy</a></span>
          	<div class="_subcription">
          		<span><?php echo $this->translate("Subscription:");?></span>
          		<span class="_active"><?php echo $this->translate("Active");?></span>
          	</div>
          	<div class="_valid">
          		<span class="sesbasic_text_light"><?php echo $this->translate("Valid till:");?></span>
          		<span class="_date">22/09/2022</span>
          	</div>
          </div>
				</div>
				<div class="_right">
					<div class="_rightcont">
						<div class="_icon">
							<i class="_zoom"></i>
						</div>
						<div class="_rightinfo">
							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_offline"><?php echo $this->translate("Offline")?></span></div>
						</div>
					</div>
				</div>
			</article>
		</div>

		<div class="profile_mentor_subscriptions_item">
			<article>
				<div class="_left">
					<div class="itemthumb">
           	<span class="_userimg"><img src="/application/themes/sescompany/images/nophoto_user_thumb_profile.png" alt=""></span>
          	<span class="_username"><a href="javascript:void(0);">Chris Naismith</a></span>
          </div>
          <div class="iteminfo">
          	<span class="category custom_tag"><a href="javascript:void(0);">Marketing strategy</a></span>
          	<div class="_subcription">
          		<span><?php echo $this->translate("Subscription:");?></span>
          		<span class="_expired"><?php echo $this->translate("Expired");?></span>
          	</div>
          	<div class="_valid">
          		<span class="sesbasic_text_light"><?php echo $this->translate("Valid till:");?></span>
          		<span class="_date">22/09/2022</span>
          	</div>
          </div>
				</div>
				<div class="_right">
					<div class="_rightcont">
						<div class="_txt">
							<?php echo $this->translate("Your subscriptions expired, please renew your subscription");?>
						</div>
						<div class="_btn">
							<a href="javascript:void(0);" class="sesbasic_animation custom_btn custom_btn_primary"><span>Subscribe</span></a>
						</div>
					</div>
				</div>
			</article>
		</div>

	</div>
</div>


<!--Mentor Subscribers-->
<div class="sesbasic_bxs profile_mentor_subscribers">
	<div class="profile_mentor_subscribers_top">
		<div class="_head">
    	    <span class="custom_btn custom_btn_sec custom_btn_rounded"><span><?php echo $this->translate("Today's Booking Slots")?></span></span>
  	    </div>
        <?php $itemkey = 1;?>
    <?php foreach( $this->paginator as $item ) : ?>
    <?php
      $itemArray = $item->toArray();
      $parent_id = $itemArray['parent_id']; $viewer = Engine_Api::_()->user()->getViewer();
          $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
    $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->getIdentity()));
          $this->viewrTimezone = $viewer->getIdentity() ? $viewer->timezone: 'Europe/Moscow';
    $this->timeFrameValue = $timeFrameValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.bookingtimeframe",'3');


    if($scheduleRow){
      $monday = json_decode($scheduleRow->monday, true);
      $tuesday = json_decode($scheduleRow->tuesday, true);
      $wednesday = json_decode($scheduleRow->wednesday, true);
      $thursday = json_decode($scheduleRow->thursday, true);
      $friday = json_decode($scheduleRow->friday, true);
      $saturday = json_decode($scheduleRow->saturday, true);
      $sunday = json_decode($scheduleRow->sunday, true);
      $data = array();
      //$data['demo'] = 'demo';
      if(!empty($monday))
        $data = array_merge($data,$monday);
      if(!empty($tuesday))
        $data = array_merge($data,$tuesday);
      if(!empty($wednesday))
        $data = array_merge($data,$wednesday);
      if(!empty($thursday))
        $data = array_merge($data,$thursday);
      if(!empty($friday))
        $data = array_merge($data,$friday);
      if(!empty($saturday))
        $data = array_merge($data,$saturday);
      if(!empty($sunday))
        $data = array_merge($data,$sunday);
      
      
       $this->timeSlot ='';
       $popAvail = array();
       foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($this->viewrTimezone));
        $d2 = date_format($date2, 'Y-m-d');

        $utcTimeSlot = date_format($date2, 'H:i');
        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);
        
        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){ 
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
      }
   
      $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = array();
      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = 0;
      foreach ($popAvail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $monday['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tuesday['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wednesday['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thursday['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $friday['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $saturday['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sunday['sun_'.$c7] = $value; 
        }
      }

      $this->monday = $timeSlot['mon'] = $monday;
      $this->tuesday = $timeSlot['tue'] = $tuesday;
      $this->wednesday = $timeSlot['wed'] = $wednesday;
      $this->thursday = $timeSlot['thu'] = $thursday;
      $this->friday = $timeSlot['fri'] = $friday;
      $this->saturday = $timeSlot['sat'] = $saturday;
      $this->sunday = $timeSlot['sun'] = $sunday;

    }
    $this->timeSlot = $timeSlot;
    ?>
  
   
      		
      	
      	            <div class="profile_mentor_subscribers_slots">
                  <?php foreach ($days as $date => $day):?>
                    
                        <?php foreach ($time as $key => $value) :?>
                          <?php if(in_array($value, $this->timeSlot[$day],TRUE)):?>
                            <?php 
                            $date1 = date_create(null, timezone_open($this->viewrTimezone));
                            $date2 = date_create($date, timezone_open($this->viewrTimezone));
                            date_time_set($date2, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
                            ?>
                            <?php if($date1 < $date2 ): ?>
                              <?php
                              $date3 = date_create($date." ".$value);
                              $startTime = date_format($date3, 'H:i');
                              date_add($date3, date_interval_create_from_date_string($this->duration." seconds"));
                              $endTime = date_format($date3, 'H:i');
                             
                              ?>
                              <div class="item_slots">
                                <span onclick="window.location.href='bookings/service/book-service/<?php echo $item->getIdentity();?>?date=<?php echo $date;?>&time=<?php echo date($timeFormateForUI, strtotime($value))?>-<?php echo $endTime;?>'"><?php echo date($timeFormateForUI, strtotime($value))?></span>
                              </div>
                            <?php endif; ?>
                          <?php endif;?>
                        <?php endforeach; ?>
                        <?php if($day === 'mon' && count($this->monday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php 
                        if($day === 'tue' && count($this->tuesday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'wed' && count($this->wednesday) == 0): ?>
                          <div class="item_slots_closed"><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'thu' && count($this->thursday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'fri' && count($this->friday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'sat' && count($this->saturday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'sun' && count($this->sunday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Closed') ?></div> 
                        <?php endif; ?>
                  <?php endforeach;?>
                    </div>
    <?php endforeach;?>
	</div>
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscribers");?></h2>
	</div>

	<div class="profile_mentor_subscribers_listing">

<?php foreach( $this->paginator as $item ) : ?>
    <?php
      $itemArray = $item->toArray();
      $parent_id = $itemArray['parent_id']; $viewer = Engine_Api::_()->user()->getViewer();
          $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
    $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->getIdentity()));
          $this->viewrTimezone = $viewer->getIdentity() ? $viewer->timezone: 'Europe/Moscow';
    $this->timeFrameValue = $timeFrameValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.bookingtimeframe",'3');


    if($scheduleRow){
      $monday = json_decode($scheduleRow->monday, true);
      $tuesday = json_decode($scheduleRow->tuesday, true);
      $wednesday = json_decode($scheduleRow->wednesday, true);
      $thursday = json_decode($scheduleRow->thursday, true);
      $friday = json_decode($scheduleRow->friday, true);
      $saturday = json_decode($scheduleRow->saturday, true);
      $sunday = json_decode($scheduleRow->sunday, true);
$data = array();
      //$data['demo'] = 'demo';
      if(!empty($monday))
        $data = array_merge($data,$monday);
      if(!empty($tuesday))
        $data = array_merge($data,$tuesday);
      if(!empty($wednesday))
        $data = array_merge($data,$wednesday);
      if(!empty($thursday))
        $data = array_merge($data,$thursday);
      if(!empty($friday))
        $data = array_merge($data,$friday);
      if(!empty($saturday))
        $data = array_merge($data,$saturday);
      if(!empty($sunday))
        $data = array_merge($data,$sunday);
      //unset($data['demo']);
      
      $this->timeSlot ='';
$popAvail = array();
      foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($this->viewrTimezone));
        $d2 = date_format($date2, 'Y-m-d');

        $utcTimeSlot = date_format($date2, 'H:i');
        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);
        
        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){ 
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }

      }
   
      $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = array();
      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = 0;
      foreach ($popAvail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $monday['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tuesday['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wednesday['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thursday['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $friday['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $saturday['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sunday['sun_'.$c7] = $value; 
        }
      }

      $this->monday = $timeSlot['mon'] = $monday;
      $this->tuesday = $timeSlot['tue'] = $tuesday;
      $this->wednesday = $timeSlot['wed'] = $wednesday;
      $this->thursday = $timeSlot['thu'] = $thursday;
      $this->friday = $timeSlot['fri'] = $friday;
      $this->saturday = $timeSlot['sat'] = $saturday;
      $this->sunday = $timeSlot['sun'] = $sunday;

    }
    $this->timeSlot = $timeSlot;
    ?>
    
    <?php foreach ($days as $date => $day):?>
                    
                        <?php foreach ($time as $key => $value) :?>
                          <?php if(in_array($value, $this->timeSlot[$day],TRUE)):?>
                            <?php 
                            $date1 = date_create(null, timezone_open($this->viewrTimezone));
                            $date2 = date_create($date, timezone_open($this->viewrTimezone));
                            date_time_set($date2, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
                            ?>
                            <?php if($date1 < $date2 ): ?>
                              <?php
                              $date3 = date_create($date." ".$value);
                              $startTime = date_format($date3, 'H:i');
                              date_add($date3, date_interval_create_from_date_string($this->duration." seconds"));
                              $endTime = date_format($date3, 'H:i');
                             
                              ?>
                       
                              
                              		<div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Open")?></span></div>
                        							<div class="_time"><b><?php echo $this->translate("Meeting Time:")?></b> <span><?php echo date($timeFormateForUI, strtotime($value))?></span></div>
                        							
                        						</div>
                        						<div class="_btn">
                        						    
                        						        <?php 
                        						        $params = array();
                    						            $params['pro_id'] = $parent_id;
                                                        $params['page'] = 1;
                        						        $zoomMeetingUrl = getMeetingCurlRequest($params);
                        						        
                        						        if(count($zoomMeetingUrl)>0){
                                                            foreach(array_reverse($zoomMeetingUrl) as $key => $value){
                                                            if(1){ 
                                                              $start_time = str_replace('T', ' ', $value->start_time);
                                                              $date1 = date_create($start_time, timezone_open($this->viewrTimezone));
                                                              $date1 = date_timezone_set($date1, timezone_open($this->viewrTimezone));
                                                              $time = date_format($date1, 'H:i');
                                                              $date = date_format($date1, 'Y-m-d');
                                                            ?>
                                                               <a class="smoothbox buttonlink sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" value="<?php echo $value->start_url; ?>" onclick="startMeeting('<?php echo $value->start_url; ?>')">Start Meeting</a>
                                                             <?php 
                                                             }
                                                            }
                                                          }
                                                          ?>
                            							<!--<a href="javascript:void(0);" class="sesbasic_animation custom_btn custom_btn_primary"><span>Start Meeting</span></a>-->
                        						</div>
                        					</div>
                        				</div>
                        				<div class="_right">
                        					<div class="_message">
                        						<h3><?php echo $this->translate("Ooops!")?></h3>
                        						<p class="sesbasic_text_light"><?php echo $this->translate("You dont have any subscriber yet. Once someone subscribe you this meeting time, they will show up here!")?></p>
                        					</div>
                        				</div>
                        			</article>
                        		</div>
                            <?php endif; ?>
                          <?php endif;?>
                        <?php endforeach; ?>
                        <?php if($day === 'mon' && count($this->monday) == 0): ?>
                         <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>
                        <?php endif; ?>
                        <?php 
                        if($day === 'tue' && count($this->tuesday) == 0): ?>
                           <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>
                        <?php endif; ?>
                        <?php if($day === 'wed' && count($this->wednesday) == 0): ?>
 <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>                        <?php endif; ?>
                        <?php if($day === 'thu' && count($this->thursday) == 0): ?>
 <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>                        <?php endif; ?>
                        <?php if($day === 'fri' && count($this->friday) == 0): ?>
 <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>                        <?php endif; ?>
                        <?php if($day === 'sat' && count($this->saturday) == 0): ?>
 <div class="profile_mentor_subscribers_item">
                        			<article>
                        				<div class="_left">
                        					<div class="_leftcont">
                        						<div class="_icon">
                        							<i class="_zoom"></i>
                        						</div>
                        						<div class="_leftinfo">
                        							<div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                        						</div>
                        					</div>
                        				</div>
                        			</article>
                        		</div>                        
                        		<?php endif; ?>
                                <?php if($day === 'sun' && count($this->sunday) == 0): ?>
                                <div class="profile_mentor_subscribers_item">
                                    <article>
                                        <div class="_left">
                                        <div class="_leftcont">
                                        <div class="_icon">
                                        <i class="_zoom"></i>
                                        </div>
                                        <div class="_leftinfo">
                                        <div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_open"><?php echo $this->translate("Closed")?></span></div>
                                        </div>
                                        </div>
                                        </div>
                                    </article>
                                </div>                        
                        		<?php endif; ?>
                  <?php endforeach;?>
		

<?php endforeach;?>
	</div>
</div>

<?php

  function getMeetingCurlRequest($postFields){
        // Curl request for create zoom url
        //
        
        $ch = curl_init();
        $postUrl = "https://".$_SERVER['HTTP_HOST']."/zoom/get-zoom-meeting-url.php";
        
        curl_setopt($ch, CURLOPT_URL,$postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,
        // "servicebooking_id=value1&postvar2=value2&postvar3=value3");
        
        // In real life you should use something like:
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                 http_build_query($postFields));
        
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        
        
        curl_close ($ch);
        
        $res = json_decode($server_output);
        
        // Further processing ...
           return $res;
  }
  ?>