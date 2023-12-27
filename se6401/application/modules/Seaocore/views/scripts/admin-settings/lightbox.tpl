<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: news.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/Mini_Color_Picker/jquery.minicolors.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/Mini_Color_Picker/jquery.minicolors.css');
?>
<h2>
  <?php echo $this->translate('SocialApps.tech Core Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='seaocore_settings_form'>
	<a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'guidelines'), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif);padding-left:23px;"><?php echo $this->translate("Guidelines for integrating a new module to display photos belonging to it in Advanced Lightbox Viewer."); ?></a>
  <br />  
  <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'guidelines'), 'admin_default', true) ?>/#high-resolution-and-large-size-photos" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif);padding-left:23px;"><?php echo $this->translate("Guidelines for uploading high resolution and large size photos."); ?></a>
  <?php
		$url = '';
		$desc = '';
		if (empty($this->content_id)) {
      $url = $this->url(array('controller' => 'content', "page" => $this->page_id), 'admin_default', true);
			//$url = $this->layout()->staticBaseUrl . 'admin/content?page=' .$this->page_id;
			$desc = $this->translate("The \"Photo Lightbox Viewer\" widget has been removed from the Layout Editor. Please <a href='$url' target='_blank'>click here</a> to place this widget in the Site Header for enabling the \"Photo Lightbox Viewer\" for your site.");		
			echo "<ul class='form-errors'><li><ul class='errors'><li>$desc</li></ul></li></ul>";
		}

	?>
  <div class='settings' style="margin-top:15px;">
	  <?php  echo $this->form->render($this)  ?>
  </div>
</div>

<script type="text/javascript">
  window.addEventListener('DOMContentLoaded', function(){ 
    showOptions('seaocore_photolightbox_fontcolor','<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sea.lightbox.fixedwindow', 1) ?>');
    showads('<?php echo Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->showSocialEngineAddOnsLightBoxPhoto() ?>');
    showModuleName('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.display.lightbox', 0) ?>');
  });

  function showlightboxads(option) {
    if(scriptJquery('#seaocore_adtype-wrapper').length) {
      if(option == 0) {
        scriptJquery('#seaocore_adtype-wrapper').css('display', 'none');
      }
      else {      
        scriptJquery('#seaocore_adtype-wrapper').css('display', 'block');
        
      }
    }
  }
 function showOptions(id, value){
   var element=scriptJquery('#' + id+'-wrapper');
   if(element){    
     if(value == 0 ){
       element.css('display', 'block');
     }else{
       element.css('display', 'none');
     }
   }
   
 }
  function showads(option) {
    if(option == 1) {
      if(scriptJquery('#seaocore_lightboxads-wrapper').length) {
        scriptJquery('#seaocore_lightboxads-wrapper').css('display', 'block');      
         showlightboxads(scriptJquery('#seaocore_lightboxads-1').is(':checked'));       
      }
    }
    else {
      if(scriptJquery('#seaocore_lightboxads-wrapper').length) {
        scriptJquery('#seaocore_lightboxads-wrapper').css('display', 'none');
        showlightboxads(0);
      }
    }
  }  
  
  function showModuleName(option) {
    if(option == 0) {
      if(scriptJquery('#seaocore_lightbox_option_display-wrapper').length) {
        scriptJquery('#seaocore_lightbox_option_display-wrapper').css('display', 'block');        
      }
    }
    else {
     if(scriptJquery('#seaocore_lightbox_option_display-wrapper').length) {
        scriptJquery('#seaocore_lightbox_option_display-wrapper').css('display', 'none');        
      }
    }
  }
   
</script>
