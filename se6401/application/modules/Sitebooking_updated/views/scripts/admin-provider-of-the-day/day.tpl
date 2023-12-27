<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected Provider ?")) ?>');
  }

  function selectAll()
  {
    var i;
    var multidelete_form = scriptJquery('#multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      scriptJquery('#order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      scriptJquery('#order').value = order;
      scriptJquery('#order_direction').value = default_direction;
    }
    scriptJquery('#filter_form').submit();
  }
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div> 

<div class='clear'>
  <div class='settings'>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'provider-of-the-day', 'action' => 'multi-delete'), 'admin_default'); ?>" onSubmit="return multiDelete()" class="global_form">
      <div>
        <h3><?php echo $this->translate("Provider of the Day widget") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Add and manage service providers on your site to be shown in the Provider of the Day widget. You can also mark a service providers for future date such that the marked service provider automatically shows up as Provider of the Day on the desired date. Note that for this provider of the day to be shown, you must first place the Provider of the Day widget at the desired location.") ?>
        </p>
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-of-the-day', 'action' => 'add-item'), $this->translate('Add a Provider of the Day'), array(
            'class' => 'smoothbox buttonlink',
            'style' => 'background-image: url('.$this->layout()->staticBaseUrl.'application/modules/Core/externals/images/admin/new_category.png);'))
        ?>	<br/>	<br/>
        <?php if ($this->paginator->getTotalItemCount() > 0): ?>
          <table class='admin_table'width="100%">
            <thead>
              <tr>
								<?php $class = ( $this->order == 'engine4_sitebooking_pros.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitebooking_pros.title', 'DESC');"><?php echo $this->translate("Provider Title") ?></a></th>

                <?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
                <?php //Start End date work  ?>
                <?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
                <?php //End End date work  ?>
                <th align="left"><?php echo $this->translate("Option") ?></th>
              </tr>
            </thead>
            <tbody>
							<?php foreach ($this->paginator as $item): ?>
                <tr>
                  <td><input name='delete_<?php echo $item->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->itemoftheday_id ?>"/></td>
                  <td class='admin_table_bold admin-txt-normal' title="<?php echo $this->translate($item->getTitle()) ?>">
                
                  <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->pro_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->slug), $item->title); ?>

                  </td>
                  <td align="left"><?php echo $item->start_date ?></td>
                  <?php //Start End date work ?>
                  <td align="left"><?php echo $item->end_date ?></td>
                  <?php //End End date work  ?>
                  <td align="left">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'provider-of-the-day', 'action' => 'delete-item', 'id' => $item->itemoftheday_id), $this->translate('delete'), array('class' => 'smoothbox',)) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table><br />
          <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
          </div>
        <?php else: ?>
          <div class="tip"><span><?php echo $this->translate("No providers have been marked as Provider of the Day."); ?></span> </div><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php echo $this->paginationControl($this->paginator); ?>
