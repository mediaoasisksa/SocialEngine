<?php

$date = date("Y-m-d");
$day = strtolower(substr(date("l", strtotime($date)),0,3));
$days[$date] = $day;
$timeFrameValue = number_format($this->timeFrameValue)*7;
for($i = 0;$i < $timeFrameValue-1; $i++){
$date = date('Y-m-d', strtotime($date. ' + 1 day'));
$day = strtolower(substr(date("l", strtotime($date)),0,3));
$days[$date] = $day;
}

$duration = $this->duration;
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

<?php $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
$options['locale'] = $this->locale()->getLocale();
 
if (is_object($options['locale'])) {
    $locale = $options['locale']->__toString();
} else {
  $locale = (string) $options['locale'];
}
if (strlen($locale) < 5) {
  $locale = Zend_Locale::getBrowser();
  if (is_array($locale)) {
    foreach ($locale as $browserLocale => $q) {
      if (strlen($browserLocale) >= 5) {
        $locale = $browserLocale;
        break;
      }
    }
  }
  if (!$locale || !is_string($locale) || strlen($locale) < 5) {
    $locale = 'en_US';
  }
}

$currency = new Zend_Currency($unit, $locale);
$symbol = $currency->getSymbol();
?>

<div class="headline">
  <h2>
  <?php echo $this->translate('Services');?>
  </h2>

  <?php if( count($this->navigation) > 0 ): ?>
  <div class="tabs">
    <?php 
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
  <?php endif; ?>
</div>
<div class="sitebooking_book_service layout_middle booked_listings">
  <div>
  <h3 class="_heading">Booking Service</h3>
<ul class=" sitebooking_list sb_common">
    <?php foreach( $this->service as $item ): ?>
            <li class="_list">
          <div class="_inner">
			  
      <div class='services_browse_photo _left' >
		 <div class="_img"> <?php echo $this->itemBackgroundPhoto($item, 'thumb.normal') ?></div>
 		</div>
			  
		
				  
      <div class='_right' id='service_info'>
		  
		        <div class='services_browse_info_title _name'>
        <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?> </h3>
      </div>
		  
		        <div class='services_browse_info_date' style="display: inline-block; width: 100%;">
        <?php echo $this->itemPhoto(Engine_Api::_()->getItem('sitebooking_pro',$item->parent_id), 'thumb.icon'); ?>
       <span> <?php echo $this->translate('By');?>
        <?php echo $item->provider_title; ?></span>
      </div>
		  
    <div class="_price">
		<span class="_pricevalue"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></span></div>
		  

      <!-- <div style="display: none">
		  <span class="_parentlabel" ><?php // echo $this->translate('Timezone').' : ' ?> </span>
        <span><?php // echo $item->timezone ?></span>
      </div> -->
		  
      <!-- <div id="appt_title" style="display: none;" class="_parentlabel"><?php //echo $this->translate('Appointment Date:') ?></div>   
      </div> -->
				  
				 
			  </div>
    </li>
    <?php endforeach; ?>
  </ul>
    </div>
</div>

<div class="tip">
  <?php if(count($this->timeSlot) == 0): ?>
  <span> 
    <?php echo $this->translate('Time duration is not available. Please book another service.')?>
    <?php echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','reset' => true,), $this->translate('Browse services'), array('class' => '',)); ?>
  </span>
  <?php return ; 
  endif; ?>
</div>
<div class="layout_middle">
<div class="provider_availibility">

