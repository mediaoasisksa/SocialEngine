<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class='settings'>
  <?php echo $this->form->render($this); ?>
</div>
<script>
	scriptJquery(document).ready(function() {
    loginLogs("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.enableloginlogs', '1'); ?>");
  });

	function loginLogs(value) {
		if(value == 1) {
			scriptJquery('#logincrondays-wrapper').show();
		} else {
			scriptJquery('#logincrondays-wrapper').hide();
		}
	}
</script>
