<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage-fonts.tpl 2022-06-20
 */

?>

<h2><?php echo $this->translate('Elpis Theme') ?></h2>

<?php if( engine_count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script>
  scriptJquery(document).ready(function() {
    usegooglefont('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('elpis.googlefonts', 1);?>');
  });
  
  function usegooglefont(value) {
    if(value == 1) {
      if(document.getElementById('elpis_bodygrp'))
        document.getElementById('elpis_bodygrp').style.display = 'none';
      if(document.getElementById('elpis_headinggrp'))
        document.getElementById('elpis_headinggrp').style.display = 'none';
      if(document.getElementById('elpis_mainmenugrp'))
        document.getElementById('elpis_mainmenugrp').style.display = 'none';
      if(document.getElementById('elpis_tabgrp'))
        document.getElementById('elpis_tabgrp').style.display = 'none';
      if(document.getElementById('elpis_googlebodygrp'))
        document.getElementById('elpis_googlebodygrp').style.display = 'block';
      if(document.getElementById('elpis_googleheadinggrp'))
        document.getElementById('elpis_googleheadinggrp').style.display = 'block';
      if(document.getElementById('elpis_googlemainmenugrp'))
        document.getElementById('elpis_googlemainmenugrp').style.display = 'block';
      if(document.getElementById('elpis_googletabgrp'))
        document.getElementById('elpis_googletabgrp').style.display = 'block';
    } else {
      if(document.getElementById('elpis_bodygrp'))
        document.getElementById('elpis_bodygrp').style.display = 'block';
      if(document.getElementById('elpis_headinggrp'))
        document.getElementById('elpis_headinggrp').style.display = 'block';
      if(document.getElementById('elpis_mainmenugrp'))
        document.getElementById('elpis_mainmenugrp').style.display = 'block';
      if(document.getElementById('elpis_tabgrp'))
        document.getElementById('elpis_tabgrp').style.display = 'block';
      if(document.getElementById('elpis_googlebodygrp'))
        document.getElementById('elpis_googlebodygrp').style.display = 'none';
      if(document.getElementById('elpis_googleheadinggrp'))
        document.getElementById('elpis_googleheadinggrp').style.display = 'none';
      if(document.getElementById('elpis_googlemainmenugrp'))
        document.getElementById('elpis_googlemainmenugrp').style.display = 'none';
      if(document.getElementById('elpis_googletabgrp'))
        document.getElementById('elpis_googletabgrp').style.display = 'none';
    }
  }
</script>
