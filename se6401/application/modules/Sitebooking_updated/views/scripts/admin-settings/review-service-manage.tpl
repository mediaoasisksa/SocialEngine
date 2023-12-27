
<script type="text/javascript">

function multiDeleteService()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected Service Ratings&Reviews entries?');?>");
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

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->childNavigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->childNavigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Reviews'); ?></h3>
<p><?php echo $this->translate('This page lists all the reviews posted by members of your site for existing services. Here, you can monitor reviews, delete them. Entering criteria into the filter fields will help you find specific review entries. Leaving the filter fields blank will show all the review entries on your social network.
'); ?></p><br />

<div class="admin_search">
  <div class="search">
  <?php echo $this->form->render($this); ?>
  </div>
</div>     

<div class="admin_members_results">  
  <?php if($this->paginator->getTotalItemCount() > 0): ?>
  <div>
    <?php echo $this->translate(array('%s Review', '%s Reviews', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <?php else: ?>
  <div>
    <?php echo $this->translate('0 Review.') ?>
  </div>
  <?php return;endif;?>
</div>

<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-service-review-delete')); ?>" onSubmit="return multiDeleteService()">
<table class='admin_table'>
  <thead>
  <tr>
    <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
    <th align="center" class="admin_table_centered"><?php echo $this->translate('Sevice Title') ?></th>
    <th align="center" class="admin_table_centered"><?php echo $this->translate('Reviewed By and Date') ?></th>
    <th align="center" class="admin_table_centered"><?php echo $this->translate('Review Title') ?></th>
    <th align="center" class="admin_table_centered"><?php echo $this->translate('Rating') ?></th>
    <th align="center" class="admin_table_centered"><?php echo $this->translate('Option') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($this->paginator as $item): ?>
    <tr>
    <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
    <td>              
      <span><?php echo $this->htmlLink(array('action' => 'view','pro_id' => $item->parent_id,'ser_id' => $item->ser_id,'route' => 'sitebooking_service_entry_view','reset' => true,'slug' => $item->slug), $item->getTitle()) ?></span>
    </td>

    <td>
      <span><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
      <span>
        <?php if($item->review_creation_date): ?>
           <?php echo ", ".$item->review_creation_date ?>
        <?php endif; ?>  
      </span>
    </td>
    
    <td><?php echo $item->review ?></td>

    <td>
      <!-- USER REVIEW RATING -->
      <span id="admin_service_review_manage_<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
      <span id="admin_service_review_manage_<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
      <span id="admin_service_review_manage_<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
      <span id="admin_service_review_manage_<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
      <span id="admin_service_review_manage_<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>

      <script type="text/javascript">
        en4.core.runonce.add( function() {
        
        var rating = 0;
          
        rating = "<?php echo $item->rating;?>";

        for(var x=1; x<=parseInt(rating); x++) {
          
          var id = <?php echo $item->getIdentity() ?>;
          // console.log(id)
          id = "admin_service_review_manage_"+id+"_"+x;
          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
        }

        var remainder = Math.round(rating)-rating;

        for(var x=parseInt(rating)+1; x<=5; x++) {
          
          var id = <?php echo $item->getIdentity() ?>;
          id = "admin_service_review_manage_"+id+"_"+x;
          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }

        if (remainder <= 0.5 && remainder !=0){

          var id = <?php echo $item->getIdentity() ?>;
          var last = parseInt(rating)+1;
          id = "admin_service_review_manage_"+id+"_"+last;
          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
        }
        });
      </script>
    </td>

    <td>
      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'admin-settings', 'action' => 'review-service-delete', 'id' => $item->rating_id,'format' => 'smoothbox'),$this->translate("delete"),array('class' => 'smoothbox')) ?>
    </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<br>

<div class='buttons'>
  <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>

<br>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
)); ?>

