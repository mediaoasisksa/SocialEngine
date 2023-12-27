<div class="sitebooking_booked_service booked_listings">
<div>
<h3 class="_heading">Booked Services</h3>

<?php if( $this->bookedItems->getTotalItemCount() > 0 ): ?>
<ul class=" sitebooking_list sb_common">
      <?php foreach( $this->bookedItems as $item ): ?>
        <li class="_list">
          <div class="_inner">
            
          <?php $serviceItem = Engine_Api::_()->getItem('sitebooking_ser',$item->ser_id);
          ?>
          <?php $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$item->pro_id); ?>

          <?php $current = time();
              $bookDate = strtotime($item->booking_date);
              $time = $current-$bookDate;
          ?>
          <?php if($item->getIdentity() == $this->booking_id && $item->status == 'booked' && $time <= 120): ?>
            <div class="sapps_success_tip sapps_tip">
              <b><?php echo $this->translate('Success')."!" ?></b>
              <?php echo $this->translate('This service has been booked successfully.');?>
            </div>
          <?php endif;?>

          <?php if(empty($serviceItem)) : ?>
            <div class="sapps_danger_tip sapps_tip">
              <?php echo $this->translate('Provider has permanently deleted this Service.');?>
            </div>
          <?php continue;?>

          <?php elseif($providerItem->enabled == 0) : ?>
            <div class="sapps_info_tip sapps_tip">
              <?php echo $this->translate('Provider has been disabled, thus all the services related to this provider have also been disabled.');?>
            </div>
          <?php elseif($serviceItem->enabled == 0) : ?>
            <div class="sapps_info_tip sapps_tip">
              <?php echo $this->translate('Provider has disabled this service.');?>
            </div>
          <?php elseif($serviceItem->approved == 0) : ?>
            <div class="sapps_info_tip sapps_tip">
              <?php echo $this->translate('Admin has disabled this service.');?>
            </div>
          <?php endif ;?>  

          <div class='providers_browse_photo _left'>
            <div class="_img"><?php echo $this->itemBackgroundPhoto($serviceItem, 'thumb.normal') ?></div>
          </div>

          <div class="_right">
          <div class='_info'>
            <div class='services_browse_info_title _name'>
              <h3><?php echo $this->htmlLink($serviceItem->getHref(),$serviceItem->getTitle()) ?></h3>
            </div>
            <div class='providers_browse_info_date'>
              <?php echo $this->htmlLink($providerItem->getHref(),$this->itemPhoto($providerItem, 'thumb.icon')); ?>
				    <span>
              <?php echo $this->translate('By');?>
              <?php echo $this->htmlLink($providerItem->getHref(), $providerItem->title); ?>
            </span>
            </div>

            <div class="_statusinfo">
                        
                          <div class='_price' id="charges">
       
         <span class="_pricevalue"> <?php echo $this->locale()->toCurrency($item->total_charges,Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?></span>
              
            </div>
            <div class='_date'>
              <span class="_parentlabel"><?php echo $this->translate('Booking Date: ');?></span>
              <span>
              <?php
                $date1 = date_create($item->booking_date, timezone_open('UTC'));
                $date1 = date_timezone_set($date1, timezone_open($this->timezone));
                echo date_format($date1, 'F j \, Y');
              ?>
              </span>
            </div>
            <div class='providers_browse_info_date' style="overflow: hidden; word-break: break-word; margin-bottom: 5px;">
            <div class="_parentlabel"><?php echo $this->translate('Appointments Date & Time:');?></div>

              <?php 
                //$timeDuration = Engine_Api::_()->sitebooking()->timezoneConvertUsingDuration('UTC',$this->timezone,$item->duration,$serviceItem->duration);
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


            <?php $user = Engine_Api::_()->getItem('user',$providerItem->getOwner()); ?>
            <div class="seaocore_button">
              <?php echo $this->Message($user); ?>
				
			  <?php if(Engine_API::_()->seaocore()->isMobile()): ?>
                <a href="tel:<?php echo $providerItem->telephone_no ?>" class="btn btn-default">Contact</a>
              <?php else:?>
                <?php
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $providerItem->getIdentity(), 'format' => 'smoothbox'), $this->translate('Contact us'), array(
                    'class' => 'buttonlink smoothbox'
                  ));
                ?>
              <?php endif;?>
            </div>
            </div>
          </div>

          <div class='_infolabels' id="action_options_<?php echo $item->servicebooking_id; ?>">

            <?php if($item->status === 'pending'):?>
              <p class="service_pending">Service Pending</p>
            <?php elseif($item->status === 'completed'): ?>
              <p class="service_completed">Service Completed</p>
            <?php elseif($item->status === 'rejected'): ?>
              <p class="service_rejected">Service Rejected</p>
            <?php elseif($item->status === 'canceled'): ?>
              <p class="service_canceled">Service Canceled</p>
            <?php elseif($item->status === 'booked'): ?>
            <button>
                <?php
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'index', 'action' => 'status', 'booking_id' => $item->getIdentity(), 'action_type' =>'cancel', 'format' => 'smoothbox'), $this->translate('Cancel'), array(
                    'class' => 'buttonlink smoothbox'
                  ));
                ?>
            </button>

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
)); ?>
<?php else: ?>
  <div class="tip">
    <span> 
      <?php echo $this->translate('Not any service has been booked.')?>
    </span>
  </div>
<?php endif; ?>
</div>
