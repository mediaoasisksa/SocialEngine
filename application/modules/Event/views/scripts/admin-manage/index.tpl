<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9916 2013-02-15 03:13:27Z alex $
 * @author     Jung
 */
?>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected events?');?>");
}

function selectAll(obj)
{
  scriptJquery('.checkbox').each(function(){
    scriptJquery(this).prop("checked",scriptJquery(obj).prop("checked"))
  });
}
</script>





<h2><?php echo $this->translate("Events Plugin") ?></h2>

<?php if( engine_count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("EVENT_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
if( $settings->getSetting('user.support.links', 0) == 1 ) {
	echo '     More info: <a href="https://community.socialengine.com/blogs/597/51/events" target="_blank">See KB article</a>.';
} 
?>	
<br />
<br />
  <?php if( engine_count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <table class='admin_table admin_responsive_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll(this);' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'>ID</th>
          <th><?php echo $this->translate("Title") ?></th>
          <th><?php echo $this->translate("Owner") ?></th>
          <th><?php echo $this->translate("Views") ?></th>
          <th><?php echo $this->translate("Date Created") ?></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->event_id; ?>' value='<?php echo $item->event_id; ?>' /></td>
            <td data-label="ID"><?php echo $item->event_id ?></td>
            <td data-label="<?php echo $this->translate("Title") ?>"><?php echo $item->title ?></td>
            <td data-label="<?php echo $this->translate("Owner") ?>"><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()); ?></td>
            <td data-label="<?php echo $this->translate("Views") ?>"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
            <td data-label="<?php echo $this->translate("Date Created") ?>"><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
            <td  class="admin_table_options">
              <a href="<?php echo $this->url(array('id' => $item->event_id), 'event_profile') ?>">
                <?php echo $this->translate("view") ?>
              </a>
              <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'event', 'controller' => 'manage', 'action' => 'delete', 'id' => $item->event_id),
                $this->translate('delete'),
                array('class' => 'smoothbox',
              )) ?>
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

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no events posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
