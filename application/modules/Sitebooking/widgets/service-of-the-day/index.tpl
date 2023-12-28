<div class="_thumb">
	<span class="_offthedayicon">
  		<img src="application/modules/Sitebooking/externals/images/oftheday.png" />
  </span>
  
  <?php if(!empty($this->dayitem->photo_id)) :?>
    <?php $url = Engine_Api::_()->storage()->get($this->dayitem->photo_id)->getPhotoUrl();?>
		<?php echo $this->htmlLink($this->dayitem->getHref(), "<img src = $url style='max-width:100%'>") ?>
  <?php else: ?>
    <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_service_profile.png" ?> 
		<?php echo $this->htmlLink($this->dayitem->getHref(), "<img src = $src>") ?>
  <?php endif; ?>
  
  <div class="_name">
	  <?php echo $this->htmlLink($this->dayitem->getHref(), $this->dayitem->title) ?>
  </div>    
</div>    


