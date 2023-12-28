<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: invite.tpl 9747 2012-07-26 02:08:08Z john $
 * @author	   John
 */
?>
<?php if( $this->count > 0 ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      scriptJquery('#selectall').on('click', function(event) {
        scriptJquery('input[type=checkbox]').prop('checked', scriptJquery(this).prop('checked'));
      })
    });
  </script>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php else: ?>
  <div>
    <?php echo $this->translate('You have no friends you can invite.');?>
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Close'), array('onclick' => 'parent.Smoothbox.close();')) ?>
  </div>
<?php endif; ?>
