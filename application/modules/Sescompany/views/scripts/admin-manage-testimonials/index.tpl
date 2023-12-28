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
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected testimonials ?") ?>");
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
<h3><?php echo "Manage Testimonials"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the Testimonials created by you. Here, you can also add and manage any number of Testimonials on your website. You can place these testimonials on the Landing Page.<br /><br /> Configure other settings from here: <a href='admin/sescompany/settings/landing-page-setting#sescompany_mngtestimonials-wrapper'>Testimonials For Design 1 and Design 2</a>") ?>	
</p>
<br class="clear" />
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-testimonials', 'action' => 'create'), $this->translate("Create New Testimonial"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s testimonial found.', '%s testimonials found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Owner Name") ?></th>
        <th><?php echo $this->translate("Designation") ?></th>
        <th><?php echo $this->translate("Creation Date") ?></th>
        <th align="center"><?php echo $this->translate("Status");?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->testimonial_id;?>' value='<?php echo $item->testimonial_id ?>' /></td>
          <td><?php echo $item->testimonial_id ?></td>
          <td><?php echo $item->owner_name; ?></td>
          <td><?php echo $item->designation; ?></td>
          <td><?php echo $item->creation_date; ?></td>
          <td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-testimonials', 'action' => 'enabled', 'id' => $item->testimonial_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-testimonials', 'action' => 'enabled', 'testimonial_id' => $item->testimonial_id), $this->htmlImage('application/modules/Sesbasic/externals/images/icons/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-testimonials', 'action' => 'create', 'testimonial_id' => $item->testimonial_id), $this->translate("Edit"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage-testimonials', 'action' => 'delete', 'testimonial_id' => $item->testimonial_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
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
      <?php echo $this->translate("There are no testimonials created by you yet.") ?>
    </span>
  </div>
<?php endif; ?>