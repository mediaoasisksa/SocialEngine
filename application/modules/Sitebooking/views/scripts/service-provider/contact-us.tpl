<?php if($this->providerItem->telephone_no): ?>
<form class="global_form">
<div>
	<div>
		<h3><?php echo $this->translate('Contact Us');?></h3>
		<p style="font-weight: bold; font-size: 15px;"><?php echo $this->providerItem->title; ?></p>
		<p><?php echo $this->providerItem->telephone_no; ?></p>
		<?php else: ?>
		<div class="sapps_tip sapps_info_tip">There is no contact information.</div>
	</div>
</div>
</form>
<?php endif; ?>