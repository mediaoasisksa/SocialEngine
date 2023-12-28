
<?php

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/owl-carousel/jquery.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/owl-carousel/owl.carousel.js'); ?>
<div class="custom_mentor_listing sesbasic_bxs">
  <?php foreach( $this->paginator as $item ) : ?>
          <?php $itemArray = $item->toArray();
            $parent_id = $itemArray['parent_id']; 
            $data = array();
            $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
            $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->ser_id));
            $viewer = Engine_Api::_()->user()->getViewer();
            if($scheduleRow){
              $monday = json_decode($scheduleRow->monday, true);
              $tuesday = json_decode($scheduleRow->tuesday, true);
              $wednesday = json_decode($scheduleRow->wednesday, true);
              $thursday = json_decode($scheduleRow->thursday, true);
              $friday = json_decode($scheduleRow->friday, true);
              $saturday = json_decode($scheduleRow->saturday, true);
              $sunday = json_decode($scheduleRow->sunday, true);

              $data['demo'] = 'demo';
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

              unset($data['demo']);
            }
            ?>
 <div class="custom_mentor_listing_item">
		<article>
			<div class="custom_mentor_listing_item_left">
				<div class="itemthumb">
				    <?php $url = $item->getOwner()->getPhotoUrl(); $url = $url ? "." . $url : '/application/themes/sescompany/images/nophoto_user_thumb_profile.png'?>
					<a href="<?php echo $item->getOwner()->getHref();?>"><img src="<?php echo $url;?>" alt="" /></a>
				</div>
				<div class="viewbtn">
					<a href="<?php echo $item->getOwner()->getHref();?>" class="sesbasic_animation custom_btn custom_btn_primary"><span><?php echo $this->translate("View Profile");?></span></a>
				</div>
				 <div class="itemfee">
              <span class="_fee"><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $item->price; ?></span><span class="_txt"> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></span>
            </div>
			</div>
			<div class="custom_mentor_listing_item_middle">
				<div class="itemheader">
					<div class="itemtitle">
						<h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
						<span class="category custom_tag"><a href="<?php echo $item->getHref();?>"><?php echo $this->translate(Engine_Api::_()->getItemTable('sitebooking_category')->getCategoryName($item->category_id));?></a></span>
					</div> 
					<div class="itemdesination">
						<?php echo $item->getOwner()->jobtitle;?><?php if($item->getOwner()->qualifications):?> at <b><?php echo $item->getOwner()->qualifications;?></b><?php endif;?>
					</div>
				</div>
				<div class="itemdes">
                    <?php echo $item->description;?>
				</div>
				
				<?php if($viewer && $viewer->getIdentity()):?>
                  <?php
                  
                      $service_id = $item->getIdentity();
                        $table = Engine_Api::_()->getItemTable('sitebooking_schedule');
                        $scheduleRow = $table->fetchRow($table->select()->where('ser_id = ?',$service_id));
                    
                        if($scheduleRow){
                          $monday = json_decode($scheduleRow->monday, true);
                          $tuesday = json_decode($scheduleRow->tuesday, true);
                          $wednesday = json_decode($scheduleRow->wednesday, true);
                          $thursday = json_decode($scheduleRow->thursday, true);
                          $friday = json_decode($scheduleRow->friday, true);
                          $saturday = json_decode($scheduleRow->saturday, true);
                          $sunday = json_decode($scheduleRow->sunday, true);
                    
                          $data['demo'] = 'demo';
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
                    
                          unset($data['demo']);
                    
                          foreach ($data as $key => $value) {
                            $date1 = date_create(null, timezone_open('UTC'));
                            date_time_set($date1, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
                            $d1 =  date_format($date1, 'Y-m-d');
                            $date2 = date_timezone_set($date1, timezone_open($viewer->timezone));
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
                          $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = -1;
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
                    
                          sort($monday);
                          sort($tuesday);
                          sort($wednesday);
                          sort($thursday);
                          sort($friday);
                          sort($saturday);
                          sort($sunday);
                    
                          $this->monday = $monday;
                          $this->tuesday = $tuesday;
                          $this->wednesday = $wednesday;
                          $this->thursday = $thursday;
                          $this->friday = $friday;
                          $this->saturday = $saturday;
                          $this->sunday = $sunday;
                    
                          if(!empty($monday)){
                            $mon = $monday;
                            $tempMon[0] = 0;
                            for ($i=1; $i < sizeof($mon); $i++) { 
                              $startTime = new DateTime($mon[$i-1]);
                              $endTime = new DateTime($mon[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempMon[] = $i-1;
                                $tempMon[] = $i;  
                              }
                            }
                            $tempMon[] = sizeof($mon)-1;
                            $this->mon = $tempMon;
                          }
                    
                          if(!empty($tuesday)){
                            $tue = $tuesday;
                            $tempTue[0] = 0;
                            for ($i=1; $i < sizeof($tue); $i++) { 
                              $startTime = new DateTime($tue[$i-1]);
                              $endTime = new DateTime($tue[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempTue[] = $i-1;
                                $tempTue[] = $i;  
                              }
                            }
                            $tempTue[] = sizeof($tue)-1;
                            $this->tue = $tempTue;
                          }
                    
                          if(!empty($wednesday)){
                            $wed = $wednesday;
                            $tempWed[0] = 0;
                            for ($i=1; $i < sizeof($wed); $i++) { 
                              $startTime = new DateTime($wed[$i-1]);
                              $endTime = new DateTime($wed[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempWed[] = $i-1;
                                $tempWed[] = $i;  
                              }
                            }
                            $tempWed[] = sizeof($wed)-1;
                            $this->wed = $tempWed;
                          }
                    
                          if(!empty($thursday)){
                            $thu = $thursday;
                            $tempThu[0] = 0;
                            for ($i=1; $i < sizeof($thu); $i++) { 
                              $startTime = new DateTime($thu[$i-1]);
                              $endTime = new DateTime($thu[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempThu[] = $i-1;
                                $tempThu[] = $i;  
                              }
                            }
                            $tempThu[] = sizeof($thu)-1;
                            $this->thu = $tempThu;
                          }
                    
                          if(!empty($friday)){
                            $fri = $friday;
                            $tempFri[0] = 0;
                            for ($i=1; $i < sizeof($fri); $i++) { 
                              $startTime = new DateTime($fri[$i-1]);
                              $endTime = new DateTime($fri[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempFri[] = $i-1;
                                $tempFri[] = $i;  
                              }
                            }
                            $tempFri[] = sizeof($fri)-1;
                            $this->fri = $tempFri;
                          }
                    
                          if(!empty($saturday)){
                            $sat = $saturday;
                            $tempSat[0] = 0;
                            for ($i=1; $i < sizeof($sat); $i++) { 
                              $startTime = new DateTime($sat[$i-1]);
                              $endTime = new DateTime($sat[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempSat[] = $i-1;
                                $tempSat[] = $i;  
                              }
                            }
                            $tempSat[] = sizeof($sat)-1;
                            $this->sat = $tempSat;
                          }
                    
                          if(!empty($sunday)){
                            $sun = $sunday;
                            $tempSun[0] = 0;
                            for ($i=1; $i < sizeof($sun); $i++) { 
                              $startTime = new DateTime($sun[$i-1]);
                              $endTime = new DateTime($sun[$i]);
                              $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                              $timeDiff = $duration->format("%H:%I:%S"); 
                    
                              if( $timeDiff != '00:30:00' ){
                                $tempSun[] = $i-1;
                                $tempSun[] = $i;  
                              }
                            }
                            $tempSun[] = sizeof($sun)-1;
                            $this->sun = $tempSun;
                          }
                        }
                        
                        ?>
                        
                        <div class="provider_timing">
    <div class="_info" <?php if(date("D") == "Mon"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?> >
      <div class="_days">Mon</div>
		<div class="_timings">
        <?php $mon = $this->mon;
        if(!empty($mon)){
        for ($i=0; $i < sizeof($mon); $i=$i+2) {
          $monday = $this->monday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$monday[$mon[$i+1]])[0], (int) explode(':',$monday[$mon[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $monday[$mon[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
	  </div>
    <div class="_info" <?php if(date("D") == "Tue"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?>>
       <div class="_days">Tue</div>
		<div class="_timings">
        <?php $tue = $this->tue;
        if(!empty($tue)){
        for ($i=0; $i < sizeof($tue); $i=$i+2) {
          $tuesday = $this->tuesday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$tuesday[$tue[$i+1]])[0], (int) explode(':',$tuesday[$tue[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $tuesday[$tue[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info" <?php if(date("D") == "Wed"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?>>
     <div class="_days">Wed</div>
		<div class="_timings">
        <?php $wed = $this->wed;
        if(!empty($wed)){
        for ($i=0; $i < sizeof($wed); $i=$i+2) {
          $wednesday = $this->wednesday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$wednesday[$wed[$i+1]])[0], (int) explode(':',$wednesday[$wed[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $wednesday[$wed[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info" <?php if(date("D") == "Thu"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?>>
      <div class="_days">Thu</div>
		<div class="_timings">
        <?php $thu = $this->thu;
        if(!empty($thu)){
        for ($i=0; $i < sizeof($thu); $i=$i+2) {
          $thursday = $this->thursday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$thursday[$thu[$i+1]])[0], (int) explode(':',$thursday[$thu[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $thursday[$thu[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info" <?php if(date("D") == "Fri"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?> >
      <div class="_days">Fri</div>
		<div class="_timings">
        <?php $fri = $this->fri;
        if(!empty($fri)){
        for ($i=0; $i < sizeof($fri); $i=$i+2) {
          $friday = $this->friday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$friday[$fri[$i+1]])[0], (int) explode(':',$friday[$fri[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $friday[$fri[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info"  <?php if(date("D") == "Sat"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?>>
     <div class="_days">Sat</div>
		<div class="_timings">
        <?php $sat = $this->sat;
        if(!empty($sat)){
        for ($i=0; $i < sizeof($sat); $i=$i+2) {
          $saturday = $this->saturday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$saturday[$sat[$i+1]])[0], (int) explode(':',$saturday[$sat[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $saturday[$sat[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
		</div>
    </div>
    <div class="_info" <?php if(date("D") == "Sun"):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?>>
      <div class="_days">Sun</div>
		<div class="_timings">
        <?php $sun = $this->sun;
        if(!empty($sun)){
        for ($i=0; $i < sizeof($sun); $i=$i+2) {
          $sunday = $this->sunday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$sunday[$sun[$i+1]])[0], (int) explode(':',$sunday[$sun[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $sunday[$sun[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
	   </div>
    </div>
</div>
<?php endif;?>

                   <?php if(count($data) > 0 && $viewer->getIdentity() != $item->owner_id): ?>
    				<div class="itembtn">
    					<a href="javascript:void(0);"  onclick="window.location.href='bookings/service/book-service/<?php echo $item->getIdentity();?>'" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Subscribe");?></span></a>
    				</div>
    				<?php elseif($viewer->getIdentity() == $item->owner_id):?>
    				<div class="itembtn">
    					<a href="javascript:void(0);"  onclick="window.location.href='/bookings/providers/<?php echo $parent_id;?>/services/service-manage'" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Go to Dashboard");?></span></a>
    				</div>
    				<?php endif;?>
			</div>
			<div class="custom_mentor_listing_item_right">
			    <?php if(count($data) > 0 && $viewer->getIdentity() != $item->owner_id): ?>     
    			<!--	<div class="topbtn">
    					<a href="javascript:void(0);" onclick="window.location.href='bookings/service/book-service/<?php echo $item->getIdentity();?>'" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Join Now");?></span></a>
    				</div>-->
    				<?php elseif($viewer->getIdentity() == $item->owner_id):?>
    				<div class="itembtn">
    					<a href="javascript:void(0);"  onclick="window.location.href='/bookings/providers/<?php echo $parent_id;?>/services/service-manage'" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Go to Dashboard");?></span></a>
    				</div>
    				<?php endif;?>
				<div class="ques">
					<?php echo $this->translate("What can I expect from this mentor?");?>	
				</div>
				<div class="mentorfeature">
					<div class="icons">
						<i class="far fa-comments"></i>
						<i class="far fa-envelope"></i>
						<i class="far fa-comment-dots"></i>
					</div>
					<div class="text">
						<?php echo $this->translate("Unlimited chat, e-mail or text with mentor, within boundaries");?>		
					</div>
				</div>
				<div class="mentorfeature">
					<div class="icons">
						<i class="fas fa-video"></i>
					</div>
					<div class="text">
						<?php echo $this->translate("Multiple Zoom meetings per month");?>		
					</div>
				</div>
			</div>
		</article>
  </div>
  <?php endforeach;?>
<!--  <div class="custom_mentor_listing_item">-->
<!--		<article>-->
<!--			<div class="custom_mentor_listing_item_left">-->
<!--				<div class="itemthumb">-->
<!--					<a href=""><img src="http://eliteadmin.themedesigner.in/demos/bt4/university/dist/images/users/9.jpg" alt="" /></a>-->
<!--				</div>-->
<!--				<div class="viewbtn">-->
<!--					<a href="" class="sesbasic_animation custom_btn custom_btn_primary"><span><?php echo $this->translate("View Profile");?></span></a>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="custom_mentor_listing_item_middle">-->
<!--				<div class="itemheader">-->
<!--					<div class="itemtitle">-->
<!--						<h3><a href="">Blake Harrison</a></h3>-->
<!--						<span class="category custom_tag"><a href=""><?php echo $this->translate("Digital Marketing");?></a></span>-->
<!--					</div>-->
<!--					<div class="itemdesination">-->
<!--						Marketing Manager at <b>MTARGET</b>-->
<!--					</div>-->
<!--				</div>-->
<!--				<div class="itemdes">-->
<!--					The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.-->
<!--				</div>-->
<!--				<div class="itembtn">-->
<!--					<a href="" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Subscribe");?></span></a>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="custom_mentor_listing_item_right">-->
<!--				<div class="topbtn">-->
<!--					<a href="" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Join Now");?></span></a>-->
<!--				</div>-->
<!--				<div class="ques">-->
<!--					<?php echo $this->translate("What can I expect from this mentor?");?>	-->
<!--				</div>-->
<!--				<div class="mentorfeature">-->
<!--					<div class="icons">-->
<!--						<i class="far fa-comments"></i>-->
<!--						<i class="far fa-envelope"></i>-->
<!--						<i class="far fa-comment-dots"></i>-->
<!--					</div>-->
<!--					<div class="text">-->
<!--						<?php echo $this->translate("Unlimited chat, e-mail or text with mentor, within boundaries");?>		-->
<!--					</div>-->
<!--				</div>-->
<!--				<div class="mentorfeature">-->
<!--					<div class="icons">-->
<!--						<i class="fas fa-phone-alt"></i>-->
<!--					</div>-->
<!--					<div class="text">-->
<!--						<?php echo $this->translate("Up to 2 calls per month");?>		-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</article>-->
<!--  </div>-->
<!--  <div class="custom_mentor_listing_item">-->
<!--		<article>-->
<!--			<div class="custom_mentor_listing_item_left">-->
<!--				<div class="itemthumb">-->
<!--					<a href=""><img src="http://eliteadmin.themedesigner.in/demos/bt4/university/dist/images/users/9.jpg" alt="" /></a>-->
<!--				</div>-->
<!--				<div class="viewbtn">-->
<!--					<a href="" class="sesbasic_animation custom_btn custom_btn_primary"><span><?php echo $this->translate("View Profile");?></span></a>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="custom_mentor_listing_item_middle">-->
<!--				<div class="itemheader">-->
<!--					<div class="itemtitle">-->
<!--						<h3><a href="">Blake Harrison</a></h3>-->
<!--						<span class="category custom_tag"><a href=""><?php echo $this->translate("Digital Marketing");?></a></span>-->
<!--					</div>-->
<!--					<div class="itemdesination">-->
<!--						Marketing Manager at <b>MTARGET</b>-->
<!--					</div>-->
<!--				</div>-->
<!--				<div class="itemdes">-->
<!--					The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.-->
<!--				</div>-->
<!--				<div class="itembtn">-->
<!--					<a href="" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Subscribe");?></span></a>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="custom_mentor_listing_item_right">-->
<!--				<div class="topbtn">-->
<!--					<a href="" class="custom_btn custom_btn_sec custom_btn_rounded"><i class="fas fa-star"></i><span><?php echo $this->translate("Join Now");?></span></a>-->
<!--				</div>-->
<!--				<div class="ques">-->
<!--					<?php echo $this->translate("What can I expect from this mentor?");?>	-->
<!--				</div>-->
<!--				<div class="mentorfeature">-->
<!--					<div class="icons">-->
<!--						<i class="far fa-comments"></i>-->
<!--						<i class="far fa-envelope"></i>-->
<!--						<i class="far fa-comment-dots"></i>-->
<!--					</div>-->
<!--					<div class="text">-->
<!--						<?php echo $this->translate("Unlimited chat, e-mail or text with mentor, within boundaries");?>		-->
<!--					</div>-->
<!--				</div>-->
<!--				<div class="mentorfeature">-->
<!--					<div class="icons">-->
<!--						<i class="fas fa-phone-alt"></i>-->
<!--					</div>-->
<!--					<div class="text">-->
<!--						<?php echo $this->translate("Up to 2 calls per month");?>		-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</article>-->
<!--  </div>-->

<!--</div>-->
