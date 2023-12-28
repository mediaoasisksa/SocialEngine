<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author	   John
 */
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already uploaded the maximum number of entries allowed.');?>
      <?php echo $this->translate('If you would like to upload a new entry, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'group_general'));?>
    </span>
  </div>
  <br/>
<?php else:?>
  <script type="text/javascript">
    var modulename = 'group';
    var category_id = '<?php echo $this->category_id; ?>';
    var subcat_id = '<?php echo $this->subcat_id; ?>';
    var subsubcat_id = '<?php echo $this->subsubcat_id; ?>';

    en4.core.runonce.add(function() {
      if(category_id && category_id != 0) {
        showSubCategory(category_id, subcat_id);
      } else {
        if(scriptJquery('#category_id').val()) {
          showSubCategory(scriptJquery('#category_id').val());
        } else {
          if(document.getElementById('subcat_id-wrapper'))
            document.getElementById('subcat_id-wrapper').style.display = "none";
        }
      }

      if(subsubcat_id) {
        if(subcat_id && subcat_id != 0) {
          showSubSubCategory(subcat_id, subsubcat_id);
        } else {
          if(document.getElementById('subsubcat_id-wrapper'))
            document.getElementById('subsubcat_id-wrapper').style.display = "none";
        }
      } else if(subcat_id) {
        showSubSubCategory(subcat_id);
      }
      else {
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      }
    });
  </script>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
<script type="text/javascript">
  scriptJquery('.core_main_group').parent().addClass('active');
</script>
