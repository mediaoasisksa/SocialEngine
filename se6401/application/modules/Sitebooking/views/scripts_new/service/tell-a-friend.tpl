<?php
$this->headLink()
		->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitebusiness/externals/styles/style_sitebusiness_profile.css');
?>
<div class=" ">
  <?php  
	 if (isset($this->form->captcha)):
	   $this->form->removeElement('captcha');      
	 endif;
  
   ?>
  <?php echo $this->form->render($this); ?>
</div>