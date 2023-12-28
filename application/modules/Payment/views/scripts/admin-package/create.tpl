<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<div class="settings package_create_form">
  <?php echo $this->form->render($this) ?>
</div>
<script>

  scriptJquery(window).ready(function() {
    sendReminder(1);
  });

  function sendReminder(value) { 
    if(value == 1) { 
      scriptJquery('#reminder_email-wrapper').show();
    } else {
      scriptJquery('#reminder_email-wrapper').hide();
    }
  }
  if(document.getElementById('reminder_email-select'))
  document.getElementById('reminder_email-select').style.display = "none";
</script>
