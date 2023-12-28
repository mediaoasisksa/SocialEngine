<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<script type="text/javascript">
  
  var modulename = 'music';
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
<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
  var playlist_id = <?php echo $this->playlist_id ?>;
  function updateTextFields() {
    if (scriptJquery('#playlist_id').selectedIndex > 0) {
      scriptJquery('#title-wrapper').hide();
      scriptJquery('#description-wrapper').hide();
      scriptJquery('#search-wrapper').hide();
    } else {
      scriptJquery('#title-wrapper').show();
      scriptJquery('#description-wrapper').show();
      scriptJquery('#search-wrapper').show();
    }
  }

  en4.core.runonce.add(function () {
    new Uploader('#upload_file', {
      uploadLinkClass : 'buttonlink icon_music_new',
      uploadLinkTitle : '<?php echo $this->translate("Add Music");?>',
      uploadLinkDesc : '<?php echo $this->translate("_MUSIC_UPLOAD_DESCRIPTION");?>'
    });
  });
  // populate field if playlist_id is specified
  if (playlist_id > 0) {
    scriptJquery('#playlist_id option').each(function(el, index) {
      if (scriptJquery(this).value == playlist_id)
        scriptJquery('#playlist_id').selectedIndex = index;
    });
    updateTextFields();
  }
</script>


<script type="text/javascript">
  scriptJquery('.core_main_music').parent().addClass('active');
</script>
