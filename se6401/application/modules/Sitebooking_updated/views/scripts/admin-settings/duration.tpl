<h2>
    <?php echo $this->translate('Services Booking & Appointments Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
      <h3><?php echo $this->translate("Manage Durations") ?></h3>
      <p class="description">
        <?php echo $this->translate("This page lists all the possible durations for appointment bookings of this plugin. Here you can enable or disable durations or can make new time durations.") ?>
      </p>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'settings', 'action' => 'add-duration','format' => 'smoothbox'), $this->translate('Add New Duration'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
      <br/>
      <br/>
        <?php if(count($this->durationItems)>0):?>

      <table class='admin_table'>
        <thead>
          <tr>
            <th style="text-align: left;"><?php echo $this->translate("Duration Time") ?></th>
            <th><?php echo $this->translate("Action") ?></th>
          </tr>

        </thead>
        <tbody>
          <?php foreach ($this->durationItems as $item): ?>
          <tr>
          	<td style="text-align: left;">
          	<?php 
	          	$seconds = $item->duration;
	          	if($seconds >=1800){
	          		$minutes = $seconds/60;
	          		if($minutes >=60){
	          			$hours = $minutes/60;
	            		echo $this->translate(array('%s Hour', '%s Hours',$hours), $hours);
	          		}else {
	          			echo $this->translate(array('%s Minute', '%s Minutes',$minutes), $minutes);
	          		}
	          	} 
          	?>
          </td>
            <?php if($item->action == 1): ?>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'settings', 'action' => 'disable-duration', 'id' =>$item->duration_id), $this->translate('disable') ) ?>
            </td>
            <?php else: ?>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'settings', 'action' => 'enable-duration', 'id' =>$item->duration_id), $this->translate('enable')) ?>
            </td>
        	<?php endif; ?>
          </tr>

          <?php endforeach; ?>

        </tbody>
      </table>
      <?php endif;?>
      </div>
    </form>
  </div>
</div>
