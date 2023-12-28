<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?>

<h2>
  <?php echo $this->translate("Manage Members") ?>
</h2>

<p>
  <?php echo $this->translate("USER_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br />
<?php
	$settings = Engine_Api::_()->getApi('settings', 'core');
	if( $settings->getSetting('user.support.links', 0) == 1 ) {
		echo 'More info: <a href="https://community.socialengine.com/blogs/597/12/members" target="_blank">See KB article</a>.';
	} 
?>	
<br />
<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      scriptJquery('#order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      scriptJquery('#order').val(order);
      scriptJquery('#order_direction').val(default_direction);
    }
    scriptJquery('#filter_form').trigger("submit");
  }

function multiModify()
{
  var multimodify_form = scriptJquery('#multimodify_form');
  if (multimodify_form.find("#submit_button").val() == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
  }
}

function selectAll(obj) {
  scriptJquery('.checkbox').each(function(){
    scriptJquery(this).prop("checked",scriptJquery(obj).prop("checked"))
  });
}

function loginAsUser(id) {
  if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
    return;
  }
  var url = '<?php echo $this->url(array('action' => 'login')) ?>';
  var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
  (scriptJquery.ajax({
    url : url,
    dataType: 'json',
    method : 'post',
    data : {
      format : 'json',
      id : id
    },
    success : function() {
      window.location.replace( baseUrl );
    }
  }));
}

<?php if( $this->openUser ): ?>
scriptJquery(document).ready(function() {
  scriptJquery('#multimodify_form .admin_table_options a').each(function() {
    var el = scriptJquery(this);
    if( -1 < el.attr('href').indexOf('/edit/') ) {
      el.trigger("click");
    }
  });
});
<?php endif ?>
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />
<?php if($this->paginator->getTotalItemCount() > 0) { ?>
  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s member found.", "%s members found.", $count),
          $this->locale()->toNumber($count)) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        //'params' => $this->formValues,
      )); ?>
    </div>
  </div>
  <br />
  <div class="admin_table_form ">
  <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
    <table class='admin_table admin_responsive_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><input onclick="selectAll(this)" type='checkbox' class='checkbox'></th>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
          <th style=''><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
          <th style='' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
          <th style='' class='admin_table_centered'><?php echo $this->translate("Approved") ?></th>
          <th style=''><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Signup Date") ?></a></th>
          <th style='' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( engine_count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ):
            $user = $this->item('user', $item->user_id);
            ?>
            <tr>
              <td><input <?php if ($item->level_id == 1) echo 'disabled';?> name='modify_<?php echo $item->getIdentity();?>' value='<?php echo $item->getIdentity();?>' type='checkbox' class='checkbox'></td>
              <td  data-label="ID"><?php echo $item->user_id ?></td>
              <td data-label="<?php echo $this->translate("Display Name") ?>" class='admin_table_bold'>
                <?php echo $this->htmlLink($user->getHref(),
                    $this->string()->truncate($user->getTitle(), 10),
                    array('target' => '_blank'))?>
              </td>
              <td data-label="<?php echo $this->translate("Username") ?>" class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
              <td data-label="<?php echo $this->translate("Email") ?>" class='admin_table_email'>
                <?php if( !$this->hideEmails ): ?>
                  <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
                <?php else: ?>
                  (hidden)
                <?php endif; ?>
              </td>
              <td data-label="<?php echo $this->translate("User Level") ?>" class="admin_table_centered nowrap">
                <a href="<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>">
                  <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
                </a>
              </td>
              <td data-label="<?php echo $this->translate("Approved") ?>" class='admin_table_centered'>
                <?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') ) ?>
              </td>
              <td data-label="<?php echo $this->translate("Date") ?>" class="nowrap">
                <?php echo $this->locale()->toDateTime($item->creation_date) ?>
              </td>
              <td class='admin_table_options'>
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'stats', 'id' => $item->user_id));?>'>
                  <?php echo $this->translate("stats") ?>
                </a>
                <?php $auth = $item->isSuperAdmin() ? $this->viewer()->isSuperAdmin($item) : 1; ?>
                <?php if ($auth): ?>
  
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->user_id));?>'>
                  <?php echo $this->translate("edit") ?>
                </a>
                <?php endif; ?>
                <?php if ( $item->level_id != 1 ): ?>
             
                  <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->user_id));?>'>
                    <?php echo $this->translate("delete") ?>
                  </a>
               
                  <a href='<?php echo $this->url(array('action' => 'login', 'id' => $item->user_id));?>' onclick="loginAsUser(<?php echo $item->user_id ?>); return false;">
                    <?php echo $this->translate("login") ?>
                  </a>
                <?php endif; ?>
                <?php if ( $this->emailResend && $item->user_id != 1 && $item->verified == 0 ): ?>
                 
                  <a class='smoothbox' href='<?php echo $this->url(array('action' => 'resend-email', 'id' => $item->user_id));?>'>
                    <?php echo $this->translate("Resend Email") ?>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons'>
      <button type='submit' name="submit_button" value="approve"><?php echo $this->translate("Approve Selected") ?></button>
      <button type='submit' name="submit_button" value="delete" style="float: right;"><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
  </div>
<?php } else { ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no members.") ?>
    </span>
  </div>
<?php } ?>
