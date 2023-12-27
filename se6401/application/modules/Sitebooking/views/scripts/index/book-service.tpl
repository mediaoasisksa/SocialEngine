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
  <?php if($this->message):?>
     <div class="tip">
         <span >
             <?php echo $this->translate($this->message);?>
         </span>
     </div>
   <?php endif;?>
<ul class=" sitebooking_list sb_common">
    <?php foreach( $this->service as $item ): ?>
            <li class="_list">
          <div class="_inner">
			  
      <div class='services_browse_photo _left' >
		 <div class="_img"> <?php echo $this->itemBackgroundPhoto($item->getOwner(), 'thumb.profile') ?></div>
 		</div>
			  
		
				  
      <div class='_right' id='service_info'>
		  
		        <div class='services_browse_info_title _name'>
		            <?php
                  
                        $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($item->getOwner());
                        //print_r($values->toArray());die;
                        // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
                        foreach($values->toArray() as $value) {
                            if($value['field_id'] == 7) {
                                $fname = $value['value'];
                            } else if($value['field_id'] == 33) {
                                $fname = $value['value'];
                            } elseif($value['field_id'] == 8) {
                                $lname = $value['value'];
                            } else if($value['field_id'] == 34) {
                                $lname = $value['value'];
                            }
                        }
                    ?>
    				<h3><?php echo $this->htmlLink($item->getOwner()->getHref(), $fname . ' ' . $lname) ?></h3>
      
		        
      </div>
		  
    <div class="_price">
        <?php if($item->type == 2):?>
		<span class="_pricevalue"><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $item['price']; ?> / <?php echo $this->translate("Monthly") ?></span></div>
		 <?php else:?>
		 		<span class="_pricevalue"><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $item['price']; ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></span></div>

		 <?php endif;?>

				  
				 
			  </div>
			  <div id="timezone_user" class="_timezone" style="margin-top: 10px;margin-left: 10px;">
        <b><?php echo $this->translate('Timezone').': ' ?></b>
        <a><?php  echo $this->viewer()->getIdentity() ? $this->viewer()->timezone : 'Europe/Moscow'; ?><a  class="button smoothbox timezone_user"  style=" margin-top: 10px;margin-left: 10px;" href="members/settings/timezone?>"><?php echo $this->translate('Change Timezone')?></a></div>
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
  <div id="avalable_calendar" style="display:none;">

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
  <div id ="desc_contact" style="display: block;"> 
  <form id="booking_form" class="global_form" name="booking_form" method="POST" action="<?php echo $this->url();?>">
	  <div class="form-elements">
		  <div class="form-wrapper">
    <div class="form-label"><label for="problem_desc" class="optional"><?php echo $this->translate('Share more details about this booking (300 Characters)') ?></label></div>
    <div id="description-element" class="form-element">
    <textarea name="problem_desc" id="problem_desc" cols="45" rows="6"></textarea>
    </div>
    <div class="form-label"><label for="telephone_no" class="required"><?php echo $this->translate('Telephone No.') ?></label></div>
    <div id="description-element" class="form-element">
    <input type="text" name="telephone_no" id="telephone_no" ></input>

    <span id='error_msg' style=" display: none; color: red; margin-top: 6px;"><?php echo $this->translate('Invalid Format, please enter valid telephone number.') ?></span>
    </div>
    
