<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<script type="text/javascript">
  var modulename = 'poll';
  en4.core.runonce.add(function() {
    <?php if(isset($this->category_id) && $this->category_id != 0) { ?>
      showSubCategory('<?php echo $this->category_id; ?>','<?php echo $this->subcat_id; ?>');
    <?php } else { ?>
      if(document.getElementById('subcat_id-wrapper'))
        document.getElementById('subcat_id-wrapper').style.display = "none";
    <?php } ?>

    <?php if(isset($this->subsubcat_id)) { ?>
      <?php if(isset($this->subcat_id) && $this->subcat_id != 0) { ?>
        showSubSubCategory('<?php echo $this->subcat_id; ?>' ,'<?php echo $this->subsubcat_id; ?>');
      <?php } else { ?>
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      <?php } ?>
    <?php } else { ?>
      if(document.getElementById('subsubcat_id-wrapper'))
        document.getElementById('subsubcat_id-wrapper').style.display = "none";
    <?php } ?>
  });
</script>
<div class="layout_middle">
  <div class="generic_layout_container">
    <div class="headline">
      <h2>
        <?php echo $this->translate('Polls');?>
      </h2>
      <div class="tabs">
        <?php
          // Render the menu
          echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
      </div>
    </div>
  </div>
</div>
<div class="layout_middle">
  <div class="generic_layout_container">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
