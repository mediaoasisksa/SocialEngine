
<?php
$duration = 1800; //$this->duration;

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
  // $d2 = date_format($date, 'H:i');
  $time[] = $d1;
}

for($i = 0;$i<24;$i++)
{
  $d1 = date_format($date,$timeFormate);
  date_add($date, date_interval_create_from_date_string("3600 seconds"));
  // $d2 = date_format($date, 'H:i');
  $timeOptions[] = $d1;
}



?>
<?php if(count($this->serviceRows)>0): ?>
<div class="provider_availibility" id="provider_availibility">
<!--Filters-->
	<form class="_filters" action="" method="POST">
		<ul>
			<li class="_service">
				<label>Select Service</label>
				<!--<select id="sitebooking_services_options" name="ser_id" >-->
				<!--	<?php foreach ($this->serviceRows as $key => $value) : ?>-->
    <!--        <option value="<?php echo $value['ser_id']; ?>" <?php if ($this->ser_id == $value['ser_id']) echo "selected"; ?> ><?php echo $this->translate($value['title']) ?></option>-->
    <!--      <?php endforeach; ?>-->
				<!--</select>-->
				<?php foreach ($this->serviceRows as $key => $value) : ?>
				<input onclick="changeOption('<?php echo $value['ser_id'];?>');" type="radio" id="ser_id_<?php echo $value['ser_id'];?>" name="ser_id" value="<?php echo $value['ser_id']; ?>" <?php if ($this->ser_id == $value['ser_id']) echo "checked"; ?>>
          <label for="ser_id_<?php echo $value['ser_id'];?>"><?php echo $this->translate($value['title']) ?></label>
        <?php endforeach;?>
			</li>
			<li class="_starttime">
				<label>Start Time</label>
				<select name="starttime" class="timerange" id="starttime">
          <?php foreach ($timeOptions as $key => $value) : ?>
					<option value="<?php echo $key ?>" <?php if ($this->starttime == $key) echo "selected"; ?> ><?php echo date($timeFormateForUI, strtotime($value))?></option>
					<?php endforeach; ?>
				</select>
			</li>
			<li class="_endtime">
				<label>End Time</label>
				<select name="endtime" class="timerange" id="endtime">
					<?php foreach ($timeOptions as $key => $value) : ?>
          <option value="<?php echo $key ?>" <?php if ($this->endtime == $key) echo "selected"; ?> ><?php echo date($timeFormateForUI, strtotime($value))?></option>
          <?php endforeach; ?>
				</select>
			</li>
			<li class="_check">
				<div>
					<input id="selectall" type="checkbox">
					<label for="selectall">Select All</label>
				</div>
			</li>
		</ul>

    <img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader">


    <div class="service_availability" id="service_availability">
  		<div class="_selectedname">
        <div class="_categoryname">
          <?php if ($this->servicePhotoId): ?>
            <?php $url = Engine_Api::_()->storage()->get($this->servicePhotoId)->getPhotoUrl(); ?>
          <?php else: ?>
            <?php $url = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_service_profile.png" ?>
          <?php endif; ?>
          <i id="image" style="background-image: url('<?php echo $url; ?>')"></i>
          <span id="service_title"><?php echo $this->serviceTitle ?></span>
        </div>
  			<div class="_timezone">
        <b><?php echo $this->translate('Timezone').': ' ?></b>
        <a><?php  echo $this->providerTimeZone ?><a class="button smoothbox" style="margin-left: 10px;"  href="members/settings/timezone/pro_id/<?php echo $this->pro_id;?>"><?php echo $this->translate('Change Timezone')?></a></div>
  		</div>

      <!--Calendar-->
  	
    	<div class="_timeslots">
    		<div class="_columns">
    			<div class="_heading">Monday</div>
    			<div class="_switchtext" onclick="offDay('mon','mon_offday')">
    				<input type="checkbox" id="mon_offday" class="offday" name="mon_offday" value="mon" <?php if(!count($this->monday)) :?> checked="checked" <?php endif;?> />
    				<label for="mon_offday">Off Day</label>
    			</div>
    			<div class="_time">
            <?php foreach ($time as $key => $value) : ?>
      				<span>
      					<input class="data mon" type="checkbox" id="mon_<?php echo $key; ?>" name="mon_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value,$this->monday, TRUE)) :?> checked="checked" <?php endif;?> <?php if(!count($this->monday)) :?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->monday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?> />
      					<label id="label_mon_<?php echo $key; ?>" for="mon_<?php echo $key; ?>"><span ><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
      				</span>
            <?php endforeach; ?>
    				
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Tuesday</div>
    			<div class="_switchtext" onclick="offDay('tue','tue_offday')">
    				<input type="checkbox" id="tue_offday" class="offday" name="tue_offday" value="tue" <?php if(!count($this->tuesday)):?> checked="checked" <?php endif;?>>
    				<label for="tue_offday">Off Day</label>
    			</div>
    			<div class="_time">
            <?php foreach ($time as $key => $value) : ?>
      				<span>
      					<input class="data tue" type="checkbox" id="tue_<?php echo $key; ?>" name="tue_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->tuesday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->tuesday)):?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->tuesday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?>/>
      					<label id="label_tue_<?php echo $key; ?>" for="tue_<?php echo $key; ?>"><span  ><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
      				</span>
    				<?php endforeach; ?>
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Wednesday</div>
    			<div class="_switchtext" onclick="offDay('wed','wed_offday')">
    				<input type="checkbox" id="wed_offday" class="offday" name="wed_offday" value="wed" <?php if(!count($this->wednesday)):?> checked="checked" <?php endif;?>/>
    				<label for="wed_offday">Off Day</label>
    			</div>
    			<div class="_time">
    				<?php foreach ($time as $key => $value) : ?>
              <span>
    					  <input class="data wed" type="checkbox" id="wed_<?php echo $key; ?>" name="wed_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->wednesday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->wednesday)) :?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->wednesday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?>/>
    					  <label id="label_wed_<?php echo $key; ?>" for="wed_<?php echo $key; ?>"><span><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
    				  </span>
    				<?php endforeach; ?>
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Thursday</div>
    			<div class="_switchtext" onclick="offDay('thu','thu_offday')">
    				<input type="checkbox" id="thu_offday" class="offday" name="thu_offday" value="thu" <?php if(!count($this->thursday)):?> checked="checked" <?php endif;?>/>
    				<label for="thu_offday">Off Day</label>
    			</div>
    			<div class="_time">
    				<?php foreach ($time as $key => $value) : ?>
      				<span>
      					<input class="data thu" type="checkbox" id="thu_<?php echo $key; ?>" name="thu_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->thursday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->thursday)) :?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->thursday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?>/>
      					<label id="label_thu_<?php echo $key; ?>" for="thu_<?php echo $key; ?>"><span><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
      				</span>
            <?php endforeach; ?>
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Friday</div>
    			<div class="_switchtext" onclick="offDay('fri','fri_offday')">
    				<input type="checkbox" id="fri_offday" class="offday" name="fri_offday" value="fri" <?php if(!count($this->friday)):?> checked="checked" <?php endif;?>/>
    				<label for="fri_offday">Off Day</label>
    			</div>
    			<div class="_time">
            <?php foreach ($time as $key => $value) : ?>
      				<span>
      					<input class="data fri" type="checkbox" id="fri_<?php echo $key; ?>" name="fri_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->friday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->friday)):?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->friday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?>/>
      					<label id="label_fri_<?php echo $key; ?>" for="fri_<?php echo $key; ?>"><span><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
      				</span>
            <?php endforeach; ?>				
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Saturday</div>
    			<div class="_switchtext" onclick="offDay('sat','sat_offday')">
    				<input type="checkbox" id="sat_offday" class="offday" name="sat_offday" value="sat" <?php if(!count($this->saturday)):?> checked="checked"  <?php endif;?>/>
    				<label for="sat_offday">Off Day</label>
    			</div>
    			<div class="_time">
            <?php foreach ($time as $key => $value) : ?>
      				<span>
      					<input class="data sat" type="checkbox" id="sat_<?php echo $key; ?>" name="sat_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->saturday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->saturday)):?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->saturday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?>/>
      					<label id="label_sat_<?php echo $key; ?>" for="sat_<?php echo $key; ?>"><span><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
      				</span>
    				<?php endforeach; ?>  
    			</div>	
    		</div>
    		<div class="_columns">
    			<div class="_heading">Sunday</div>
    			<div class="_switchtext" onclick="offDay('sun','sun_offday')">
    				<input type="checkbox" id="sun_offday" class="offday" name="sun_offday" value="sun" <?php if(!count($this->sunday)):?> checked="checked" <?php endif;?>/>
    				<label for="sun_offday">Off Day</label>
    			</div>
    			<div class="_time">
            <?php foreach ($time as $key => $value) : ?>					
    				  <span>
                <input class="data sun" type="checkbox" id="sun_<?php echo $key; ?>" name="sun_<?php echo $key; ?>" value="<?php echo $value; ?>"  <?php if(in_array($value, $this->sunday,TRUE)):?> checked="checked" <?php endif;?> <?php if(!count($this->sunday)):?> disabled="disabled" <?php endif;?> <?php if(in_array($value,$this->sunday1, TRUE)) :?> disabled="disabled" checked1="checked1" <?php endif;?> />
    					  <label id="label_sun_<?php echo $key; ?>" for="sun_<?php echo $key; ?>"><span ><?php echo date($timeFormateForUI, strtotime($value))?></span></label>
    				  </span>
    				<?php endforeach; ?>
    			</div>	
    		</div>
    	</div>
    </div>
  	<div class="_save"><button type="submit" name="save" value="save">Save</button></div>
  </form>
