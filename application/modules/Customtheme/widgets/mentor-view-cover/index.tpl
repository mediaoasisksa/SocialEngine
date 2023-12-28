<?php ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/slick.css'); ?>
<?php $this->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Customtheme/externals/scripts/jquery-slick.js');?>
<?php $this->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Customtheme/externals/scripts/slick.min.js');?>
<div class="sesbasic_bxs mentor_view_cover">
	<div class="mentor_view_cover_top">
		<div class="mentor_view_cover_top_content">
			<div class="_thumb">	
				<div class="_img">
				  <?php if($this->subject()->photo_id) { ?>
				    <?php echo $this->itemPhoto($this->subject()) ?>
				  <?php } else { ?>
				    <?php echo $this->itemBackgroundPhoto($this->subject()) ?>
				  <?php } ?>
				</div>
			</div>
			<div class="_info">
				<?php if($this->profileType):?><span class="category custom_tag"><a href="/pages/mentor-service"><?php echo $this->translate($this->profileType);?></a></span><?php endif;?>
				<div class="_social">
					<?php if($this->twitterLink):?><a href="<?php echo $this->twitterLink;?>"><i class="fab fa-twitter"></i></a><?php endif;?>
					<?php if($this->linkedinLink):?><a href="<?php echo $this->linkedinLink;?>"><i class="fab fa-linkedin"></i></a><?php endif;?>
				</div>
			</div>
		</div>
	</div>
	<div class="mentor_view_cover_bottom_content">
		<div class="mentor_view_cover_bottom_left">	     
		    <?php if($this->fname && $this->lname):?>
			    <h3><?php echo $this->fname  . ' ' .  $this->lname?></h3>
			<?php else:?>
			    <h3><?php echo $this->subject()->getTitle()?></h3>
			<?php endif;?>
			<?php if($this->subject()->jobtitle):?><?php echo $this->subject()->jobtitle;?><?php endif;?>
			<?php if($this->subject()->qualifications):?> <?php echo $this->translate('At');?> <b><?php echo $this->subject()->qualifications;?></b><?php endif;?>
			<?php if($this->subject()->description):?><p class="t"><?php echo $this->subject()->description;?></p><?php endif;?>
			
			
			<?php if($this->educationlevel):?><b> <?php echo $this->educationlevel;?></b>  <?php echo $this->translate('From');?> <?php if($this->educationinstitute):?> <b><?php echo $this->educationinstitute;?></b><?php endif;?><?php endif;?>

			<?php if(($this->city) || ($this->country)):?>
				<p class="_ti _location">
					<i class="fas fa-map-marker-alt"></i>
					<?php if($this->city):?><span><?php echo $this->city;?></span><?php endif;?><?php if($this->country):?><span><?php echo $this->country;?></span><?php endif;?>
				</p>
			<?php endif;?>
			<?php if($this->viewer_id == $this->subject()->getIdentity()):?>
			<p class="_ti"><i class="fas fa-edit"></i><span><a href="/members/edit/profile"><?php echo $this->translate("Edit Profile");?></a></span></p>
			<p class="_ti"><i class="fas fa-cog"></i><span><a href="/members/settings/general"><?php echo $this->translate("Settings");?></a></span></p>
			<p class="_ti"><i class="fas fa-sign-out-alt"></i><span><a href="/logout"><?php echo $this->translate("Logout");?></a></span></p>
			<?php endif;?>
		</div>
		<?php if($this->dataMentor):?>
		<div class="mentor_view_cover_bottom_right">
			<ul>
				<li class="mentor_view_cover_bottom_right_service">
					<div class="_info">
						<h3><a href="/pages/mentor-service"><?php echo $this->translate("Mentor Service")?></a></h3>
						<p><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $this->dataMentor['price'] . ' / ' . $this->translate('Monthly'); ?></p>
					</div>
					<?php if($this->viewer_id == $this->dataMentor['owner_id']): ?>
					<div class="_options">
						<a href="/bookings/providers/<?php echo $this->dataMentor['parent_id']; ?>/service/edit/<?php echo $this->dataMentor['ser_id']; ?>"><i class="fas fa-pencil-alt"></i><span><?php echo $this->translate("Edit Service")?></span></a>
						<!--<a href="/bookings/providers/<?php echo $this->dataMentor['parent_id']; ?>/service/delete/<?php echo $this->dataMentor['ser_id']; ?>"><i class="fas fa-trash"></i><span><?php echo $this->translate("Delete Service")?></span></a>-->
						<a href="/sitebooking/service/disable/pro_id/<?php echo $this->dataMentor['parent_id']; ?>/id/<?php echo $this->dataMentor['ser_id']; ?>/format/smoothbox" class="_disabled smoothbox"><i class="fas fa-minus-circle"></i><span><?php echo $this->translate("Disable Service")?></span></a>
						
						<a href="/bookings/providers/available/<?php echo $this->dataConsultant['parent_id']; ?>?ser_id=<?php echo $this->dataMentor['ser_id']; ?>&isAjax=1"><i class="far fa-clock"></i><span><?php echo $this->translate("Set Booking Time")?></span></a>
					</div>
					<?php endif;?>
				</li>
				<li class="mentor_view_cover_bottom_right_service">
					<div class="_info">
						<h3><a href="/bookings/services/home"><?php echo $this->translate("Consulting Service")?></a></h3>
						<p><p><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD') . ' ' . $this->dataConsultant['price']; ?> / 30 Minutes</p>
					</div>
					<?php if($this->viewer_id == $this->dataConsultant['owner_id']): ?>
					<div class="_options">
	                    <a href="/bookings/providers/<?php echo $this->dataConsultant['parent_id']; ?>/service/edit/<?php echo $this->dataConsultant['ser_id']; ?>"><i class="fas fa-pencil-alt"></i><span><?php echo $this->translate("Edit Service")?></span></a>
						<a href="/sitebooking/service/disable/pro_id/<?php echo $this->dataConsultant['parent_id']; ?>/id/<?php echo $this->dataConsultant['ser_id']; ?>/format/smoothbox" class="_disabled smoothbox"><i class="fas fa-minus-circle"></i><span><?php echo $this->translate("Disable Service")?></span></a>
						<a href="/bookings/providers/available/<?php echo $this->dataConsultant['parent_id']; ?>?ser_id=<?php echo $this->dataConsultant['ser_id']; ?>&isAjax=1"><i class="far fa-clock"></i><span><?php echo $this->translate("Set Booking Time")?></span></a>
					</div>
					<?php endif;?>
				</li>
			</ul>
		</div>
		<?php endif;?>
	</div>	
</div>