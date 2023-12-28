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
<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>
<?php if( count($this->subnavigation)): ?>
  <div class='sesbasic-admin-navgation'> <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?> </div>
<?php endif; ?>

<script type="text/javascript">
function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected features ?") ?>");
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<h3><?php echo "Manage Features"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the Features created by you. Here, you can also add and manage any number of Features on your website. You can place these features on the Landing Page.  <br /><br /> Configure other settings from here: <a href='admin/sescompany/settings/landing-page-setting#sescompany_la1features-wrapper'>Features For Design 1
</a>") ?>	
</p>
<br class="clear" />
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-features', 'action' => 'create'), $this->translate("Create New Feature"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s feature found.', '%s features found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Feature Name") ?></th>
        <th><?php echo $this->translate("Description") ?></th>
        <th align="center"><?php echo $this->translate("Status");?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->feature_id;?>' value='<?php echo $item->feature_id ?>' /></td>
          <td><?php echo $item->feature_id ?></td>
          <td><?php echo $item->feature_name; ?></td>
          <td><?php echo $item->description; ?></td>
          <td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-features', 'action' => 'enabled', 'id' => $item->feature_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-features', 'action' => 'enabled', 'feature_id' => $item->feature_id), $this->htmlImage('application/modules/Sesbasic/externals/images/icons/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-features', 'action' => 'create', 'feature_id' => $item->feature_id), $this->translate("Edit"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-features', 'action' => 'delete', 'feature_id' => $item->feature_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no features created by you yet.") ?>
    </span>
  </div>
<?php endif; ?>