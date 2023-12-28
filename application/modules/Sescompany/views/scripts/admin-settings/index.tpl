<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>

<div class='clear'>
  <div class='settings sescompany_admin_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<div class="sesbasic_waiting_msg_box" style="display:none;">
	<div class="sesbasic_waiting_msg_box_cont">
    <?php echo $this->translate("Please wait.. It might take some time to activate plugin."); ?>
    <i></i>
  </div>
</div>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.pluginactivated',0)){ 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php } ?>
<?php $showPopup = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popup.enable', 1);?>

<script type="text/javascript">
 
  window.addEvent('domready',function() {
    showPopup('<?php echo $showPopup;?>');
  });
  
  function showPopup(value) {
    if(value == 1)
      document.getElementById('sescompany_popup_day-wrapper').style.display = 'block';
    else
      document.getElementById('sescompany_popup_day-wrapper').style.display = 'none';
  }
</script>
<?php 
$showPopup = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popupsign', 1);
$loginsignupvisiablity = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popup.enable', 1);
?>

<script type="text/javascript">
 
  window.addEvent('domready',function() {
    showPopup('<?php echo $showPopup;?>');
    <?php if(!empty($showPopup)): ?>
      loginsignupvisiablity('<?php echo $loginsignupvisiablity;?>');
    <?php endif; ?>
  });
  
  function loginsignupvisiablity(value) {
    if(value == 1) {
			document.getElementById('sescompany_popup_day-wrapper').style.display = 'block';
		} else {
			document.getElementById('sescompany_popup_day-wrapper').style.display = 'none';
    }
  }
  
  function showPopup(value) {
    if(value == 1) {
			document.getElementById('sescompany_popup_enable-wrapper').style.display = 'block';
      document.getElementById('sescompany_popup_day-wrapper').style.display = 'block';
			document.getElementById('sescompany_popupfixed-wrapper').style.display = 'block';
		} else {
			document.getElementById('sescompany_popup_enable-wrapper').style.display = 'none';
      document.getElementById('sescompany_popup_day-wrapper').style.display = 'none';
			document.getElementById('sescompany_popupfixed-wrapper').style.display = 'none';
		}
  }
</script>
     