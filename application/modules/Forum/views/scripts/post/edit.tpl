<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>
<script type="text/javascript">
function updateUploader()
{
  if(scriptJquery('#photo_delete').pop("checked")) {
    scriptJquery('#photo_group-wrapper').show();
  }
  else 
  {
    scriptJquery('#photo_group-wrapper').hide();
  }
}
</script>
<div class="layout_middle">
  <div class="generic_layout_container">
    <h2><?php echo $this->translate('Edit Post');?></h2>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
