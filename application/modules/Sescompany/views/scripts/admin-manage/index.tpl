<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/font-awesome/css/font-awesome.min.css'); ?>

<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>
<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'header-settings'), $this->translate('Header Settings')) ?>
    </li>
    <li  class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'index'), $this->translate('Main Menu Icons')) ?>
    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'manage-search'), $this->translate('Manage Search Module')) ?>
    </li>
  </ul>
</div>
<h3><?php echo "Manage Main Menu Icons"; ?></h3>
<p><?php echo "Here, you can add icons for the Main Navigation Menu items on your website. You can also edit and delete the icons. <br />
 
While adding the icon you can choose to add a Font Icon or upload an image for the icon as per your requirement.
"; ?> </p>
<br />

<table class='admin_table company_manangemenu_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Menu Item") ?></th>
      <th><?php echo $this->translate("Icon") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): //print_r($item->toarray());die; ?>
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($item->module)): ?>
        <tr>
          <td><?php echo $item->label ?></td>
          <td>
            <?php if(!empty($item->file_id) || !empty($item->font_icon)):?>
              <?php $label = 'Edit';?>
              <?php if(empty($item->icon_type)): ?>
                <img class="company_manangemenu_icon" alt="" src="<?php echo $this->storage->get($item->file_id, '')->getPhotoUrl(); ?>" />
              <?php else: ?>
                <i class="fa <?php echo $item->font_icon; ?>"></i>
              <?php endif;?>
            <?php else:?>
              <?php $label = 'Add';?>
                  -
            <?php endif;?>
          </td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sescompany', 'controller' => 'admin-manage', 'action' => 'upload-icon', 'id' => $item->id,'type' => 'main', 'icon_type' => $item->icon_type), $label, array('class' => 'smoothbox')); ?>
            <?php if(!empty($item->file_id) || !empty($item->font_icon)):?>
              | 
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sescompany', 'controller' => 'admin-manage', 'action' => 'delete-menu-icon', 'id' => $item->id, 'file_id' => $item->file_id, 'icon_type' => $item->icon_type), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
            <?php endif;?>
          </td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>