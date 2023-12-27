<?php

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/dropzone/css/dropzone.css')
                 ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/dropzone/css/style.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/dropzone/js/dropzone.js');

$this->headTranslate( array( 'Remove', 'Click to remove this entry.', 'Upload failed', 'Upload Progress ({size})', '{name} already added.', 'An error occurred.', 'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads', 'Minimum Files Size Deceeded - {filename} ( {filesize} )', 'Maximum Files Size Exceeded - {filename} ( {filesize} )', 'Invalid File Type - %s (%s)', ) );


// element name or id
$elementName = $this->element->getName();

$dropzoneVar = $this->data['dropzoneEleAccessVarableName'];
$url = $this->data['url'];
$deleteurl = $this->data['deleteUrl'];
$formId = $this->data['formId'];

$parallelUploads = ($this->data['parallelUploads']) ? $this->data['parallelUploads'] : 2;
$uploadMultiple = $this->data['uploadMultiple'];
$maxFilesize = $this->data['maxFilesize'];
$maxFiles = ($this->data['maxFiles']) ? $this->data['maxFiles'] : 10;
$acceptedFiles = $this->data['acceptedFiles'];
$autoProcessQueue = $this->data['autoProcessQueue'];
$external_button_to_upload_file = $this->data['external_button_to_upload_file']; // show button from this file for for upload photo
$add_photo_button_link_id = $this->data['add_photo_button_link_id'];
$submit_button = $this->data['submit_button'];

?>

<?php if(!empty($this->data['linkDescription'])): ?>
    <div class="description"><?php echo $this->translate($this->data['linkDescription']); ?></div>
<?php endif;?>

<?php if ( $external_button_to_upload_file &&  empty($add_photo_button_link_id) ): $add_photo_button_link_id = $dropzoneVar . "SelectLink"; ?>
  <div class="uploader-links">
      <a id="<?php echo $add_photo_button_link_id ?>" class="upload-link <?php echo $this->data['linkClass'] ?>" onClick="" href="javascript:void(0);">
          <?php echo $this->translate($this->data['linkTitle']); ?>
      </a>
  </div>
<?php endif ?>
<div>
    <div id="<?php echo $dropzoneVar . "Element" ?>" class="dropzone">
          <div class="dz-default dz-message"><?php echo $this->data['linkTitle']; ?></div>
    </div>
    <div class="progress">
        <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress>
            <span class="progress-text"></span>
        </div>
    </div>
    <div class="error-message"></div>
    <div class="success-message"></div>
    <input type="hidden" name="<?php echo $elementName; ?>" class="file-ids" id="file" value="" />
</div>
<div>
    <button id="<?php echo $dropzoneVar . "UploadBtn" ?>" class="start-upload-link buttonlink" style='display:none;'>
        <?php echo $this->translate('Save Photos');?>
    </button>
</div>