</div>
<?php else: ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('You have not created any service yet.');?>
      </span>
    </div>
<?php endif; ?>


<script type="text/javascript">
  
 //en4.core.runonce.add(function() {
    $('.sitebooking_main_provider_manage').parent().addClass('active');

  $('#selectall').on('click',function(){
    var all = $('.data');
    if(this.checked == true){
      for (var i = 0; i < all.length; ++i) {
        if(document.getElementById('label_'+all[i].id).style.display == 'none'){
          all[i].checked = false; 
        }else{
          all[i].checked = true;
        }
      }
    }
    else{
      for (var i = 0; i < all.length; ++i) { 
        all[i].checked = false; 
      }
    }

  });

  $('.timerange').on('change',function(){
    var starttime = $('#starttime').val();
    var endtime = $('#endtime').val();
    var time=[];
    var y1 = starttime*2;
    var y2 = endtime*2;
      <?php foreach ($time as $key => $value): ?>
        var z=i='<?php echo $key ?>';
      if(y2-y1 > 0) {
        if(z >= y1 && z <= y2 ){
          document.getElementById('label_mon_'+z).style.display = "block";
          document.getElementById('label_tue_'+z).style.display = "block";
          document.getElementById('label_wed_'+z).style.display = "block";
          document.getElementById('label_thu_'+z).style.display = "block";
          document.getElementById('label_fri_'+z).style.display = "block";
          document.getElementById('label_sat_'+z).style.display = "block";
          document.getElementById('label_sun_'+z).style.display = "block";

          if(document.getElementById('selectall').checked == true){
            document.getElementById('mon_'+z).checked = true;
            document.getElementById('tue_'+z).checked = true;
            document.getElementById('wed_'+z).checked = true;
            document.getElementById('thu_'+z).checked = true;
            document.getElementById('fri_'+z).checked = true;
            document.getElementById('sat_'+z).checked = true;
            document.getElementById('sun_'+z).checked = true;
          }
        }
        else{
          document.getElementById('label_mon_'+z).style.display = "none";
          document.getElementById('label_tue_'+z).style.display = "none";
          document.getElementById('label_wed_'+z).style.display = "none";
          document.getElementById('label_thu_'+z).style.display = "none";
          document.getElementById('label_fri_'+z).style.display = "none";
          document.getElementById('label_sat_'+z).style.display = "none";
          document.getElementById('label_sun_'+z).style.display = "none";
          
          if(document.getElementById('selectall').checked == true){
            document.getElementById('mon_'+z).checked = false;
            document.getElementById('tue_'+z).checked = false;
            document.getElementById('wed_'+z).checked = false;
            document.getElementById('thu_'+z).checked = false;
            document.getElementById('fri_'+z).checked = false;
            document.getElementById('sat_'+z).checked = false;
            document.getElementById('sun_'+z).checked = false;
          }
        }
      } else {
        if(z >= y1 && z >= y2 || z <= y1 && z <= y2){
          document.getElementById('label_mon_'+z).style.display = "block";
          document.getElementById('label_tue_'+z).style.display = "block";
          document.getElementById('label_wed_'+z).style.display = "block";
          document.getElementById('label_thu_'+z).style.display = "block";
          document.getElementById('label_fri_'+z).style.display = "block";
          document.getElementById('label_sat_'+z).style.display = "block";
          document.getElementById('label_sun_'+z).style.display = "block";

          if(document.getElementById('selectall').checked == true){
            document.getElementById('mon_'+z).checked = true;
            document.getElementById('tue_'+z).checked = true;
            document.getElementById('wed_'+z).checked = true;
            document.getElementById('thu_'+z).checked = true;
            document.getElementById('fri_'+z).checked = true;
            document.getElementById('sat_'+z).checked = true;
            document.getElementById('sun_'+z).checked = true;
          }
        }
        else{
          document.getElementById('label_mon_'+z).style.display = "none";
          document.getElementById('label_tue_'+z).style.display = "none";
          document.getElementById('label_wed_'+z).style.display = "none";
          document.getElementById('label_thu_'+z).style.display = "none";
          document.getElementById('label_fri_'+z).style.display = "none";
          document.getElementById('label_sat_'+z).style.display = "none";
          document.getElementById('label_sun_'+z).style.display = "none";

          if(document.getElementById('selectall').checked == true){
            document.getElementById('mon_'+z).checked = false;
            document.getElementById('tue_'+z).checked = false;
            document.getElementById('wed_'+z).checked = false;
            document.getElementById('thu_'+z).checked = false;
            document.getElementById('fri_'+z).checked = false;
            document.getElementById('sat_'+z).checked = false;
            document.getElementById('sun_'+z).checked = false;
        
          }
        }
      }
      <?php endforeach ?>;
  });

  $('#sitebooking_services_options').on('change', function(){

    var clist=document.getElementsByClassName("data");
    for (var i = 0; i < clist.length; ++i) { 
      clist[i].checked = false; 
    }
    (new Request.HTML({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'available'), 'default', true) ?>',
      'data' : {
        'format' : 'html',
        'ser_id' : this.value,
        'pro_id' : <?php echo $this->pro_id; ?>,
        'isAjax' : '1'
      },
      onRequest: function () {
        $('loader').show();
      },  
      onSuccess: function(responseJSON, responseText,responseHTML) {
        $('loader').hide();
        var element = new Element('div', {
            'html': responseHTML
        });

        $('service_availability').innerHTML = element.getElement('.service_availability').innerHTML;
        $('starttime').selectedIndex = 0;
        $('endtime').selectedIndex = 0;
      }     
    })).send();
  });
