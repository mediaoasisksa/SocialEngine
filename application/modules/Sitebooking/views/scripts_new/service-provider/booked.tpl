<?php if($this->message == 1): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You are not authorized to access this page.');return;?>
    </span>
  </div>
<?php endif;?>
<?php echo $this->form->render($this); ?>
<div class="booked_service_listings" id="booked_service_listings">
<?php if(count($this->bookedItems) <= 0): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No booking request found matching this criteria.');?>
      </span>
    </div>
  <?php else: ?> 
<div class="sitebooking_dashboard_service booked_listings">
  <h3 class="_heading">Booked Services</h3>
  <ul class=" sitebooking_list sb_common">
    <?php foreach( $this->bookedItems as $item ): ?>          
      <li class="_list">
        <div class="_inner">
          <?php $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$item->ser_id);
          ?>
          <?php if(empty($serviceItem)) : ?>
            <div class="sapps_danger_tip sapps_tip">
              <?php echo $this->translate('You have permanently deleted this service.');?>
            </div>
            <?php continue;?>
          
          <?php elseif($serviceItem->enabled == 0) : ?>
            <div class="sapps_info_tip sapps_tip">
              <?php echo $this->translate('You have disabled this service.');?>
            </div>
          <?php elseif($serviceItem->approved == 0) : ?>
            <div class="sapps_info_tip sapps_tip">
              <?php echo $this->translate('Admin has disabled this service.');?>
            </div>
          <?php endif ;?>
        
          <?php $userItem = Engine_Api::_()->getItem('user',$item->user_id); ?>
          <div class='providers_browse_photo _left'>
            <div class="_img"><?php echo $this->itemBackgroundPhoto($serviceItem, 'thumb.normal') ?></div>
          </div>
          <div class="_right">
            <div class='_info'>
              <div class='services_browse_info_title _name'>
                <h3><?php echo $this->htmlLink($serviceItem->getHref(),$serviceItem->getTitle()) ?></h3>
              </div>
              <div class="_statusinfo">
                 <div class='_price' id="charges">
                    <span class="_pricevalue">
                    <?php echo $this->locale()->toCurrency($item->total_charges,Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?></span>
                   </div>
                  <div class='_date'>
                    <span class="_parentlabel"><?php echo $this->translate('Booked By: ');?></span>
                    <?php echo $this->htmlLink($userItem->getHref(), $userItem->getTitle()); ?>
                    <?php 
                    $date1 = date_create($item->booking_date, timezone_open('UTC'));
                    $date1 = date_timezone_set($date1, timezone_open($this->timezone));
                    echo "on ".date_format($date1, 'F j \, Y');              
                    ?>
                  </div>
                 <div class='_telno'>
                  <span class="_parentlabel"><?php echo $this->translate('Telephone No.: ');?></span>
                  <?php echo $item->telephone_no; ?>
                </div>     
                <div class='providers_browse_info_date' style="overflow: hidden; word-break: break-word; margin-bottom: 5px;">
                  <div class="_parentlabel"><?php echo $this->translate('Appointments Date & Time:');?></div>
                  <?php 
                  $timeDuration = Engine_Api::_()->sitebooking()->timezoneConvert('UTC',$this->timezone,$item->duration);
                  foreach ($timeDuration as $key => $value) : ?>

                  <div class="service_apmnt_info">
                    <div class="_inforow">
                      <span class="_label"><?php echo $this->translate('Date').": " ?></span>
                      <span><?php echo $key ?></span>
                    </div>
                    <div class="_inforow">
                      <span class="_label"><?php echo $this->translate('Time').": " ?></span>
                      <span> <?php echo str_replace(',', ' |', $value); ?></span>
                    </div>
                  </div>
                  <?php endforeach;?>
                </div>
                <div class='_desc'>
                  <div class="_parentlabel"><?php echo $this->translate('Booking Description');?></div>
                  <p class='providers_browse_info_date'><?php echo $item->problem_desc; ?></p>
                </div>
              </div>
              <div class="seaocore_button">
                <?php echo $this->Message($userItem); ?>
                <?php if(Engine_API::_()->seaocore()->isMobile()): ?>
                  <a href="tel:<?php echo $item->telephone_no ?>" class="btn btn-default">Contact</a>
                <?php else:?>
                  <?php
                    echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'index', 'action' => 'contact', 'booking_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Contact'), array(
                      'class' => 'buttonlink smoothbox'
                    ));
                  ?>
                <?php endif;?>
                <?php if($item->status === 'pending'){
              if(count($this->zoomMeetingUrl)>0){
                foreach(array_reverse($this->zoomMeetingUrl) as $key => $value){
                if($value->user_id == $item->user_id && $value->servicebooking_id == $item->servicebooking_id ){ 
                  $start_time = str_replace('T', ' ', $value->start_time);
                  $date1 = date_create($start_time, timezone_open('UTC'));
                  $date1 = date_timezone_set($date1, timezone_open($this->timezone));
                  $time = date_format($date1, 'H:i');
                  $date = date_format($date1, 'Y-m-d');
                ?>
                   <a class="smoothbox buttonlink" style="cursor: pointer;" value="<?php echo $value->start_url; ?>" onclick="startMeeting('<?php echo $value->start_url; ?>')">Start Meeting <?php echo $date.' | '.$time; ?></a>
                 <?php 
                 }
                }
              }
              } ?>
              </div>
            </div>
           
            <div class='_infolabels' id="action_options_<?php echo $item->servicebooking_id; ?>">
              <p>Status:  <?php print_r($item->status )?> </p>
              <?php if($item->status === 'completed'): ?>
              <p class="service_completed">Service Completed</p>
              <?php elseif($item->status === 'rejected'): ?>
              <p class="service_rejected">Service Rejected</p>
              <?php elseif($item->status === 'canceled'): ?>
              <p class="service_canceled">Service Canceled</p>
              <?php elseif($item->status === 'booked'): ?>
             
              <p>(Waiting for admin approval)</p>
              <?php elseif($item->status === 'pending'):?>
              <div class="sb_button_fix">
                <button class="action" id="complete_<?php echo $item->servicebooking_id; ?>" value="complete,<?php echo $item->servicebooking_id; ?>" onclick="changeStatus('complete,<?php echo $item->servicebooking_id; ?>')">Mark as Complete</button>
              </div>
              <?php endif ;?>
            </div>
          </div>   
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php echo $this->paginationControl($this->bookedItems, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  'params' => $this->formValues,
)); ?>
<?php endif; ?>
</div>

