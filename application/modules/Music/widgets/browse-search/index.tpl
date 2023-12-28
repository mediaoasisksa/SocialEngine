<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <john@socialengine.com>
 */
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<script type="text/javascript">
  //<![CDATA[
    en4.core.runonce.add(function() {
      scriptJquery('#sort, #show').on('change', function(){
        scriptJquery(this).parent('form').trigger("submit");
      });
    });
  //]]>

  var modulename = 'music';
  en4.core.runonce.add(function() {
    if(document.getElementById('category_id')) {
      <?php if(isset($_GET['category_id']) && $_GET['category_id'] != 0) { ?>
          showSubCategory('<?php echo $_GET['category_id']; ?>','<?php echo $_GET['subcat_id']; ?>');
        <?php if(isset($_GET['subsubcat_id'])){ ?>
          showSubSubCategory("<?php echo $_GET['subcat_id']; ?>","<?php echo $_GET['subsubcat_id']; ?>");
        <?php } else {?>
          if(document.getElementById('subsubcat_id-wrapper'))
            document.getElementById('subsubcat_id-wrapper').style.display = "none";
        <?php } ?>
      <?php } else { ?>
        if(document.getElementById('subcat_id-wrapper'))
          document.getElementById('subcat_id-wrapper').style.display = "none";
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      <?php } ?>
    }
  });
</script>
<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>
