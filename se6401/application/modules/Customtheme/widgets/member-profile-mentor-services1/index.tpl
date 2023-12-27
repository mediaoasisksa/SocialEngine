<?php if($this->approved):?>
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
    <?php 
    
    if($this->subject()->getIdentity() != $this->dataMentor['owner_id']): ?>
<div class="sesbasic_bxs profile_mentor_subscriptions">
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscriptions");?></h2>
	</div>
	<div class="profile_mentor_subscriptions_listing">
		        <?php $subsribe=0; foreach( $this->paginator as $item ) : ?>
    		     <?php  $itemArray = $item->toArray(); $parent_id = $itemArray['parent_id'];
                    $params = array();
                    $params['ser_id'] = $item->getIdentity(); $params['groupby'] = 1;
                    $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                ?>
                <?php foreach($bookedItems as $bookingItem):?>
                    <?php $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);?>
                    
                    <?php if($user->getIdentity() == Engine_Api::_()->user()->getViewer()->getIdentity()):?>
                   
                    <?php $subsribe = $subsribe+1;?>
                        <div class="profile_mentor_subscriptions_item">
                        <article>
                            <div class="_left">
                                <div class="itemthumb">
                                    <span class="_userimg"><img src="<?php echo $item->getOwner()->getPhotoUrl() ?  $item->getOwner()->getPhotoUrl() : '/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></span>
                                    
                                    
                                    <?php
                                        $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($item->getOwner());
                                        //print_r($values->toArray());die;
                                        // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
                                        foreach($values->toArray() as $value) {
                            if($value['field_id'] == 36) {
                                $fname = $value['value'];
                            } else if($value['field_id'] == 47) {
                                $fname = $value['value'];
                            } elseif($value['field_id'] ==37) {
                                $lname = $value['value'];
                            } else if($value['field_id'] == 48) {
                                $lname = $value['value'];
                            }
                        }
                                    ?>
                    				<span class="_username"><a href="<?php echo $item->getOwner()->getHref();?>"><?php echo $this->htmlLink($item->getOwner()->getHref(), $fname . ' ' . $lname) ?></a></span>
                                </div>
                                <div class="iteminfo">
                                    <span class="category custom_tag"><a href="/pages/mentor-service?category_id=<?php echo $item->category_id;?>"><?php echo $this->translate(Engine_Api::_()->getItemTable('sitebooking_category')->getCategoryName($item->category_id));?></a></span>
                                    <?php if(strtotime($bookingItem->servicing_end_date) > strtotime(date("Y-m-d"))):?>
                                    <div class="_subcription">
                                        <span><?php echo $this->translate("Subscription:");?></span>
                                        <span class="_active"><?php echo $this->translate("Active");?></span>
                                    </div>
                                    <div class="_valid">
                                        <span class="sesbasic_text_light"><?php echo $this->translate("Valid till:");?></span>
                                        <span class="_date"><?php echo $bookingItem->servicing_end_date;?></span>
                                    </div>
                                    <?php else:?>
                                     <div class="_subcription">
                                        <span><?php echo $this->translate("Subscription:");?></span>
                                        <span class="_expired"><?php echo $this->translate("Expired");?></span>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </div>
                            <div class="_right">
                                <div class="_rightcont">
                                    <div class="_icon">
                                        <i class="_zoom"></i>
                                    </div>
                                    <!--<div class="_rightinfo">-->
                                    <!--    <div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_online"><?php echo $this->translate("Online")?></span></div>-->
                                    <!--    <div class="_time"><b><?php echo $this->translate("Meeting Time:")?></b> <span><?php  $dd = json_decode($bookingItem->duration, true);?> <?php echo $dd[$bookingItem->servicing_date];?></span></div>-->
                                    <!--</div>-->
                                    
                                        
                                         <?php 
            						        $params = array();
            					            $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
                                            $params['page'] = 1;
            						        $zoomMeetingUrl = getMeetingCurlRequest($params);
            						        
                                         ?>
                                         
                                         <!--<a href="javascript:void(0);" class="sesbasic_animation custom_btn custom_btn_primary"><span>Join Meeting</span></a>-->
    `                                       <?php 
                                              $zoomM = 0;
                                              if($zoomMeetingUrl>0){
                                                foreach(array_reverse($zoomMeetingUrl) as $key => $value){
                                                    $user_ids = json_decode($value->user_ids, true);
                                                    $servicebooking_ids = json_decode($value->servicebooking_ids, true);
                                                    //print_r($user_ids);
                                                    
                                                ?>
                                                <?php $servicebooking = Engine_Api::_()->getItem('sitebooking_servicebooking', $value->servicebooking_id);?>
                                                 <?php $ser = Engine_Api::_()->getItem('sitebooking_ser', $servicebooking->ser_id);
                                                 if($ser->type != 2) {
                                                    continue;
                                                 }
                                                 ?>
                                                <?php
                                            if(($ser->type == 2 && $user_ids[Engine_Api::_()->user()->getViewer()->getIdentity()] == Engine_Api::_()->user()->getViewer()->getIdentity()) && ($servicebooking_ids[$bookingItem->servicebooking_id] == $bookingItem->servicebooking_id) ){ $zoomM++;

                                                //echo $value->id;
                                                //print_r($value->start_time);
                                                $raw_data = json_decode($value->raw_data, true);
                                                foreach($raw_data['occurrences'] as $occurrences) { $currentDate ='';$currenttime=''; $day1 = '';
                                                    //print_r($occurrences['start_time']);die;
                                                   $start_time = str_replace('T', ' ', $value->start_time);
                                                  $date1 = date_create($start_time, timezone_open('UTC'));
                                                  $date1 = date_timezone_set($date1, timezone_open(Engine_Api::_()->user()->getViewer()->timezone));
                                                  $time = date_format($date1, 'H:i');
                                                  
                                                  
                                                  $start_time = str_replace('T', ' ', $occurrences['start_time']);
                                                  $date1 = date_create($start_time, timezone_open('UTC'));
                                                  $date1 = date_timezone_set($date1, timezone_open(Engine_Api::_()->user()->getViewer()->timezone));
                                                  $date = date_format($date1, 'Y-m-d');
                                                 
                                                //   if($date == date('Y-m-d')) {
                                                //       $currentDate = $date;
                                                //       $currenttime = $time;
                                                //   }
                                                  
                                                  if($date == date('Y-m-d')) {
                                                      $currentDate = $date;
                                                      $currenttime = $time;
                                                  } else {
                                                      $currentDate = $bookingItem->servicing_date;
                                                      $currenttime = $time;
                                                  }
                                                }
                                                  
                                                ?>
                                                <div class="_rightinfo">
                                                    <?php if($currentDate == date('Y-m-d')):?>
                                                    <div class="_status">
                                                    <b><?php echo $this->translate("Today:")?></b> <span class="_open" style="color:green"><?php echo $this->translate("Online")?></span>
                                                    </div>
                                                    
                                                    <?php else:?>
                                                    <div class="_status">
                                                    <b><?php echo $this->translate("Today:")?></b> <span class="_close" style="color:red"><?php echo $this->translate("Offline")?></span>
                                                    </div>
                                                    <?php endif;?>
                                                    <?php if($currenttime):?>
                                                    <div class="_time"><b><?php echo $this->translate("Meeting Time:");?></b>
                                                    <span><?php echo $currenttime; ?></span>
                                                    </div>
                                                    <?php endif;?>
                                                </div>
                                                <?php if($currentDate == date('Y-m-d')):?>
                                                 <div class="_btn"><a class="  sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" value="<?php echo $value->join_url; ?>" onclick="startMeeting('<?php echo $value->join_url; ?>')"><span><?php echo $this->translate("Join Meeting")?></span></a></div>
                                                 <?php else:?>
                                                  <div class="_btn">
                                                            <a class="sesbasic_animation custom_btn custom_btn_primary disabled" style="cursor: pointer;" href="javascript:void(0);"><span><?php echo $this->translate("Join Meeting")?></span></a>
                                                        </div>
                                                 <?php endif;?>
                                                 <?php 
                                                 }
                                                }
                                              } ?>
                                              <?php if($zoomM == 0):?>
                                               <br /><br />
                                                <div class="tip">
                                                    <span>
                                                        <?php echo $this->translate("Your request yet to be approved for zoom meeting.");?>
                                                    </span>
                                                </div>
                                                <?php endif;?>
                                </div>
                            </div>
                        </article>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
		     <?php endforeach;?>
		     <?php if($subsribe == 0):?>
		        <div class="tip"><span style="margin-left: 12px;"><?php echo($this->translate("You don't have any subscription yet."));?></span></div>
		     <?php endif;?>
	</div>
</div>
<?php endif;?>

<!--Mentor Subscribers-->


<div class="sesbasic_bxs profile_mentor_subscribers">
    <?php 
    
    if(($this->subject()->getIdentity() == $this->dataMentor['owner_id']) && (Engine_Api::_()->user()->getViewer()->getIdentity() != $this->dataMentor['owner_id'])): ?>
	<div class="profile_mentor_subscribers_top">
		<div class="_head">
    	    <span class="custom_btn custom_btn_sec custom_btn_rounded"><span><?php echo $this->translate("Booking Slots")?></span></span>
  	    </div>
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
              <?php endforeach;?>
            </div>
        <?php endforeach;?>
	</div>
	<?php endif;?>
	
	<?php 
    
    if(Engine_Api::_()->user()->getViewer()->getIdentity() == $this->dataMentor['owner_id']): ?>
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscribers");?></h2>
	</div>
	<div class="profile_mentor_subscribers_listing">
	    
	    <?php foreach( $this->paginator as $item ) : ?>
	    
                <?php
                    $itemArray = $item->toArray();
                    $parent_id = $itemArray['parent_id']; $viewer = Engine_Api::_()->user()->getViewer();

                    
                ?>
                    <?php 
                                        $params = array();
                                        $params['ser_id'] = $item->getIdentity();
                                        
                                        $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                                    ?>
                          <?php if($bookedItems->getTotalItemCount() > 0 ):?>            
                          <?php foreach($bookedItems as $itemBook): ?>
                          <?php 
                                        $params = array();
                                        $params['pro_id'] = $parent_id;
                                        $params['page'] = 1;
                                        $zoomMeetingUrl = getMeetingCurlRequest($params);
                                        $zoomMee = 0;
                                        ?>
                        <?php if($zoomMeetingUrl>0):?>
                          <div class="profile_mentor_subscribers_item">
                            <article>
                                <div class="_left">
                                    <div class="_leftcont">
                                        <?php 
                                            foreach(array_reverse($zoomMeetingUrl) as $key => $value){
                                                if( $value->servicebooking_id == $itemBook->servicebooking_id ){ 
                                                $zoomMee++;
                                                // $start_time = str_replace('T', ' ', $value->start_time);
                                                // $date1 = date_create($start_time, timezone_open('UTC'));
                                                // $date1 = date_timezone_set($date1, timezone_open(Engine_Api::_()->user()->getViewer()->timezone));
                                                // $time = date_format($date1, 'H:i');
                                                // $date = date_format($date1, 'Y-m-d');
                                                $currentDate ='';$currenttime=''; $day1 = '';
                                                //print_r($value->start_time);
                                                $raw_data = json_decode($value->raw_data, true);
                                                foreach($raw_data['occurrences'] as $occurrences) {
                                                    //print_r($occurrences['start_time']);die;
                                                   $start_time = str_replace('T', ' ', $value->start_time);
                                                  $date1 = date_create($start_time, timezone_open('UTC'));
                                                  $date1 = date_timezone_set($date1, timezone_open(Engine_Api::_()->user()->getViewer()->timezone));
                                                  $time = date_format($date1, 'H:i');
                                                  
                                                  
                                                  $start_time = str_replace('T', ' ', $occurrences['start_time']);
                                                  $date1 = date_create($start_time, timezone_open('UTC'));
                                                  $date1 = date_timezone_set($date1, timezone_open(Engine_Api::_()->user()->getViewer()->timezone));
                                                  $date = date_format($date1, 'Y-m-d');
                                                 
                                                  if($date == date('Y-m-d')) {
                                                      $currentDate = $date;
                                                      $currenttime = $time;
                                                  } else {
                                                      $currentDate = $itemBook->servicing_date;
                                                      $currenttime = $time;
                                                  }
                                                }
                                                ?> 
                                                <div class="_icon">
                                                    <i class="_zoom"></i>
                                                </div>
                                                <div class="_leftinfo">
                                                    <div class="_status">
                                                        <?php echo $currentDate; ?>
                                                    </div>
                                                    <div class="_time"><?php echo $this->translate("Meeting Time:");?>
                                                        <?php echo $currenttime; ?>
                                                    </div>
                                                    <?php if($currentDate == date('Y-m-d')):?>
                                                        <div class="_btn">
                                                            <a class="  sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" value="<?php echo $value->start_url; ?>" onclick="startMeeting('<?php echo $value->start_url; ?>')"><span><?php echo $this->translate("Start Meeting")?></span></a>
                                                        </div>
                                                    <?php else:?>
                                                        <div class="_btn">
                                                            <a class="sesbasic_animation custom_btn custom_btn_primary disabled" style="cursor: pointer;" href="javascript:void(0);"><span><?php echo $this->translate("Start Meeting")?></span></a>
                                                        </div>
                                                    <?php endif;?>
                                                </div>
                                                <?php 
                                                }
                                            }
                                        ?>	
                                        <?php if($zoomMee ==0):?>
                                            <div class="tip">
                                                <span>
                                                    <?php echo $this->translate("Your zoom account is approved. But, meeting request yet to be approve by Admin.");?>
                                                </span>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div class="_right">
                                    <div class="_subscriberslisting">
                                        
                                        <?php 
                                            foreach(array_reverse($zoomMeetingUrl) as $key => $value){
                                                if( $value->servicebooking_id == $itemBook->servicebooking_id ){ ?>
                                                
                                                <?php foreach(json_decode($value->user_ids, true) as $key => $id1):?>
                                                <?php $user = Engine_Api::_()->getItem('user', $id1); ?>
                                                    <div class="_subscriberitem <?php echo $itemBook->servicebooking_id;?>">
                            							 <div class="_thumb">
                                                            <a href="<?php echo $user->getHref()?>"><img src="<?php echo $user->getPhotoUrl() ? $user->getPhotoUrl() : '/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></a>
                                                        </div>
                            						    <div class="_info">
                                                            <?php $fname='';$lname='';
                                                                $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($user);
                                                                //print_r($values->toArray());die;
                                                                // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
                                                                foreach($values->toArray() as $value) {
                                                                    if($value['field_id'] == 36) {
                                                                        $fname = $value['value'];
                                                                    } else if($value['field_id'] == 47) {
                                                                        $fname = $value['value'];
                                                                    } elseif($value['field_id'] ==37) {
                                                                        $lname = $value['value'];
                                                                    } else if($value['field_id'] == 48) {
                                                                        $lname = $value['value'];
                                                                    }
                                                                }
                                                                
                                                                if(!$fname && !$lname) {
                                                                 $name = $user->getTitle();
                                                                } else {
                                                                $name = $fname . ' ' . $lname; 
                                                                }
                                                            ?>
                                                            <div class="_name"><a href="<?php echo $user->getHref();?>"><?php echo $name;?></a></div>
                                                            <?php
                                                            if($itemBook->servicing_date > date("Y-m-d")) {
                                                                $date1=date_create($itemBook->servicing_date);
                                                            } else {
                                                                $date1=date_create(date("Y-m-d"));
                                                            }
                                                            
                                                            $date2=date_create($itemBook->servicing_end_date);
                                                            $diff=date_diff($date2,$date1);
                                                            $daysLeft = $diff->format("%R%a days");
                                                            $daysLeft = str_replace('-', ' ', $daysLeft);
                                                            $daysLeft = str_replace('+', ' ', $daysLeft);
                                                            ?>
                                                            <div class="_days"><?php echo str_replace('-', ' ', $daysLeft);?> left</div>
                                                        </div>
                            						</div>
                                                <?php endforeach;?>
                                        <?php
                                        }
                                        }
                                         ?>
                                         
                                          <?php if($zoomMee ==0):?>
                                            <div class="tip">
                                                <span>
                                                    <?php echo $this->translate("User details will available once requests is approved.");?>
                                                </span>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </article>
                           </div>
                           <?php endif;?>
                         <?php endforeach;?>
                         <?php else:?>
                         <div class="tip">
                                <span>
                                    <?php echo $this->translate("You don't have booking requests yet.");?>
                                </span>
                            </div>
                         <?php endif;?>
        <?php endforeach;?>
	    
</div>
<?php endif;?>
</div>
<?php else:?>
   <div class="tip">
            <span>
                 <?php echo $this->translate("Your zoom account yet to be approved. Please contact with Admin.");?>
            </span>
        </div>

<?php endif;?>
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
  
<script>
  
function startMeeting(url){
  window.open(url,'_blank');
}
</script>