<script type="text/javascript">
  
en4.core.runonce.add(function(){

  if ($('booking_date-minute') && $('servicing_date-minute')) {
      $('booking_date-minute').destroy();
      $('servicing_date-minute').destroy();
  }
  if ($('booking_date-ampm') && $('servicing_date-ampm')) {
      $('booking_date-ampm').destroy();
      $('servicing_date-ampm').destroy();
  }
  if ($('booking_date-hour') && $('servicing_date-hour')) {
      $('booking_date-hour').destroy();
      $('servicing_date-hour').destroy();
  }

  if ($('calendar_output_span_booking_date-date')) {
      $('calendar_output_span_booking_date-date').style.display = 'none';
  }

  if ($('calendar_output_span_servicing_date-date')) {
      $('calendar_output_span_servicing_date-date').style.display = 'none';
  }

  if ($('booking_date-date')) {
      $('booking_date-date').setAttribute('type', 'text');
  }

  if ($('servicing_date-date')) {
      $('servicing_date-date').setAttribute('type', 'text');
  }


  $$('.sitebooking_main_provider_manage').getParent().addClass('active');
});
function changeStatus(value){
    var res = value.split(",");
    action_type = res[0];
    var booking_id = res[1];
    var formValues = {
      requestParams:<?php echo json_encode($this->formValues) ?>
    };
    <?php if(!empty($this->formValues) ): ?>
  console.log(<?php echo json_encode($this->formValues) ?>);
      var params = $merge(formValues.requestParams, {
        'format' : 'html',
        'action_type' : action_type,
        'booking_id' : booking_id,
        'isAjax' : '1',
        'pro_id' : <?php echo $this->pro_id ?>,
        'page' : <?php echo sprintf('%d', $this->bookedItems->getCurrentPageNumber()) ?>
      });
    <?php else: ?>
      var params = {
        'format' : 'html',
        'action_type' : action_type,
        'booking_id' : booking_id,
        'isAjax' : '1',
        'pro_id' : <?php echo $this->pro_id ?>,
        'page' : <?php echo sprintf('%d', $this->bookedItems->getCurrentPageNumber()) ?>
      };
    <?php endif; ?>
    
    (new Request.HTML({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'booked'), 'default', true) ?>',
      'data' : params,  
      onSuccess: function(responseJSON, responseText,responseHTML) {

        var element = new Element('div', {
            'html': responseHTML
        });
        if(element.getElement('.booked_service_listings').innerHTML){
          $('booked_service_listings').innerHTML = element.getElement('.booked_service_listings').innerHTML; 
        } 
      }     
    })).send();
  } 
  
//   $(document).ready(function() {
//       $(".start-meeting").on("click", function(event){
//           event.preventDefault();
//           alert('yes');
          
//       });
//   });
  
  function startMeeting(url){
      window.open(url,'_blank');
  }

</script>
