<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Sitevideo
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
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
        <input type="text" class="remote-url" placeholder="<?php echo $this->translate('Enter Video URL') ?>">
        <button type="button" class="remote-fetch">Upload</button>
        <span class="success-message"></span>
        <span class="error-message"></span>
        <span class="loading" style="display: none;"><?php echo $this->translate('Loading ...'); ?></span>
    </div>
<?php endif; ?>
<?php if ($this->data['dropEnable']): ?>
    <div class="drop-area"><?php echo $this->translate($this->data['dropAreaText']); ?></div>
<?php endif; ?>
<ul class="uploaded-files-list <?php echo $viewClass ?>"></ul>
<div class="progress-bar"></div>
<?php if (empty($this->data['autostart'])): ?>
    <div>
        <button class="start-upload-link buttonlink" style='display:none;'><?php echo $this->translate('Save Video');?></button>
    </div>
<?php endif; ?>
<input type="hidden" name="<?php echo $elementName; ?>" class="file-ids" id="uploader-<?php echo $elementName; ?>-ids" value="" />
<script type="text/javascript">
    en4.core.runonce.add(function() {
    // CUSTOM CODE FOR SITEVIDEO
    callbacks = {
        onItemComplete: function(el, file, response) {
            el.removeClass('file-uploading').addClass('file-success');
            el.getElement('.file-progress-list').tween('width', this.getItemWidth());
            el.getElement('.file-progress').setStyle('opacity', 1);
            el.set('data-file_id', response.video_id);
            value = $('id').get('value') + response.video_id + ' ';
            $('id').set('value', value);
            $('code').set('value', response.code);
            if (this.options.autosubmit && this._uploadedMaxFiles() && this.form) {
              if(this.lastInput) {
                this.lastInput.destroy();
              }
                typeof this.form.submit == 'function' ? this.form.submit() : this.form.submit.click();
            }
        },
    };
    en4.seaocore.initSeaoFancyUploader(<?php echo Zend_Json::encode($this->data) ?>, callbacks);
});
</script>