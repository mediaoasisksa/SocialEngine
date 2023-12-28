<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?>

<h2>
  <?php echo $this->translate("Manage Tags") ?>
</h2>
<p>
  <?php echo $this->translate("This page lists all of the tags your users have posted. You can use this page to monitor these tags and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific tags. Leaving the filter fields blank will show all of the tags on your social network.") ?>
</p>
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
if( $settings->getSetting('user.support.links', 0) == 1 ) {
echo 'More info: <a href="https://community.socialengine.com/blogs/597/47/tag-management" target="_blank">See KB article</a>.';
}
?>
<br />
<br />
<script type="text/javascript">


function multiModify()
{
  var multimodify_form = scriptJquery('#multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected tags?")) ?>');
  }
}

function selectAll(obj)
{
  scriptJquery('.checkbox').each(function(){
    scriptJquery(this).prop("checked",scriptJquery(obj).prop("checked"))
  });
}
</script>
<div>
  <a class='smoothbox buttonlink icon_tag' href='<?php echo $this->url(array('action' => 'add'));?>'><?php echo $this->translate("Add Tags") ?></a>
</div>
<br />
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />
<?php $count = $this->paginator->getTotalItemCount() ?>

<?php if($count > 0) { ?>
  <div class='admin_results'>
    <div>
      
      <?php echo $this->translate(array("%s tag found.", "%s tags found.", $count),
          $this->locale()->toNumber($count)) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
      )); ?>
    </div>
  </div>
  <br />
  <div class="admin_table_form">
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
      <table class='admin_table admin_responsive_table'>
        <thead>
          <tr>
            <th style='width: 1%;'><input onclick="selectAll(this)" type='checkbox' class='checkbox'></th>
            <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
            <th><?php echo $this->translate("Tag Name") ?></th>
            <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Option") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if( engine_count($this->paginator) ): ?>
            <?php foreach( $this->paginator as $item ): ?>
              <tr>
                <td><input name='modify_<?php echo $item->tag_id;?>' value='<?php echo $item->tag_id;?>' type='checkbox' class='checkbox'></td>
                <td data-label="ID"><?php echo $item->tag_id ?></td>
                <td data-label="<?php echo $this->translate("Tag Name") ?>">
                <?php echo $item->text; ?>
                </td>
                <td class='admin_table_options'>
                  <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->tag_id));?>'>
                    <?php echo $this->translate("Delete") ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <br />
      <div class='buttons'>
        <button type='submit' name="submit_button" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
      </div>
    </form>
  </div>
<?php } else { ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no tags yet."); ?>
    </span>
  </div>
<?php } ?>