//});

    function changeOption(val) {

        window.location.href= "/se6401/bookings/providers/available/<?php echo $this->pro_id;?>?isAjax=1&ser_id="+val;
    // var clist=document.getElementsByClassName("data");
    // for (var i = 0; i < clist.length; ++i) { 
    //   clist[i].checked = false; 
    // }
    // (new Request.HTML({
    //   'format': 'json',
    //   'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'available'), 'default', true) ?>',
    //   'data' : {
    //     'format' : 'html',
    //     'ser_id' : val,
    //     'pro_id' : <?php echo $this->pro_id; ?>,
    //     'isAjax' : '1'
    //   },
    //   onRequest: function () {
    //     $('loader').show();
    //   },  
    //   onSuccess: function(responseJSON, responseText,responseHTML) {
    //     $('loader').hide();
    //     var element = new Element('div', {
    //         'html': responseHTML
    //     });

    //     $('service_availability').innerHTML = element.getElement('.service_availability').innerHTML;
    //     $('starttime').selectedIndex = 0;
    //     $('endtime').selectedIndex = 0;
    //   }     
    // })).send();
 // });
    }

  function offDay(className,id) {
    var slot = $('.'+className);
    if(document.getElementById(id).checked == true){
      for (var i = 0; i < slot.length; ++i) { 
        slot[i].disabled = true; 
      }
    }else{
      for (var i = 0; i < slot.length; ++i) { 
        slot[i].disabled = false;
        
        if(jQuery('#'+ className + '_' +i).attr("checked1") == "checked1") {
            jQuery('#'+ className + '_' +i).attr("disabled", true);
        }
      }
    }
    // console.log('#'+id);
    // if(jQuery('#'+id).attr("checked1") == "checked1"){
    //   for (var i = 0; i < slot.length; ++i) { 
    //     slot[i].disabled = true; 
    //   }
    // }
  }
</script>
