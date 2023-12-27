<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Service Bookings'); ?></h3>
<p><?php echo $this->translate('This page lists all the service bookings your users have made. You can use this page to monitor these booked services.'); ?></p>
<br/>

<div class="admin_search ">
  <div class="search">
    <form method="post" class="global_form_box" action="" width="100%">

      <div>
          <label>
              <?php echo $this->translate("User") ?>
          </label>
          <?php if (empty($this->user)): ?>
              <input type="text" name="user" /> 
          <?php else: ?>
              <input type="text" name="user" value="<?php echo $this->translate($this->user) ?>"/>
          <?php endif; ?>
      </div>

      <div>
          <label>
              <?php echo $this->translate("Provider") ?>
          </label>  
          <?php if (empty($this->provider)): ?>
              <input type="text" name="provider" /> 
          <?php else: ?> 
              <input type="text" name="provider" value="<?php echo $this->translate($this->provider) ?>" />
          <?php endif; ?>
      </div>

      <div>
          <label>
              <?php echo $this->translate("Service") ?>
          </label>  
          <?php if (empty($this->service)): ?>
              <input type="text" name="service" /> 
          <?php else: ?> 
              <input type="text" name="service" value="<?php echo $this->translate($this->service) ?>" />
          <?php endif; ?>
      </div>

      <div class="sb_admin_calendar">
        <?php 
          //MAKE THE bookingdate filter
          $attributes = array();
          $attributes['dateFormat'] = 'ymd';

          $form = new Engine_Form_Element_CalendarDateTime('bookingdate');
          $attributes['options'] = $form->getMultiOptions();
          $attributes['id'] = 'bookingdate';

          $bookingdate['date'] = $this->bookingdate;

          echo '<label>Booking Date</label><div>';
          echo $this->FormCalendarDateTime('bookingdate', $bookingdate, array_merge(array('label' => 'Booking Date'), $attributes), $attributes['options'] );
          echo '</div>';
        ?>
      </div>

      <div class="sb_admin_calendar">
          <label>
              <?php echo $this->translate("Status") ?> 
          </label>
          <select id="sponsored" name="status">
            <option value="0"  ><?php echo $this->translate("Select") ?></option>
            <option value="pending" <?php if ($this->status === 'pending') echo "selected"; ?> ><?php echo $this->translate("Pending") ?></option>
            <option value="rejected" <?php if ($this->status === 'rejected') echo "selected"; ?> ><?php echo $this->translate("Rejected") ?></option>
            <option value="booked" <?php if ($this->status === 'booked') echo "selected"; ?> ><?php echo $this->translate("Booked") ?></option>
            <option value="canceled" <?php if ($this->status === 'canceled') echo "selected"; ?> ><?php echo $this->translate("Canceled") ?></option>
          </select>
      </div> 

      <div class="sb_admin_calendar" style="clear: both; margin-left: 0; margin-top: 10px;">
        <?php 
          //MAKE THE servicingdate filter
          $attributes = array();
          $attributes['dateFormat'] = 'ymd';

          $form = new Engine_Form_Element_CalendarDateTime('servicingdate');
          $attributes['options'] = $form->getMultiOptions();
          $attributes['id'] = 'servicingdate';

          $servicingdate['date'] = $this->servicingdate;

          echo '<label>Servicing Date</label><div>';
          echo $this->FormCalendarDateTime('servicingdate', $servicingdate, array_merge(array('label' => 'Servicing Date'), $attributes), $attributes['options'] );
          echo '</div>';
        ?>
      </div> 

  <script type="text/javascript">

    en4.core.runonce.add(function() {

      window.addEvent('domready', function() {

          if ($('bookingdate-minute') && $('servicingdate-minute')) {
              $('bookingdate-minute').destroy();
              $('servicingdate-minute').destroy();
          }
          if ($('bookingdate-ampm') && $('servicingdate-ampm')) {
              $('bookingdate-ampm').destroy();
              $('servicingdate-ampm').destroy();
          }
          if ($('bookingdate-hour') && $('servicingdate-hour')) {
              $('bookingdate-hour').destroy();
              $('servicingdate-hour').destroy();
          }

          if ($('calendar_output_span_bookingdate-date')) {
              $('calendar_output_span_bookingdate-date').style.display = 'none';
          }

          if ($('calendar_output_span_servicingdate-date')) {
              $('calendar_output_span_servicingdate-date').style.display = 'none';
          }

          if ($('bookingdate-date')) {
              $('bookingdate-date').setAttribute('type', 'text');
          }

          if ($('servicingdate-date')) {
              $('servicingdate-date').setAttribute('type', 'text');
          }

      });
    
    }); 

  </script>        
      <div class="clear mtop10" style="margin-left: 0;">
          <button type="submit" name="search" value="search"><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>


<div class='admin_members_results'>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s booking found.', '%s bookings found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php elseif($this->search == 1): ?>
        <div class="tip"><span>
            <?php echo $this->translate("No results were found.") ?></span>
        </div>
    <?php return; endif; ?>
</div>

<?php if( count($this->paginator) ): ?>
<form id='multidelete_form'>
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'>ID</th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("User") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Provider") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Service") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Status") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Booking Date") ?></th>
        <th align="" class=""><?php echo $this->translate("Servicing Time") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Cost") ?></th>
        <th align="center" class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class="admin_table_centered"><?php echo $item->getIdentity() ?></td>
          <td class="admin_table_centered"><?php echo ucwords($item->getOwner()->getTitle()) ?></td>
          <td class="admin_table_centered"><?php echo ucwords($item->provider_title) ?></td>
          <td class="admin_table_centered"><?php echo ucwords($item->service_title) ?></td>
          
          <?php if ($item->status === 'booked'){ ?>
           <td class="admin_table_centered"><?php echo 'Pending for Approval'; ?></td>
          <?php } elseif($item->status === 'pending') { ?>
          <td class="admin_table_centered"><?php echo 'Approved'; ?></td>
          <?php } else{ ?>
          <td class="admin_table_centered"><?php echo ucwords($item->status) ?></td>
          <?php } ?>
          
          <td class="admin_table_centered"><?php echo $item->booking_date ?></td>            
          <td class="">
            <?php 
              $date = json_decode($item->duration);
              foreach ($date as $key => $value) : ?>
              <p><?php echo $key." : ".$value; ?></p>
            <?php endforeach;?>
          </td>
          <td class="admin_table_centered"><?php echo $this->locale()->toCurrency($item->total_charges,Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?></td>
          <td>
            <?php echo $this->htmlLink(array('action' => 'view','pro_id' => $item->pro_id,'ser_id' => $item->ser_id,'route' => 'sitebooking_service_entry_view','reset' => true,'slug' => $item->service_slug), $this->translate("view") )?>
            |
            <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'admin-booking-manage', 'action' => 'change-status', 'booking_id' => $item->getIdentity(),'format' => 'smoothbox'),
                  $this->translate("edit"),array('class' => 'smoothbox')); ?>
            |
            <?php echo $this->htmlLink(
                  array('route'  => 'default', 'module' => 'sitebooking', 'controller' => 'admin-booking-manage', 'action' => 'delete', 'id' => $item->getIdentity(),'format' => 'smoothbox'),
                  $this->translate("delete"),
                  array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
  <br/>
  <div>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  'params' => $this->formValues,
)); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no booked services by your users yet.") ?>
    </span>
  </div>
<?php endif; ?>

