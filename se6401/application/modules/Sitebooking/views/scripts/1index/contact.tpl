<form class="global_form">
<div>
	<div>
		<?php if($this->bookedItem->telephone_no): ?>
		<h3><?php echo $this->translate('Contact Me');?></h3>
		<p style="font-weight: bold; font-size: 15px;"><?php echo $this->username; ?></p>
		<p><?php echo $this->bookedItem->telephone_no; ?></p>
		<?php else: ?>
		<div class="sapps_tip sapps_info_tip">There is no contact information.</div>
		<?php endif;?>
	</div>
</div>
</form>
