<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author		 John
 */
?>

<div id='group_photo'>
  <?php if($this->subject()->photo_id) { ?>
    <?php echo $this->itemPhoto($this->subject()) ?>
  <?php } else { ?>
    <?php echo $this->itemBackgroundPhoto($this->subject()) ?>
  <?php } ?>
</div>
