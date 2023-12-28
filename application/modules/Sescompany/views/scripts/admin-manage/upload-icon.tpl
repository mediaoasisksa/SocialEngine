<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: upload-icon.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>
<script>

  window.addEvent('domready',function() {
    showIcon(0);
  });

function showIcon(value) {
  if(value == 0) {
    $('font_icon-wrapper').style.display = 'none';
    $('photo-wrapper').style.display = 'block';
  } else if(value == 1) {
    $('font_icon-wrapper').style.display = 'block';
    $('photo-wrapper').style.display = 'none';
  }
}
</script>
