<?php

?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Remove Mapping ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to remove this service profile - category mapping? (Note that after removing this mapping, the earlier services associated with this mapping will loose their custom profile data.)'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->category_id ?>"/>

      <?php if ($this->countChilds && ($this->category->cat_dependency == 0 && $this->category->subcat_dependency == 0)): ?>
        <input type="checkbox" name="import_profile" /><?php echo $this->translate("Map sub-categories of this category with the same service profile type so that services associated with them does not loose their custom profile data."); ?><br/><br/>
      <?php elseif ($this->countChilds && ($this->category->cat_dependency != 0 && $this->category->subcat_dependency == 0)): ?>  
        <input type="checkbox" name="import_profile" /><?php echo $this->translate("Map 3rd level categories of this sub-category with the same service profile type so that services associated with them does not loose their custom profile data."); ?><br/><br/>
      <?php endif; ?>

      <button type='submit'><?php echo $this->translate('Save Changes'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
          TB_close();
  </script>
<?php endif; ?>
