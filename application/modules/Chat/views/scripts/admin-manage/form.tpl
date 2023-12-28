<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: form.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <div>
    <?php echo $this->translate("Changes saved.") ?>
  </div>
  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location = parent.window.location.href;
      parent.Smoothbox.close();
    }, 1000);
  </script>
<?php endif; ?>
