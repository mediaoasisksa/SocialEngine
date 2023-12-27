<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div class="settings">
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">

  scriptJquery(document).ready(function() {
    scriptJquery('input[type=radio][name=adminemail]:checked').trigger('change');
  });
  
  function showHideEmail(value) {
    if(value == 1) {
      scriptJquery('#adminemailaddress-wrapper').show();
    } else {
      scriptJquery('#adminemailaddress-wrapper').hide();
    }
  }
</script>
