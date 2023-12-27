<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: custom_themes.tpl 2022-06-20
 */

?>
<div class="elpis_styling_buttons">
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'elpis', 'controller' => 'settings', 'action' => 'add'), $this->translate("Add New Custom Theme"), array('class' => 'smoothbox elpis_button add_new_theme fa fa-plus', 'id' => 'custom_themes')); ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'elpis', 'controller' => 'settings', 'action' => 'add', 'customtheme_id' => $this->customtheme_id), $this->translate("Edit Custom Theme Name"), array('class' => 'smoothbox elpis_button fa fa-pencil', 'id' => 'edit_custom_themes')); ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'elpis', 'controller' => 'settings', 'action' => 'delete', 'customtheme_id' => $this->customtheme_id), $this->translate("Delete Custom Theme"), array('class' => 'smoothbox elpis_button fa fa-close', 'id' => 'delete_custom_themes')); ?>
  <a href="javascript:void(0);" class="elpis_button fa fa-close disabled" id="deletedisabled_custom_themes" style="display: none;"><?php echo $this->translate("Delete Custom Theme"); ?></a>
</div>
