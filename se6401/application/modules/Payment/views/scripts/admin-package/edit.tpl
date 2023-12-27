<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>
<h2>
  <?php echo $this->translate("Billing") ?>
</h2>	
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class="settings package_create_form">
  <?php echo $this->form->render($this) ?>
</div>
<script>

  scriptJquery(window).ready(function() {
    sendReminder('<?php echo $this->package->send_reminder; ?>');
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
