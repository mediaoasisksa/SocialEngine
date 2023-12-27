
<?php echo $this->form->render($this); ?>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected service provider entries?');?>");
}

function selectAll()
{
  var i;
  var multidelete_form = scriptJquery('#multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Service Providers'); ?></h3>
<p><?php echo $this->translate('This page lists all the service providers your users have posted. You can use this page to monitor these service providers and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific service provider entries. Leaving the filter fields blank will show all the service provider entries on your social network. Here, you can also make service providers featured / un-featured, sponsored / un-sponsored, new / remove from new, and approve / dis-approve them.'); ?></p><br>
<div class="admin_search ">
  <div class="search">
    <form method="get" class="global_form_box" action="" width="100%">
      <div>
          <label>
              <?php echo $this->translate("Title") ?>
          </label>
          <?php if (empty($this->title)): ?>
              <input type="text" name="title" /> 
          <?php else: ?>
              <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
          <?php endif; ?>
      </div>
      <div>
          <label>
              <?php echo $this->translate("Owner") ?>
          </label>  
          <?php if (empty($this->owner)): ?>
              <input type="text" name="owner" /> 
          <?php else: ?> 
              <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
          <?php endif; ?>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Featured") ?>  
        </label>
        <select id="" name="featured">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->featured == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if ($this->featured == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Sponsored") ?> 
        </label>
        <select id="sponsored" name="sponsored">
          <option value="0"  ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->sponsored == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1"  <?php if ($this->sponsored == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
        </select>
      </div>    

      <div>
        <label>
          <?php echo $this->translate("New") ?> 
        </label>
        <select id="newlabel" name="newlabel">
          <option value="0"  ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->newlabel == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1"  <?php if ($this->newlabel == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
        </select>
      </div>    

      <div>
        <label>
          <?php echo $this->translate("Approved") ?>  
        </label>
        <select id="sponsored" name="approved">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->approved == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if ($this->approved == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Verified") ?>  
        </label>
        <select id="" name="verified">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->verified == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if ($this->verified == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>

     <div>
        <label>
          <?php echo $this->translate("Published") ?>  
        </label>
        <select id="" name="status">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if ($this->status == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if ($this->status == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Browse By") ?> 
        </label>
        <select id="" name="providerbrowse">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if ($this->providerbrowse == 1) echo "selected"; ?> ><?php echo $this->translate("Most Viewed") ?></option>
          <option value="2" <?php if ($this->providerbrowse == 2) echo "selected"; ?> ><?php echo $this->translate("Most Recent") ?></option>
        </select>
      </div>
      <div>
        <button type="submit" name="search" value="search"><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>

<div class='admin_members_results'>
  <?php $counter = $this->paginator->getTotalItemCount(); ?>
  <?php if (!empty($counter)): ?>
    <div class="">
      <?php echo $this->translate(array('%s service provider found.', '%s service providers found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php elseif($this->search == 1): ?>
    <div class="tip"><span>
      <?php echo $this->translate("No results were found.") ?></span>
    </div>
  <?php return; endif; ?>    
</div>








<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Title") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Rating Count") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Owner") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Verified") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Approved") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Published") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Services") ?></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Sponserd'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponserde', 'ASC');"><?php echo $this->translate('S'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('New'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('new', 'ASC');"><?php echo $this->translate('N'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Trending'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('Trending', 'ASC');"><?php echo $this->translate('T'); ?></a></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Date") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Timezone") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td class="admin_table_centered"><?php echo $item->getIdentity() ?></td>
        <td class="admin_table_centered"><?php echo $item->getTitle() ?></td>
        <td align="center" class="admin_table_centered"><?php echo $item->rating_count; ?></td>
        <td><?php echo $item->getOwner()->getTitle() ?></td>

        <?php if ($item->verified == 1): ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'verified', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Unverified')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'verified', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Verified')))) ?></td>
        <?php endif; ?>

        <?php if ($item->approved == 1): ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'approved', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Dis-Approved')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'approved', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Approved')))) ?></td>
        <?php endif; ?> 
        <td align="center" class="admin_table_centered"><?php if($item->status == "1")echo "Yes";else echo "No"; ?></td>
        <td align="center" class="admin_table_centered"><?php echo $item->no_of_services ?></td>

        <?php if ($item->featured == 1): ?> 
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'featured', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.gif', '', array('title' => $this->translate('Make Un-featured')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'featured', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title' => $this->translate('Make Featured')))) ?></td>
        <?php endif; ?>

        <?php if ($item->sponsored == 1): ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'sponsored', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))); ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'sponsored', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))); ?>
        <?php endif; ?>   

        <?php if ($item->newlabel == 1): ?> 
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'newlabel', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/icon/new.png', '', array('title' => $this->translate('Remove New Label')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'newlabel', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/icon/new-disable.png', '', array('title' => $this->translate('Set New Label')))) ?></td>
        <?php endif; ?>
        <?php if ($item->hot == 1): ?> 
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'hot', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/admin/trending.png', '', array('title' => $this->translate('Remove Trending')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-manage', 'action' => 'hot', 'pro_id' => $item->pro_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/admin/nontrending.png', '', array('title' => $this->translate('Set Trending')))) ?></td>
        <?php endif; ?>
        <td><?php echo $item->creation_date ?></td>
        <td class="admin_table_centered"><?php echo $item->timezone ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'edit', 'pro_id' => $item->pro_id),
                $this->translate("edit")); ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'admin-provider-manage', 'action' => 'delete', 'id' => $item->pro_id,'format' => 'smoothbox'),
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
  <?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  'params' => $this->formValues,
)); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no service provider entries by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>