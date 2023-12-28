<?php 

?>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', '')) { ?>
  <div class="tip"><span><?php echo "You have not enter 'Google Map API Key' to use location feature in this plugin. So, you can enter 'Google Map API Key' from <a href='admin/sesbasic/settings/global' target='_blank'>here</a>."; ?></span></div>
<?php } ?>