<!--    <div id="timezone-label" class="form-label"><label for="timezone" class="optional">Timezone</label></div>--}}-->
<!--<div id="timezone-element" class="form-element">--}}-->
<!--<select name="timezone" id="timezone">--}}-->
<!--<option value="Africa/Abidjan">Africa/Abidjan</option><option value="Africa/Accra">Africa/Accra</option><option value="Africa/Addis_Ababa">Africa/Addis_Ababa</option><option value="Africa/Algiers">Africa/Algiers</option><option value="Africa/Asmara">Africa/Asmara</option><option value="Africa/Bamako">Africa/Bamako</option><option value="Africa/Bangui">Africa/Bangui</option><option value="Africa/Banjul">Africa/Banjul</option><option value="Africa/Bissau">Africa/Bissau</option><option value="Africa/Blantyre">Africa/Blantyre</option><option value="Africa/Brazzaville">Africa/Brazzaville</option><option value="Africa/Bujumbura">Africa/Bujumbura</option><option value="Africa/Cairo">Africa/Cairo</option><option value="Africa/Casablanca">Africa/Casablanca</option><option value="Africa/Ceuta">Africa/Ceuta</option><option value="Africa/Conakry">Africa/Conakry</option><option value="Africa/Dakar">Africa/Dakar</option><option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option><option value="Africa/Djibouti">Africa/Djibouti</option><option value="Africa/Douala">Africa/Douala</option><option value="Africa/El_Aaiun">Africa/El_Aaiun</option><option value="Africa/Freetown">Africa/Freetown</option><option value="Africa/Gaborone">Africa/Gaborone</option><option value="Africa/Harare">Africa/Harare</option><option value="Africa/Johannesburg">Africa/Johannesburg</option><option value="Africa/Kampala">Africa/Kampala</option><option value="Africa/Khartoum">Africa/Khartoum</option><option value="Africa/Kigali">Africa/Kigali</option><option value="Africa/Kinshasa">Africa/Kinshasa</option><option value="Africa/Lagos">Africa/Lagos</option><option value="Africa/Libreville">Africa/Libreville</option><option value="Africa/Lome">Africa/Lome</option><option value="Africa/Luanda">Africa/Luanda</option><option value="Africa/Lubumbashi">Africa/Lubumbashi</option><option value="Africa/Lusaka">Africa/Lusaka</option><option value="Africa/Malabo">Africa/Malabo</option><option value="Africa/Maputo">Africa/Maputo</option><option value="Africa/Maseru">Africa/Maseru</option><option value="Africa/Mbabane">Africa/Mbabane</option><option value="Africa/Mogadishu">Africa/Mogadishu</option><option value="Africa/Monrovia">Africa/Monrovia</option><option value="Africa/Nairobi">Africa/Nairobi</option><option value="Africa/Ndjamena">Africa/Ndjamena</option><option value="Africa/Niamey">Africa/Niamey</option><option value="Africa/Nouakchott">Africa/Nouakchott</option><option value="Africa/Ouagadougou">Africa/Ouagadougou</option><option value="Africa/Porto-Novo">Africa/Porto-Novo</option><option value="Africa/Sao_Tome">Africa/Sao_Tome</option><option value="Africa/Tripoli">Africa/Tripoli</option><option value="Africa/Tunis">Africa/Tunis</option><option value="Africa/Windhoek">Africa/Windhoek</option><option value="America/Adak">America/Adak</option><option value="America/Anchorage">America/Anchorage</option><option value="America/Anguilla">America/Anguilla</option><option value="America/Antigua">America/Antigua</option><option value="America/Araguaina">America/Araguaina</option><option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option><option value="America/Argentina/Catamarca">America/Argentina/Catamarca</option><option value="America/Argentina/Cordoba">America/Argentina/Cordoba</option><option value="America/Argentina/Jujuy">America/Argentina/Jujuy</option><option value="America/Argentina/La_Rioja">America/Argentina/La_Rioja</option><option value="America/Argentina/Mendoza">America/Argentina/Mendoza</option><option value="America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option><option value="America/Argentina/Salta">America/Argentina/Salta</option><option value="America/Argentina/San_Juan">America/Argentina/San_Juan</option><option value="America/Argentina/San_Luis">America/Argentina/San_Luis</option><option value="America/Argentina/Tucuman">America/Argentina/Tucuman</option><option value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option><option value="America/Aruba">America/Aruba</option><option value="America/Asuncion">America/Asuncion</option><option value="America/Atikokan">America/Atikokan</option><option value="America/Bahia">America/Bahia</option><option value="America/Barbados">America/Barbados</option><option value="America/Belem">America/Belem</option><option value="America/Belize">America/Belize</option><option value="America/Blanc-Sablon">America/Blanc-Sablon</option><option value="America/Boa_Vista">America/Boa_Vista</option><option value="America/Bogota">America/Bogota</option><option value="America/Boise">America/Boise</option><option value="America/Cambridge_Bay">America/Cambridge_Bay</option><option value="America/Campo_Grande">America/Campo_Grande</option><option value="America/Cancun">America/Cancun</option><option value="America/Caracas">America/Caracas</option><option value="America/Cayenne">America/Cayenne</option><option value="America/Cayman">America/Cayman</option><option value="America/Chicago">America/Chicago</option><option value="America/Chihuahua">America/Chihuahua</option><option value="America/Costa_Rica">America/Costa_Rica</option><option value="America/Cuiaba">America/Cuiaba</option><option value="America/Curacao">America/Curacao</option><option value="America/Danmarkshavn">America/Danmarkshavn</option><option value="America/Dawson">America/Dawson</option><option value="America/Dawson_Creek">America/Dawson_Creek</option><option value="America/Denver">America/Denver</option><option value="America/Detroit">America/Detroit</option><option value="America/Dominica">America/Dominica</option><option value="America/Edmonton">America/Edmonton</option><option value="America/Eirunepe">America/Eirunepe</option><option value="America/El_Salvador">America/El_Salvador</option><option value="America/Fortaleza">America/Fortaleza</option><option value="America/Glace_Bay">America/Glace_Bay</option><option value="America/Godthab">America/Godthab</option><option value="America/Goose_Bay">America/Goose_Bay</option><option value="America/Grand_Turk">America/Grand_Turk</option><option value="America/Grenada">America/Grenada</option><option value="America/Guadeloupe">America/Guadeloupe</option><option value="America/Guatemala">America/Guatemala</option><option value="America/Guayaquil">America/Guayaquil</option><option value="America/Guyana">America/Guyana</option><option value="America/Halifax">America/Halifax</option><option value="America/Havana">America/Havana</option><option value="America/Hermosillo">America/Hermosillo</option><option value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option><option value="America/Indiana/Knox">America/Indiana/Knox</option><option value="America/Indiana/Marengo">America/Indiana/Marengo</option><option value="America/Indiana/Petersburg">America/Indiana/Petersburg</option><option value="America/Indiana/Tell_City">America/Indiana/Tell_City</option><option value="America/Indiana/Vevay">America/Indiana/Vevay</option><option value="America/Indiana/Vincennes">America/Indiana/Vincennes</option><option value="America/Indiana/Winamac">America/Indiana/Winamac</option><option value="America/Inuvik">America/Inuvik</option><option value="America/Iqaluit">America/Iqaluit</option><option value="America/Jamaica">America/Jamaica</option><option value="America/Juneau">America/Juneau</option><option value="America/Kentucky/Louisville">America/Kentucky/Louisville</option><option value="America/Kentucky/Monticello">America/Kentucky/Monticello</option><option value="America/La_Paz">America/La_Paz</option><option value="America/Lima">America/Lima</option><option value="America/Los_Angeles">America/Los_Angeles</option><option value="America/Maceio">America/Maceio</option><option value="America/Managua">America/Managua</option><option value="America/Manaus">America/Manaus</option><option value="America/Marigot">America/Marigot</option><option value="America/Martinique">America/Martinique</option><option value="America/Matamoros">America/Matamoros</option><option value="America/Mazatlan">America/Mazatlan</option><option value="America/Menominee">America/Menominee</option><option value="America/Merida">America/Merida</option><option value="America/Mexico_City">America/Mexico_City</option><option value="America/Miquelon">America/Miquelon</option><option value="America/Moncton">America/Moncton</option><option value="America/Monterrey">America/Monterrey</option><option value="America/Montevideo">America/Montevideo</option><option value="America/Montreal">America/Montreal</option><option value="America/Montserrat">America/Montserrat</option><option value="America/Nassau">America/Nassau</option><option value="America/New_York">America/New_York</option><option value="America/Nipigon">America/Nipigon</option><option value="America/Nome">America/Nome</option><option value="America/Noronha">America/Noronha</option><option value="America/North_Dakota/Center">America/North_Dakota/Center</option><option value="America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option><option value="America/Ojinaga">America/Ojinaga</option><option value="America/Panama">America/Panama</option><option value="America/Pangnirtung">America/Pangnirtung</option><option value="America/Paramaribo">America/Paramaribo</option><option value="America/Phoenix">America/Phoenix</option><option value="America/Port_of_Spain">America/Port_of_Spain</option><option value="America/Port-au-Prince">America/Port-au-Prince</option><option value="America/Porto_Velho">America/Porto_Velho</option><option value="America/Puerto_Rico">America/Puerto_Rico</option><option value="America/Rainy_River">America/Rainy_River</option><option value="America/Rankin_Inlet">America/Rankin_Inlet</option><option value="America/Recife">America/Recife</option><option value="America/Regina">America/Regina</option><option value="America/Resolute">America/Resolute</option><option value="America/Rio_Branco">America/Rio_Branco</option><option value="America/Santa_Isabel">America/Santa_Isabel</option><option value="America/Santarem">America/Santarem</option><option value="America/Santiago">America/Santiago</option><option value="America/Santo_Domingo">America/Santo_Domingo</option><option value="America/Sao_Paulo">America/Sao_Paulo</option><option value="America/Scoresbysund">America/Scoresbysund</option><option value="America/Shiprock">America/Shiprock</option><option value="America/St_Barthelemy">America/St_Barthelemy</option><option value="America/St_Johns">America/St_Johns</option><option value="America/St_Kitts">America/St_Kitts</option><option value="America/St_Lucia">America/St_Lucia</option><option value="America/St_Thomas">America/St_Thomas</option><option value="America/St_Vincent">America/St_Vincent</option><option value="America/Swift_Current">America/Swift_Current</option><option value="America/Tegucigalpa">America/Tegucigalpa</option><option value="America/Thule">America/Thule</option><option value="America/Thunder_Bay">America/Thunder_Bay</option><option value="America/Tijuana">America/Tijuana</option><option value="America/Toronto">America/Toronto</option><option value="America/Tortola">America/Tortola</option><option value="America/Vancouver">America/Vancouver</option><option value="America/Whitehorse">America/Whitehorse</option><option value="America/Winnipeg">America/Winnipeg</option><option value="America/Yakutat">America/Yakutat</option><option value="America/Yellowknife">America/Yellowknife</option><option value="Antarctica/Casey">Antarctica/Casey</option><option value="Antarctica/Davis">Antarctica/Davis</option><option value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option><option value="Antarctica/Mawson">Antarctica/Mawson</option><option value="Antarctica/McMurdo">Antarctica/McMurdo</option><option value="Antarctica/Palmer">Antarctica/Palmer</option><option value="Antarctica/Rothera">Antarctica/Rothera</option><option value="Antarctica/South_Pole">Antarctica/South_Pole</option><option value="Antarctica/Syowa">Antarctica/Syowa</option><option value="Antarctica/Vostok">Antarctica/Vostok</option><option value="Arctic/Longyearbyen">Arctic/Longyearbyen</option><option value="Asia/Aden">Asia/Aden</option><option value="Asia/Almaty">Asia/Almaty</option><option value="Asia/Amman">Asia/Amman</option><option value="Asia/Anadyr">Asia/Anadyr</option><option value="Asia/Aqtau">Asia/Aqtau</option><option value="Asia/Aqtobe">Asia/Aqtobe</option><option value="Asia/Ashgabat">Asia/Ashgabat</option><option value="Asia/Baghdad">Asia/Baghdad</option><option value="Asia/Bahrain">Asia/Bahrain</option><option value="Asia/Baku">Asia/Baku</option><option value="Asia/Bangkok">Asia/Bangkok</option><option value="Asia/Beirut">Asia/Beirut</option><option value="Asia/Bishkek">Asia/Bishkek</option><option value="Asia/Brunei">Asia/Brunei</option><option value="Asia/Choibalsan">Asia/Choibalsan</option><option value="Asia/Chongqing">Asia/Chongqing</option><option value="Asia/Colombo">Asia/Colombo</option><option value="Asia/Damascus">Asia/Damascus</option><option value="Asia/Dhaka">Asia/Dhaka</option><option value="Asia/Dili">Asia/Dili</option><option value="Asia/Dubai">Asia/Dubai</option><option value="Asia/Dushanbe">Asia/Dushanbe</option><option value="Asia/Gaza">Asia/Gaza</option><option value="Asia/Harbin">Asia/Harbin</option><option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option><option value="Asia/Hong_Kong">Asia/Hong_Kong</option><option value="Asia/Hovd">Asia/Hovd</option><option value="Asia/Irkutsk">Asia/Irkutsk</option><option value="Asia/Jakarta">Asia/Jakarta</option><option value="Asia/Jayapura">Asia/Jayapura</option><option value="Asia/Jerusalem">Asia/Jerusalem</option><option value="Asia/Kabul">Asia/Kabul</option><option value="Asia/Kamchatka">Asia/Kamchatka</option><option value="Asia/Karachi">Asia/Karachi</option><option value="Asia/Kashgar">Asia/Kashgar</option><option value="Asia/Kathmandu">Asia/Kathmandu</option><option value="Asia/Kolkata">Asia/Kolkata</option><option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option><option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option><option value="Asia/Kuching">Asia/Kuching</option><option value="Asia/Kuwait">Asia/Kuwait</option><option value="Asia/Macau">Asia/Macau</option><option value="Asia/Magadan">Asia/Magadan</option><option value="Asia/Makassar">Asia/Makassar</option><option value="Asia/Manila">Asia/Manila</option><option value="Asia/Muscat">Asia/Muscat</option><option value="Asia/Nicosia">Asia/Nicosia</option><option value="Asia/Novokuznetsk">Asia/Novokuznetsk</option><option value="Asia/Novosibirsk">Asia/Novosibirsk</option><option value="Asia/Omsk">Asia/Omsk</option><option value="Asia/Oral">Asia/Oral</option><option value="Asia/Phnom_Penh">Asia/Phnom_Penh</option><option value="Asia/Pontianak">Asia/Pontianak</option><option value="Asia/Pyongyang">Asia/Pyongyang</option><option value="Asia/Qatar">Asia/Qatar</option><option value="Asia/Qyzylorda">Asia/Qyzylorda</option><option value="Asia/Rangoon">Asia/Rangoon</option><option value="Asia/Riyadh">Asia/Riyadh</option><option value="Asia/Sakhalin">Asia/Sakhalin</option><option value="Asia/Samarkand">Asia/Samarkand</option><option value="Asia/Seoul">Asia/Seoul</option><option value="Asia/Shanghai">Asia/Shanghai</option><option value="Asia/Singapore">Asia/Singapore</option><option value="Asia/Taipei">Asia/Taipei</option><option value="Asia/Tashkent">Asia/Tashkent</option><option value="Asia/Tbilisi">Asia/Tbilisi</option><option value="Asia/Tehran">Asia/Tehran</option><option value="Asia/Thimphu">Asia/Thimphu</option><option value="Asia/Tokyo">Asia/Tokyo</option><option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option><option value="Asia/Urumqi">Asia/Urumqi</option><option value="Asia/Vientiane">Asia/Vientiane</option><option value="Asia/Vladivostok">Asia/Vladivostok</option><option value="Asia/Yakutsk">Asia/Yakutsk</option><option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option><option value="Asia/Yerevan">Asia/Yerevan</option><option value="Atlantic/Azores">Atlantic/Azores</option><option value="Atlantic/Bermuda">Atlantic/Bermuda</option><option value="Atlantic/Canary">Atlantic/Canary</option><option value="Atlantic/Cape_Verde">Atlantic/Cape_Verde</option><option value="Atlantic/Faroe">Atlantic/Faroe</option><option value="Atlantic/Madeira">Atlantic/Madeira</option><option value="Atlantic/Reykjavik">Atlantic/Reykjavik</option><option value="Atlantic/South_Georgia">Atlantic/South_Georgia</option><option value="Atlantic/St_Helena">Atlantic/St_Helena</option><option value="Atlantic/Stanley">Atlantic/Stanley</option><option value="Australia/Adelaide">Australia/Adelaide</option><option value="Australia/Brisbane">Australia/Brisbane</option><option value="Australia/Broken_Hill">Australia/Broken_Hill</option><option value="Australia/Currie">Australia/Currie</option><option value="Australia/Darwin">Australia/Darwin</option><option value="Australia/Eucla">Australia/Eucla</option><option value="Australia/Hobart">Australia/Hobart</option><option value="Australia/Lindeman">Australia/Lindeman</option><option value="Australia/Lord_Howe">Australia/Lord_Howe</option><option value="Australia/Melbourne">Australia/Melbourne</option><option value="Australia/Perth">Australia/Perth</option><option value="Australia/Sydney">Australia/Sydney</option><option value="Europe/Amsterdam">Europe/Amsterdam</option><option value="Europe/Andorra">Europe/Andorra</option><option value="Europe/Athens">Europe/Athens</option><option value="Europe/Belgrade">Europe/Belgrade</option><option value="Europe/Berlin">Europe/Berlin</option><option value="Europe/Bratislava">Europe/Bratislava</option><option value="Europe/Brussels">Europe/Brussels</option><option value="Europe/Bucharest">Europe/Bucharest</option><option value="Europe/Budapest">Europe/Budapest</option><option value="Europe/Chisinau">Europe/Chisinau</option><option value="Europe/Copenhagen">Europe/Copenhagen</option><option value="Europe/Dublin">Europe/Dublin</option><option value="Europe/Gibraltar">Europe/Gibraltar</option><option value="Europe/Guernsey">Europe/Guernsey</option><option value="Europe/Helsinki">Europe/Helsinki</option><option value="Europe/Isle_of_Man">Europe/Isle_of_Man</option><option value="Europe/Istanbul">Europe/Istanbul</option><option value="Europe/Jersey">Europe/Jersey</option><option value="Europe/Kaliningrad">Europe/Kaliningrad</option><option value="Europe/Kiev">Europe/Kiev</option><option value="Europe/Lisbon">Europe/Lisbon</option><option value="Europe/Ljubljana">Europe/Ljubljana</option><option value="Europe/London">Europe/London</option><option value="Europe/Luxembourg">Europe/Luxembourg</option><option value="Europe/Madrid">Europe/Madrid</option><option value="Europe/Malta">Europe/Malta</option><option value="Europe/Mariehamn">Europe/Mariehamn</option><option value="Europe/Minsk">Europe/Minsk</option><option value="Europe/Monaco">Europe/Monaco</option><option value="Europe/Moscow">Europe/Moscow</option><option value="Europe/Oslo">Europe/Oslo</option><option value="Europe/Paris">Europe/Paris</option><option value="Europe/Podgorica">Europe/Podgorica</option><option value="Europe/Prague">Europe/Prague</option><option value="Europe/Riga">Europe/Riga</option><option value="Europe/Rome">Europe/Rome</option><option value="Europe/Samara">Europe/Samara</option><option value="Europe/San_Marino">Europe/San_Marino</option><option value="Europe/Sarajevo">Europe/Sarajevo</option><option value="Europe/Simferopol">Europe/Simferopol</option><option value="Europe/Skopje">Europe/Skopje</option><option value="Europe/Sofia">Europe/Sofia</option><option value="Europe/Stockholm">Europe/Stockholm</option><option value="Europe/Tallinn">Europe/Tallinn</option><option value="Europe/Tirane">Europe/Tirane</option><option value="Europe/Uzhgorod">Europe/Uzhgorod</option><option value="Europe/Vaduz">Europe/Vaduz</option><option value="Europe/Vatican">Europe/Vatican</option><option value="Europe/Vienna">Europe/Vienna</option><option value="Europe/Vilnius">Europe/Vilnius</option><option value="Europe/Volgograd">Europe/Volgograd</option><option value="Europe/Warsaw">Europe/Warsaw</option><option value="Europe/Zagreb">Europe/Zagreb</option><option value="Europe/Zaporozhye">Europe/Zaporozhye</option><option value="Europe/Zurich">Europe/Zurich</option><option value="GMT+02:00">GMT+02:00</option><option value="Indian/Antananarivo">Indian/Antananarivo</option><option value="Indian/Chagos">Indian/Chagos</option><option value="Indian/Christmas">Indian/Christmas</option><option value="Indian/Cocos">Indian/Cocos</option><option value="Indian/Comoro">Indian/Comoro</option><option value="Indian/Kerguelen">Indian/Kerguelen</option><option value="Indian/Mahe">Indian/Mahe</option><option value="Indian/Maldives">Indian/Maldives</option><option value="Indian/Mauritius">Indian/Mauritius</option><option value="Indian/Mayotte">Indian/Mayotte</option><option value="Indian/Reunion">Indian/Reunion</option><option value="Pacific/Apia">Pacific/Apia</option><option value="Pacific/Auckland">Pacific/Auckland</option><option value="Pacific/Chatham">Pacific/Chatham</option><option value="Pacific/Easter">Pacific/Easter</option><option value="Pacific/Efate">Pacific/Efate</option><option value="Pacific/Enderbury">Pacific/Enderbury</option><option value="Pacific/Fakaofo">Pacific/Fakaofo</option><option value="Pacific/Fiji">Pacific/Fiji</option><option value="Pacific/Funafuti">Pacific/Funafuti</option><option value="Pacific/Galapagos">Pacific/Galapagos</option><option value="Pacific/Gambier">Pacific/Gambier</option><option value="Pacific/Guadalcanal">Pacific/Guadalcanal</option><option value="Pacific/Guam">Pacific/Guam</option><option value="Pacific/Honolulu">Pacific/Honolulu</option><option value="Pacific/Johnston">Pacific/Johnston</option><option value="Pacific/Kiritimati">Pacific/Kiritimati</option><option value="Pacific/Kosrae">Pacific/Kosrae</option><option value="Pacific/Kwajalein">Pacific/Kwajalein</option><option value="Pacific/Majuro">Pacific/Majuro</option><option value="Pacific/Marquesas">Pacific/Marquesas</option><option value="Pacific/Midway">Pacific/Midway</option><option value="Pacific/Nauru">Pacific/Nauru</option><option value="Pacific/Niue">Pacific/Niue</option><option value="Pacific/Norfolk">Pacific/Norfolk</option><option value="Pacific/Noumea">Pacific/Noumea</option><option value="Pacific/Pago_Pago">Pacific/Pago_Pago</option><option value="Pacific/Palau">Pacific/Palau</option><option value="Pacific/Pitcairn">Pacific/Pitcairn</option><option value="Pacific/Ponape">Pacific/Ponape</option><option value="Pacific/Port_Moresby">Pacific/Port_Moresby</option><option value="Pacific/Rarotonga">Pacific/Rarotonga</option><option value="Pacific/Saipan">Pacific/Saipan</option><option value="Pacific/Tahiti">Pacific/Tahiti</option><option value="Pacific/Tarawa">Pacific/Tarawa</option><option value="Pacific/Tongatapu">Pacific/Tongatapu</option><option value="Pacific/Truk">Pacific/Truk</option><option value="Pacific/Wake">Pacific/Wake</option><option value="Pacific/Wallis">Pacific/Wallis</option>--}}-->
<!--</select></div>-->

