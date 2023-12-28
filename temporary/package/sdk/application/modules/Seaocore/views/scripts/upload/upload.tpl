<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headTranslate(array(
  'Remove', 'Click to remove this entry.',
  'Upload failed', 'Upload Progress ({size})',
  '{name} already added.', 'An error occurred.',
  'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads',
  'Minimum Files Size Deceeded - {filename} ( {filesize} )',
  'Maximum Files Size Exceeded - {filename} ( {filesize} )',
  'Invalid File Type - %s (%s)', 'Invalid URL',
));
?>
<?php $viewClass = 'uploader-' . $this->data['view'] . '-view';?>
<?php $extraVars = is_array($this->data['vars']) ? $this->data['vars'] : array(); ?>
<?php $elementName = $this->element->getName(); ?>
<div class="description"><?php echo $this->translate($this->data['linkDescription']); ?></div>
<div class="uploader-links">
    <?php if ($this->data['dropEnable']): ?>
        <a class="drop-link" href="javascript:void(0);"><?php echo $this->translate('Drag & Drop'); ?> </a>|
    <?php endif; ?>
    <a class="upload-link <?php echo $this->data['linkClass'] ?>" href="javascript:void(0);">
        <?php echo $this->translate($this->data['linkTitle']); ?>
    </a>
    <?php if ($this->data['remoteFile']): ?>|
        <a class="remote-link" href="javascript:void(0);">
            <?php echo $this->translate('From URL'); ?>
        </a>
    <?php endif; ?>
</div>
<?php if ($this->data['remoteFile']): ?>
    <div class="remote-wrapper dnone">
        <input type="text" class="remote-url" placeholder="<?php echo $this->translate('Enter File URL') ?>">
        <button type="button" class="remote-fetch">Upload</button>
        <span class="success-message"></span>
        <span class="error-message"></span>
        <span class="loading"><?php echo $this->translate('Loading ...'); ?></span>
    </div>
<?php endif; ?>
<?php if ($this->data['dropEnable']): ?>
    <div class="drop-area"><?php echo $this->translate($this->data['dropAreaText']); ?></div>
<?php endif; ?>
<div class="uploader-links">
    <?php if (empty($this->data['autostart'])): ?>
        <a class="start-upload-link dnone" href="javascript:void(0);">
            <?php echo $this->translate('Start Upload'); ?>
        </a>
    <?php endif; ?>
    <a class="clear-list" href="javascript:void(0);">
        <?php echo $this->translate('Clear List'); ?>
    </a>
</div>
<?php if ($this->data['limitFiles']): ?>
    <div class="tip">
        <span><?php echo $this->translate("You are allowed to upload maximum %s photos.", $this->data['limitFiles']); ?></span>
    </div>
<?php endif; ?>
<div class="scrollbars">
    <ul class="uploaded-files-list <?php echo $viewClass ?>"></ul>
</div>
<div class="progress-bar"></div>
<input type="hidden" name="<?php echo $elementName; ?>" class="file-ids" id="uploader-<?php echo $elementName; ?>-ids" value="" />
<script type="text/javascript">
    en4.core.runonce.add(function() {
        callbacks = {}; // for overwriting the default functions
        en4.seaocore.initSeaoFancyUploader(<?php echo $this->jsonInline($this->data) ?>, callbacks);
    });
</script>