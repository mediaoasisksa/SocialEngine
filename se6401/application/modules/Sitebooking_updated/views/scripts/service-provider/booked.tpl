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
              </div>
            </div>
            <div class='_infolabels' id="action_options_<?php echo $item->servicebooking_id; ?>">
              <?php if($item->status === 'completed'): ?>
              <p class="service_completed">Service Completed</p>
              <?php elseif($item->status === 'rejected'): ?>
              <p class="service_rejected">Service Rejected</p>
              <?php elseif($item->status === 'canceled'): ?>
              <p class="service_canceled">Service Canceled</p>
              <?php elseif($item->status === 'booked'): ?>
              <div class="sb_button_fix">
                <button class="action" id="accept_<?php echo $item->servicebooking_id; ?>" value="accept,<?php echo $item->servicebooking_id; ?>" onclick="changeStatus('accept,<?php echo $item->servicebooking_id; ?>')">Accept</button>
                <button class="action" id="reject_<?php echo $item->servicebooking_id; ?>"value="reject,<?php echo $item->servicebooking_id; ?>" onclick="changeStatus('reject,<?php echo $item->servicebooking_id; ?>')">Reject</button>
               </div>
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

  window.addEventListener('DOMContentLoaded', function() {
    const CALENDER_DATE_FORMAT = "dd/mm/yy";
    const startDateEl = scriptJquery('#booking_date-date');
    const endDateEl = scriptJquery('#servicing_date-date');

    startDateEl.attr('type', 'text').datepicker({
      dateFormat: CALENDER_DATE_FORMAT,
      minDate: startDateEl.val()
    }).on('change', function(ev) {
      endDateEl.datepicker('option', 'minDate', startDateEl.val());
    });

    endDateEl.attr('type', 'text').datepicker({
      dateFormat: CALENDER_DATE_FORMAT,
      minDate: endDateEl.val()
    }).on('change', function(ev) {
      startDateEl.datepicker('option', 'maxnDate', endDateEl.val());
    });

  });
  
en4.core.runonce.add(function(){

  if (scriptJquery('#booking_date-minute') && scriptJquery('#servicing_date-minute')) {
      scriptJquery('#booking_date-minute').remove();
      scriptJquery('#servicing_date-minute').remove();
  }
  if (scriptJquery('#booking_date-ampm') && scriptJquery('#servicing_date-ampm')) {
      scriptJquery('#booking_date-ampm').remove();
      scriptJquery('#servicing_date-ampm').remove();
  }
  if (scriptJquery('#booking_date-hour') && scriptJquery('#servicing_date-hour')) {
      scriptJquery('#booking_date-hour').remove();
      scriptJquery('#servicing_date-hour').remove();
  }

  if (scriptJquery('#calendar_output_span_booking_date-date')) {
      scriptJquery('#calendar_output_span_booking_date-date').style.display = 'none';
  }

  if (scriptJquery('#calendar_output_span_servicing_date-date')) {
      scriptJquery('#calendar_output_span_servicing_date-date').style.display = 'none';
  }

  if (scriptJquery('#booking_date-date')) {
      scriptJquery('#booking_date-date').setAttribute('type', 'text');
  }

  if (scriptJquery('#servicing_date-date')) {
      scriptJquery('#servicing_date-date').setAttribute('type', 'text');
  }


  scriptJquery('.sitebooking_main_provider_manage').parents().addClass('active');
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
      var params = scriptJquery.extend(formValues.requestParams, {
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
    
    (scriptJquery.ajax({
      'dataType': 'html',
      'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'booked'), 'default', true) ?>',
      'data' : params,  
      success: function(responseJSON, responseText,responseHTML) {

        var element = scriptJquery.crtEle('div', {
            'html': responseHTML
        });
        if(element.find('.booked_service_listings').html()){
          scriptJquery('#booked_service_listings').html(element.find('.booked_service_listings').html()); 
        } 
      }     
    }));
  } 

</script>