<br />
<div id="sitebooking_payment-wrapper" class="form-wrapper"><div id="sitebooking_payment-label" class="form-label"><label for="sitebooking_payment" class="optional">Choose Payment Option</label></div>
<div id="sitebooking_payment-element" class="form-element"><p class="description">Payment Options?</p>

<ul class="form-options-wrapper">
<li><input type="radio" name="sitebooking_payment" id="sitebooking_payment-mada" value="mada" checked="checked"><label for="sitebooking_payment-mada">Mada Card</label></li>
<li><input type="radio" name="sitebooking_payment" id="sitebooking_payment-card" value="card"><label for="sitebooking_payment-card">Credit / Debit Card</label></li>

</ul>
</div></div>

	  </div>
	  
  </div>
  <input type="hidden" id="servicing_end_date" name="servicing_end_date" value="">
  
  <input type="hidden" id="servicing_date" name="servicing_date" value="">
  <input type="hidden" id="total_charges" name="total_charges" value="">
  <input type="hidden" id="duration" name="duration" value="">
  <input type="hidden" id="timezone" name="timezone" value="Asia/Riyadh">
	</div>
  </form>
  <button id="form_submit" type="button" style="display: block;" name="continue" value="continue">Continue</button>
</div>
</div>


<script type="text/javascript">
  //en4.core.runonce.add(function(){
  var t = '<?php echo $this->timeFrameValue; ?>';
  if( t == 1){
    document.getElementById('next').style.visibility = 'hidden';
  }
  
  var count = 0;
  <?php foreach ($days as $date => $day): ?>
    count++;
    if(count > 7){
    document.getElementById('<?php echo $date ?>').style.display = 'none';
    }
  <?php endforeach ?>
  document.getElementById('previous').style.visibility = 'hidden';



    var count = 0;
    var date = '<?php echo $_GET["date"];?>';
    var time = '<?php echo $_GET["time"];?>';
    var timeSlot =date + ',' + time;

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
    if(jQuery('#price'))
      jQuery('#price').remove();

    var h = document.createElement("p");
    h.className = "_price";
    h.id = 'price';
    jQuery('#service_info').append(h);
    h.innerHTML = "<span class='_parentlabel'>Total Amount: </span>"+"<?php echo $symbol; ?>"+<?php echo $this->service[0]->price?>;

    if(jQuery('appt_title'))
     jQuery('appt_title').remove();
    var h = document.createElement("p");
    h.className = "_parentlabel";
    h.id = 'appt_title';
    jQuery('#service_info').append(h);
    h.innerHTML = 'Appointments Date & Time:'

    var t = jQuery('.servicing_date_time');
    for (var i = 0; i < t.length; i++) {
      t[i].remove();
    }
    var catId = '<?php echo $this->service[0]->type;?>';
    for (x in duration) {
      if(catId == 2) { 
//                 var myDate = new Date(x);
//                 var da = myDate.setMonth( myDate.getMonth() + 1 );
// var result1 = "-to-" + dateFormat1(da.toLocaleDateString(), 'yyyy-MM-dd');


                var dt = new Date(x);
                         dt.setMonth( dt.getMonth() + 1 );
         var result1 = "-to-" + dateFormat1(dt.toLocaleDateString(), 'yyyy-MM-dd');

                var y = x + result1;
                var p = document.createElement("DIV");
      p.innerHTML = "<div class='_inforow' style='display: block;'><span class='_label'>Date: </span>"+y+"</div><div class='_inforow'><span class='_label'>Time: </span>"+duration[x].replace(/,/g, " | ");+"</div>";
      p.className = "servicing_date_time service_apmnt_info _statusinfo";
      jQuery('#service_info').append(p);
      } else {
                var p = document.createElement("DIV");
      p.innerHTML = "<div class='_inforow' style='display: block;'><span class='_label'>Date: </span>"+x+"</div><div class='_inforow'><span class='_label'>Time: </span>"+duration[x].replace(/,/g, " | ");+"</div>";
      p.className = "servicing_date_time service_apmnt_info _statusinfo";
      jQuery('#service_info').append(p);
      }
      



    }
    
    
    document.getElementById('time_button').style.display = "none";
    }
    else{
      if($('appt_title'))
        $('appt_title').remove();
      document.getElementById('time_button').style.display = "none";
      var t = $$('.servicing_date_time');
      for (var i = 0; i < t.length; i++) {
        t[i].remove();
      }
      if($('price'))
        $('price').remove();
    }   
    document.getElementById('duration').value = JSON.stringify(duration);
    document.getElementById('servicing_date').value = servicingDate;
    
    var myDate = new Date(servicingDate);
    if(catId == 2) { 
        
                        
                         myDate.setMonth( myDate.getMonth() + 1 );
        document.getElementById('servicing_end_date').value = dateFormat1(myDate.toLocaleDateString(), 'yyyy-MM-dd');
    }
    document.getElementById('total_charges').value = '<?php echo $this->service[0]->price?>';
  jQuery('#time_button').on('click',function(){
    document.getElementById('avalable_calendar').style.display = "none";
    document.getElementById('time_button').style.display = "none";
    document.getElementById('desc_contact').style.display = "block";
    
    document.getElementById('timezone_user').style.display = "none";
    document.getElementById('form_submit').style.display = "block";
    // var myElement = $(document.body);
    // var myFx = new Fx.Scroll(myElement).set(0, 0.2 * document.body.offsetHeight);
  });
  
  
  if("<?php echo $_GET['time'];?>") {
      document.getElementById('avalable_calendar').style.display = "none";
    document.getElementById('time_button').style.display = "none";
    document.getElementById('desc_contact').style.display = "block";
    
    document.getElementById('timezone_user').style.display = "none";
    document.getElementById('form_submit').style.display = "block";
    // var myElement = $(document.body);
    // var myFx = new Fx.Scroll(myElement).set(0, 0.2 * document.body.offsetHeight);
  }

  jQuery('#next').on('click',function(){
    var count = 0;
    <?php foreach ($days as $date => $day): ?>
    
    if($('<?php echo $date ?>').style.display != "none"){
      $('<?php echo $date ?>').style.display = 'none';
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
    
     document.getElementById(dateFormat).style.display = 'inline-block';
    
    }
    document.getElementById('previous').style.visibility = 'visible';;
    <?php end($days); ?>  
    var lastDate = '<?php echo key($days); ?>';
    if(dateFormat == lastDate){
     document.getElementById('next').style.visibility = 'hidden';
    }
  });

  jQuery('#previous').on('click',function(){
    var count = 0;
    <?php foreach ($days as $date => $day): ?>
    if( document.getElementById('<?php echo $date ?>').style.display != "none"){
       document.getElementById('<?php echo $date ?>').style.display = 'none';
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
     document.getElementById(dateFormat).style.display = 'inline-block';
    }
    var startDate = '<?php echo date("Y-m-d") ?>';
    if(dateFormat == startDate){
    document.getElementById('previous').style.visibility = 'hidden';
    }
    document.getElementById('next').style.visibility = 'visible';
  });
  jQuery('#form_submit').on('click',function(){
    var desc = document.forms["booking_form"]["problem_desc"].value;
    document.forms["booking_form"]["problem_desc"].value = desc.substring(0, 300);
    var tel = document.forms["booking_form"]["telephone_no"].value;
    var telRGEX = /^[+|1-9][0-9]{1,4}[-|1-9][0-9]{4,18}$/;
    var phoneResult = true;
    if(phoneResult == true){
      document.getElementById("booking_form").submit();
    }else{
      document.getElementById('error_msg').style.display = 'block';
    }
  });
  //});
  
  Date.isLeapYear = function (year) { 
    return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)); 
};

Date.getDaysInMonth = function (year, month) {
    return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};

Date.prototype.isLeapYear = function () { 
    return Date.isLeapYear(this.getFullYear()); 
};

Date.prototype.getDaysInMonth = function () { 
    return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
};

Date.prototype.addMonths = function (value) {
    var n = this.getDate();
    this.setDate(1);
    this.setMonth(this.getMonth() + value);
    this.setDate(Math.min(n, this.getDaysInMonth()));
    return this;
};
function dateFormat1(inputDate, format) {
    //parse the input date
    const date = new Date(inputDate);

    //extract the parts of the date
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();    

    //replace the month
    format = format.replace("MM", month.toString().padStart(2,"0"));        

    //replace the year
    if (format.indexOf("yyyy") > -1) {
        format = format.replace("yyyy", year.toString());
    } else if (format.indexOf("yy") > -1) {
        format = format.replace("yy", year.toString().substr(2,2));
    }

    //replace the day
    format = format.replace("dd", day.toString().padStart(2,"0"));

    return format;
}
</script>