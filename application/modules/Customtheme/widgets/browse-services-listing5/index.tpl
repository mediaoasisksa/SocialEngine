<?php
$date = date("Y-m-d");
$day = strtolower(substr(date("l", strtotime($date)),0,3));
$days[$date] = $day;
$timeFrameValue = number_format(3)*7;
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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/slick.css'); ?>
<?php $this->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Customtheme/externals/scripts/jquery-slick.js');?>
<?php $this->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Customtheme/externals/scripts/slick.min.js');?>
    

<section class="custom_service_listing_container">
  <div class="enwan-constants">
    <h2><?php echo $this->translate("Learn from expert mentors with training & coaching experiences");?></h2>
    <div class="khat-cons"></div>
    <p style="font-size: 22px;font-weight: 300;width: 900px;margin: 18px auto; "><?php echo $this->translate("Step up your career game plan, prep up interviews, job search &amp; promotion. Find your mentoring sessions and move to a low-cost monthly subscription.");?></p>
  </div>
  <div class="custom_service_listing">
     <?php $itemkey = 1;?>
     <?php if( $this->paginator->getTotalItemCount() > 0): ?>
    <?php foreach( $this->paginator as $item ) : ?>
    <?php
      $itemArray = $item->toArray();
      $parent_id = $itemArray['parent_id']; $viewer = Engine_Api::_()->user()->getViewer();
          $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
    $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->getIdentity()));
          $this->viewrTimezone = $viewer->getIdentity() ? $viewer->timezone: 'Europe/Moscow';
    $this->timeFrameValue = $timeFrameValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.bookingtimeframe",'3');

    if(!$scheduleRow){
        continue;
    }
    if($scheduleRow){
      $monday = json_decode($scheduleRow->monday, true);
      $tuesday = json_decode($scheduleRow->tuesday, true);
      $wednesday = json_decode($scheduleRow->wednesday, true);
      $thursday = json_decode($scheduleRow->thursday, true);
      $friday = json_decode($scheduleRow->friday, true);
      $saturday = json_decode($scheduleRow->saturday, true);
      $sunday = json_decode($scheduleRow->sunday, true);
      
      if(empty($monday) && empty($tuesday) && empty($wednesday) && empty($thursday) && empty($friday) && empty($saturday) && empty($sunday)) {
        continue;
      } 
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
      <div class="custom_service_listing_item custom_service_listing_item_<?php echo $itemkey;?>">
        <article>
          <div class="custom_service_listing_item_left">
            <div class="itemthumb">
              <?php $itemkey++; $url = $item->getOwner()->getPhotoUrl('thumb.profile'); $url = $url ?  $url : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png'?>
              <img src="<?php echo $url;?>" alt="" />
            </div>
            <div class="viewbtn">
              <a href="<?php echo $item->getOwner()->getHref();?>" class="sesbasic_animation custom_btn custom_btn_primary"><span><?php echo $this->translate("View Profile");?></span></a>
            </div> 
            <div class="itemfee">
              <span class="_fee"><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $item['price']; ?></span><span class="_txt"> / <?php echo $this->translate("Month");?></span>
            </div>
          </div> 
          <div class="custom_service_listing_item_middle">
            <div class="itemheader">
              <div class="itemtitle">
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
    				<h3><?php echo $this->htmlLink($item->getOwner()->getHref(), $fname . ' ' . $lname) ?></h3>
    				<span class="category custom_tag"><a href="/pages/mentor-service?category_id=<?php echo $item->category_id;?>"><?php echo $this->translate(Engine_Api::_()->getItemTable('sitebooking_category')->getCategoryName($item->category_id));?></a></span>
    			</div> 
              <div class="itemdesination">
  				      <?php echo $item->getOwner()->jobtitle;?><?php if($item->getOwner()->qualifications):?> at <b><?php echo $item->getOwner()->qualifications;?></b><?php endif;?>
              </div>
            </div>
            <div class="itemdes">
              <?php echo $item->description; ?>
            </div>
            <div class="itembtn">
              <a href="javascript:void(0);" class="custom_btn custom_btn_sec custom_btn_rounded"><span><?php echo $this->translate("Available Slots");?></span></a>
            </div>
            <div class="custom_service_listing_item_slots">
              <?php if(1):?>
                <div class="custom_service_listing_item_days">
                  <div class="slider slider-nav slider-nav-<?php echo $itemkey;?>">
                    <?php foreach ($days as $date => $day):?>
                      <div class="dayitem">
                        <a href="javascript:void(0);">
                          <span class="_date"><?php echo $date ?></span>
                          <span class="_day"><?php echo $day ?></span>
                        </a>
                      </div>
                    <?php endforeach;?>
                  </div>
                </div>  
                <div class="slider slider-for slider-for-<?php echo $itemkey;?>">
                  <?php foreach ($days as $date => $day):?>
                    <div>
                      <div class="custom_service_listing_item_slots_container">
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
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php 
                        if($day === 'tue' && count($this->tuesday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'wed' && count($this->wednesday) == 0): ?>
                          <div class="item_slots_closed"><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'thu' && count($this->thursday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'fri' && count($this->friday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'sat' && count($this->saturday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                        <?php if($day === 'sun' && count($this->sunday) == 0): ?>
                          <div class="item_slots_closed" ><?php echo $this->translate('Not Available') ?></div> 
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach;?>
                </div>
              <?php endif;?>
            </div>
          </div> 

          <div class="custom_service_listing_item_right">
            
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
                <?php echo $this->translate("Multiple Video Conference");?>   
              </div>
            </div>
          </div>
        </article>
      </div>
<script type="text/javascript">
   customjqueryslick('.slider-for-<?php echo $itemkey;?>').slick({
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    adaptiveHeight: true,
    arrows: false,
    fade: false,
    <?php if($orientation = ($this->layout()->orientation == 'right-to-left')){ ?>
      rtl:true,
    <?php } ?>
    asNavFor: '.slider-nav-<?php echo $itemkey;?>'
  });
customjqueryslick('.slider-nav-<?php echo $itemkey;?>').slick({
  infinite: false,
  slidesToShow: 2,
  slidesToScroll: 1,
  asNavFor: '.slider-for-<?php echo $itemkey;?>',
  centerMode: false,  
  dots: false,
  arrows: true,
  focusOnSelect: true,
  <?php if($orientation = ($this->layout()->orientation == 'right-to-left')){ ?>
    rtl:true,
  <?php } ?>
  swipeToSlide: true,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 800,
      settings: {
        arrows: false
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false
      }
    }
  ]
});
</script>
    <?php endforeach;?>
    <?php else:?>
        <div class="tip">
            <span style="margin-left:12px;"><?php echo $this->translate("There is no mentors found.");?></span>
        </div>
    <?php endif;?>
    
    
  </div>        
</section>