<!--Calendar-->
  <div id="avalable_calendar">

  <div class="_timeslots">
  <div class="_dirarrows">
    <a class="_left"  id="previous"><i class="fa fa-angle-left"></i><span>Previous</span></a>
    <a class="_right"  id="next"><span>Next</span><i class="fa fa-angle-right"></i></a>
    </div>
    <?php foreach ($days as $date => $day) :?>
    <div class="_columns" id="<?php echo $date; ?>">
    <div class="_heading"><?php echo $date ?><br><?php echo $day ?></div>
    <div class="_time">
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
        <span>
          <input type="checkbox" class="time" id="<?php echo $date.'_'.$key ?>" name="<?php echo $day.'_'.$key; ?>" value="<?php echo $date.','.$startTime.'-'.$endTime; ?>"  />
          <label for="<?php echo $date.'_'.$key ?>"><span><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
        </span>
        <?php endif; ?>
      <?php endif;?>
      <?php endforeach; ?>
    </div> 
    <?php if($day === 'mon' && count($this->monday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php 
    if($day === 'tue' && count($this->tuesday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php if($day === 'wed' && count($this->wednesday) == 0): ?>
    <div class="sb_closed_days"><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php if($day === 'thu' && count($this->thursday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php if($day === 'fri' && count($this->friday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php if($day === 'sat' && count($this->saturday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    <?php if($day === 'sun' && count($this->sunday) == 0): ?>
    <div class="sb_closed_days" ><?php echo $this->translate('Closed') ?></div> 
    <?php endif; ?>
    </div>
  <?php endforeach; ?>
  </div> 
  </div>
  
  <button id="time_button" style="display: none" >Continue</button>
  <div id ="desc_contact" style="display: none"> 
  <form id="booking_form" class="global_form" name="booking_form" method="POST" action="<?php echo $this->url();?>">
	  <div class="form-elements">
		  <div class="form-wrapper">
    <div class="form-label"><label for="problem_desc" class="required"><?php echo $this->translate('Share more details about this booking (300 Characters)') ?></label></div>
    <div id="description-element" class="form-element">
    <textarea name="problem_desc" id="problem_desc" cols="45" rows="6"></textarea>
    </div>
    <div class="form-label"><label for="telephone_no" class="required"><?php echo $this->translate('Telephone No.') ?></label></div>
    <div id="description-element" class="form-element">
    <input type="text" name="telephone_no" id="telephone_no" ></input>
    <p class="description" style="max-width: 100%;"><?php echo $this->translate('This field accept phone no in following format (+91-1234567890 or +911234567890 or 1234567890)') ?></p>
    <span id='error_msg' style=" display: none; color: red; margin-top: 6px;"><?php echo $this->translate('Invalid Format, please enter valid telephone number.') ?></span>
    </div>
	  </div>
	  
  </div>
  <input type="hidden" id="servicing_date" name="servicing_date" value="">
  <input type="hidden" id="total_charges" name="total_charges" value="">
  <input type="hidden" id="duration" name="duration" value="">
	</div>
  </form>
  <button id="form_submit" type="button" style="display: none" name="continue" value="continue">Continue</button>
</div>
</div>


<script type="text/javascript">
  en4.core.runonce.add(function(){
  var t = '<?php echo $this->timeFrameValue; ?>';
  if( t == 1){
    scriptJquery('#next').css('visibility','hidden');
  }
  
  var count = 0;
  <?php foreach ($days as $date => $day): ?>
    count++;
    if(count > 7){
    scriptJquery('#<?php echo $date ?>').css('display','none');
    }
  <?php endforeach ?>
  scriptJquery('#previous').css('visibility','hidden');



  scriptJquery('.time').on('click',function() {
    
    var count = 0;
    var timeSlot ='';
    var list = document.getElementById('avalable_calendar').getElementsByTagName("INPUT");
    for (var i = 0; i < list.length; i++) {
    if(list[i].type == "checkbox" && list[i].checked == true){
      if(timeSlot == '')
      timeSlot = list[i].value;
      else
      timeSlot = timeSlot+"$"+list[i].value;
      count++;
    }
    }
    var duration = {};
    if(timeSlot != ''){
    var timeSlot = timeSlot.split("$");
    var w = timeSlot[0].split(",")
    var dur = w[1];
    duration[w[0]] = dur;
    for (var i = 0; i < timeSlot.length-1; i++) {
      var x = timeSlot[i].split(",")
      var y = timeSlot[i+1].split(",")
      if(x[0] == y[0]){
      dur = dur+","+y[1];
      duration[y[0]] = dur;
      }else{
      var dur = y[1];
      duration[y[0]] = dur;
      }
    }
    var ser = Object.keys(duration); 
    var servicingDate = ser[0];
    for (var i = 1; i < ser.length; i++) {
      servicingDate = servicingDate+","+ser[i];
    }
    console.log(servicingDate);
    if(scriptJquery('#price'))
      scriptJquery('#price').remove();

    var h = document.createElement("P");
    h.className = "_price";
    h.id = 'price';
    document.getElementById('service_info').appendChild(h);
    h.innerHTML = "<span class='_parentlabel'>Total Amount: </span>"+"<?php echo $symbol; ?>"+<?php echo $this->service[0]->price?>*count;

    if(scriptJquery('#appt_title'))
      scriptJquery('#appt_title').remove();
    var h = document.createElement("P");
    h.className = "_parentlabel";
    h.id = 'appt_title';
    document.getElementById('service_info').appendChild(h);
    h.innerHTML = 'Appointments Date & Time:'

    var t = scriptJquery('.servicing_date_time');
    for (var i = 0; i < t.length; i++) {
      t[i].remove();
    }

    for (x in duration) {
      var p = document.createElement("DIV");
      p.innerHTML = "<div class='_inforow' style='display: block;'><span class='_label'>Date: </span>"+x+"</div><div class='_inforow'><span class='_label'>Time: </span>"+duration[x].replace(/,/g, " | ");+"</div>";
      p.className = "servicing_date_time service_apmnt_info _statusinfo";
      document.getElementById('service_info').appendChild(p);
    }
    
    
    scriptJquery('#time_button').css('display','block');
    }
    else{
      if(scriptJquery('#appt_title'))
        scriptJquery('#appt_title').remove();
      scriptJquery('#time_button').css('display','none');
      var t = scriptJquery('.servicing_date_time');
      for (var i = 0; i < t.length; i++) {
        t[i].remove();
      }
      if(scriptJquery('#price'))
        scriptJquery('#price').remove();
    }   
    scriptJquery('#duration').val(JSON.stringify(duration));
    scriptJquery('#servicing_date').val(servicingDate);
    scriptJquery('#total_charges').val(<?php echo $this->service[0]->price?>*count);
  });

  scriptJquery('#time_button').on('click',function(){
    scriptJquery('#avalable_calendar').css('display','none');
    scriptJquery('#time_button').css('display','none');
    scriptJquery('#desc_contact').css('display','block');
    scriptJquery('#form_submit').css('display','block');
    var myElement = scriptJquery(document.body);
    // var myFx = new Fx.Scroll(myElement).set(0, 0.2 * document.body.offsetHeight);
  });

  scriptJquery('#next').on('click',function(){
    var count = 0;
    <?php foreach ($days as $date => $day): ?>
    
    if(scriptJquery('#<?php echo $date ?>').css("display") != "none"){
      scriptJquery('#<?php echo $date ?>').css('display','none');
      count = '<?php echo $date ?>';
    }
    <?php endforeach ?>
    
    var date = new Date(count);
    for (var i = 0; i < 7; i++) {
    date.setDate(date.getDate() + 1);
    var year = date.getFullYear()+"";
    var month = (date.getMonth()+1)+"";
    if(month.length < 2){
      month = "0"+month;
    }
    var day = date.getDate()+"";
    if(day.length < 2){
      day = "0"+day;
    }
    var dateFormat = year + "-" + month + "-" + day;
    
    scriptJquery('#' + dateFormat).css('display','inline-block');
    
    }
    scriptJquery('#previous').css('visibility','hidden');
    <?php end($days); ?>  
    var lastDate = '<?php echo key($days); ?>';
    if(dateFormat == lastDate){
    scriptJquery('#next').css('visibility','hidden');
    }
  });

  scriptJquery('#previous').on('click',function(){
    var count = 0;
    <?php foreach ($days as $date => $day): ?>
    if(scriptJquery('#<?php echo $date ?>').css('display') != "none"){
      scriptJquery('#<?php echo $date ?>').css('display','none');
      count = '<?php echo $date ?>';
    }
    <?php endforeach ?>
    var date = new Date(count);
    date.setDate(date.getDate() - 6);
    for (var i = 0; i < 7; i++) {
    date.setDate(date.getDate() - 1);
    var year = date.getFullYear()+"";
    var month = (date.getMonth()+1)+"";
    if(month.length < 2){
      month = "0"+month;
    }
    var day = date.getDate()+"";
    if(day.length < 2){
      day = "0"+day;
    }
    var dateFormat = year + "-" + month + "-" + day;
    scriptJquery('#'+dateFormat).css('display','inline-block');
    }
    var startDate = '<?php echo date("Y-m-d") ?>';
    if(dateFormat == startDate){
    scriptJquery('#previous').css('visibility','hidden');
    }
    scriptJquery('#next').css('visibility','visible');
  });
  scriptJquery('#form_submit').on('click',function(){
    var desc = document.forms["booking_form"]["problem_desc"].value;
    document.forms["booking_form"]["problem_desc"].value = desc.substring(0, 300);
    var tel = document.forms["booking_form"]["telephone_no"].value;
    var telRGEX = /^[+|1-9][0-9]{1,4}[-|1-9][0-9]{4,18}$/;
    var phoneResult = telRGEX.test(tel);
    if(phoneResult == true){
      document.getElementById("booking_form").submit();
    }else{
      scriptJquery('#error_msg').css('display','block');
    }
  });
  });
</script>