<script type="text/javascript">

    <?php if ( $dropzoneVar ): ?>
        var <?php echo $dropzoneVar ?>;
    <?php endif ?>    

    Dropzone.autoDiscover = false; <?php // use this line before runonce so that dropzone would not configure automatically any element ?>

    en4.core.runonce.add(function (){

        var isImageUploaded = false;
        var myDropzone = new Dropzone("#<?php echo $dropzoneVar . "Element" ?>", {
            url: "<?php echo $url ?>",
            dataType:'json',
            parallelUploads : <?php echo $parallelUploads ?>,
            uploadMultiple : <?php echo json_encode($uploadMultiple) ?>,
            maxFilesize : <?php echo $maxFilesize ?>,
            maxFiles : <?php echo $maxFiles ?>,
            acceptedFiles : '<?php echo $acceptedFiles ?>',
            autoProcessQueue : <?php echo json_encode($autoProcessQueue) ?>,
            addRemoveLinks: true,
            success: function(file,response ) {
                <?php if ( $formId ) : ?>
                    if ( jQuery('#form-upload').length > 0 && jQuery('#form-upload').find('#file').length > 0 ) {
                        scriptJquery("#file").val( scriptJquery("#file").val() + response.photo_id + ' ' );
                        isImageUploaded = true;
                        //scriptJquery("#form-upload").submit();
                    }
                <?php endif; ?>
            },
            uploadprogress: function( file, progress, bytesSent ) {
                if (file.previewElement) {
                    var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
                    progressElement.style.width = progress + "%";
                    if ( progressElement.querySelector(".progress-text") ) {
                        progressElement.querySelector(".progress-text").textContent = progress + "%";
                    }
                }
            }

        });

        <?php if ( $dropzoneVar ): // assigning dropzone to a variable so that it can be access outside this file. ?>    
            <?php echo $dropzoneVar ?> = myDropzone;
        <?php endif ?>

        myDropzone.on("addedfiles", function(files) {

            var isInValid = false;
            var imageFileElement = '<?php echo $dropzoneVar . "Element" ?>';
            var temp = jQuery('#' + imageFileElement )[0].nextElementSibling;
            temp.innerHTML = "";
            var buttonId = '<?php echo $dropzoneVar . "UploadBtn" ?>';
            var objFiles = this.files; // get the files of this objects
            for( i=0; i < objFiles.length; i++ ) {
                if ( this.options.maxFilesize < objFiles[i].size/ ( 1024 * 1024 ) ) {
                    temp.innerHTML = '<?php echo $this->translate("Max File Size which is allow is %s", $maxFilesize ) ?>';
                    isInValid = true;
                }
                if( objFiles[i].type.indexOf( 'image/' ) == -1 ) {
                    temp.innerHTML = '<?php echo $this->translate("Only Image files are allowed" ) ?>';
                    isInValid = true;
                }

                if ( this.options.maxFiles < objFiles.length ) {
                    temp.innerHTML = '<?php echo $this->translate("Only %s files are allowed", $maxFiles ) ?>';
                    isInValid = true;
                }
            }

            if ( isInValid || objFiles.length == 0 || objFiles.length > this.options.maxFiles ) {
                jQuery('#' + buttonId )[0].style.display = 'none';
                jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = true;
            } else {
                // only one file upload is allowed
                jQuery('#' + buttonId )[0].style.display = 'block';
                jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = false;
            }

        });

        myDropzone.on("removedfile", function(file) {

            var isInValid = false;
            var imageFileElement = '<?php echo $dropzoneVar . "Element" ?>';
            var temp = jQuery('#' + imageFileElement )[0].nextElementSibling;
            temp.innerHTML = "";
            var buttonId = '<?php echo $dropzoneVar . "UploadBtn" ?>';

            var objFiles = this.files; // get the files of this objects
            for( i=0; i < objFiles.length; i++ ) {
                if ( this.options.maxFilesize < objFiles[i].size/ ( 1024 * 1024 ) ) {
                    temp.innerHTML = '<?php echo $this->translate("Max File Size which is allow is %s", $maxFilesize ) ?>';
                    isInValid = true;
                }
                if( objFiles[i].type.indexOf( 'image/' ) == -1 ) {
                    temp.innerHTML = '<?php echo $this->translate("Only Image files are allowed" ) ?>';
                    isInValid = true;
                }
                if ( this.options.maxFiles < objFiles.length ) {
                    temp.innerHTML = '<?php echo $this->translate("Only %s files are allowed", $maxFiles ) ?>';
                    isInValid = true;
                }
            }
            if ( isInValid || objFiles.length == 0 || objFiles.length > this.options.maxFiles ) {
                jQuery('#' + buttonId )[0].style.display = 'none';
                jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = true;
            } else {
                // only one file upload is allowed
                jQuery('#' + buttonId )[0].style.display = 'block';
                jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = false;
            }

        });
        jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>').click(function(){
            myDropzone.processQueue();
            jQuery("#form-upload").submit(function(e){
                return isImageUploaded;
            });
        });
        jQuery('#<?php echo $add_photo_button_link_id ?>').click(function(){
            myDropzone.hiddenFileInput.click();
        });
    });

</script>