<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: upload.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    new Uploader('#upload_file', {
      uploadLinkClass : 'buttonlink icon_photos_new',
      uploadLinkTitle : '<?php echo $this->translate("Add Photos");?>',
      uploadLinkDesc : '<?php echo $this->translate("Click \"Add Photos\" to select one or more photos from your computer."
        . " After you have selected the photos, they will begin to upload right away. "
        . "When your upload is finished, click the button below your photo list to save them to your employment listing.");?>'
    });
  });
</script>
<div class="layout_middle">
  <div class="generic_layout_container">
    <h2><?php echo $this->translate('Employment Listing Photos');?></h2>

    <?php echo $this->form->render($this) ?>
  </div>
</div>
