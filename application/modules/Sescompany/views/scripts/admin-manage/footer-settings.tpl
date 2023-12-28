<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: header-settings.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>
<div class='clear sesbasic_admin_form company_header_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<?php $showsocialmedia = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showsocialmedia', 1); ?>
<?php $quicklinksenable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.quicklinksenable', '1'); ?>

<script type="text/javascript">
  
  window.addEvent('domready',function() {
    social('<?php echo $showsocialmedia;?>');
    enableExtralink('<?php echo $quicklinksenable; ?>');
  });
  
  function enableExtralink(value) {
    if(value == 1) {
      document.getElementById('sescompany_quicklinkheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_quicklinkheading-wrapper').style.display = 'none';
    }
  }
  
  function social(value) {
    if(value == 1) {
      document.getElementById('sescompany_socialmediaheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_socialmediaheading-wrapper').style.display = 'none';
    }
  }
</script>