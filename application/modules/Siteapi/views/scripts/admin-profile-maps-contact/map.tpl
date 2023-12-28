<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: map.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="settings global_form_popup">
<?php if ($this->status):?>
    <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
<?php else: ?>
  <div class="error">
    <span>
      <?php echo $this->translate($this->message) ?>
    </span>
  </div>
<?php endif;?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>