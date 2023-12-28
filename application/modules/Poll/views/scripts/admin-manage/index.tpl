<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9916 2013-02-15 03:13:27Z alex $
 * @author     Steve
 */
?>


<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected polls?") ?>");
}

function selectAll(obj)
{
  scriptJquery('.checkbox').each(function(){
    scriptJquery(this).prop("checked",scriptJquery(obj).prop("checked"));
  });
}

</script>

<h2><?php echo $this->translate("Polls Plugin") ?></h2>

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
  <?php echo $this->translate("POLL_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
  <?php
  $settings = Engine_Api::_()->getApi('settings', 'core');
  if( $settings->getSetting('user.support.links', 0) == 1 ) {
          echo 'More info: <a href="https://community.socialengine.com/blogs/597/56/polls" target="_blank">See KB article</a>.';
  } 
  ?>	
</p>

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
        <th><?php echo $this->translate("Votes") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->poll_id;?>' value='<?php echo $item->poll_id ?>' /></td>
          <td data-label="ID" ><?php echo $item->poll_id ?></td>
          <td data-label="<?php echo $this->translate("Title") ?>" title="<?php echo $this->escape($item->getTitle()) ?>">
            <?php echo $this->string()->truncate($item->getTitle(), 48) ?>
          </td>
          <td data-label="<?php echo $this->translate("Owner") ?>"><?php echo $item->getOwner()->getTitle() ?></td>
          <td data-label="<?php echo $this->translate("Views") ?>"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
          <td data-label="<?php echo $this->translate("Votes") ?>"><?php echo $item->vote_count ?></td>
          <td data-label="<?php echo $this->translate("Date") ?>"><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td class="admin_table_options">
            <a href="<?php echo $this->url(array('poll_id' => $item->poll_id), 'poll_view') ?>">
              <?php echo $this->translate("view") ?>
            </a>
            
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'poll', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->poll_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
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

  <br/>

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no polls created yet.") ?>
    </span>
  </div>
<?php endif; ?>
