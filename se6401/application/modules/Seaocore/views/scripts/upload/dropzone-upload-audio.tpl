<?php
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/dropzone/css/dropzone.css');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/dropzone/js/dropzone.js');

    $this->headTranslate( array(
        'Remove', 'Click to remove this entry.',
        'Upload failed', 'Upload Progress ({size})',
        '{name} already added.', 'An error occurred.',
        'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads',
        'Minimum Files Size Deceeded - {filename} ( {filesize} )',
        'Maximum Files Size Exceeded - {filename} ( {filesize} )',
        'Invalid File Type - %s (%s)', 'Invalid URL',
        )
    );
    // element name or id
    $elementName = $this->element->getName();
    $viewClass = 'uploader-' . $this->data['view'] . '-view';
    $extraVars = is_array($this->data['vars']) ? $this->data['vars'] : array();


    $dropzoneVar = $this->data['dropzoneEleAccessVarableName'];

    $url = $this->data['url'];
    $formId = $this->data['formId'];

    $parallelUploads = $this->data['parallelUploads'];
    $uploadMultiple = $this->data['uploadMultiple'];
    $maxFilesize = $this->data['maxFilesize'];
    $maxFiles = $this->data['maxFiles'];
    $acceptedFiles = $this->data['acceptedFiles'];
    $autoProcessQueue = $this->data['autoProcessQueue'];
    $upload_max_filesize = (int)ini_get('upload_max_filesize');

?>
<div class="description"><?php echo $this->translate($this->data['linkDescription']); ?></div>
<div class="uploader-links">
    <a id="<?php echo $dropzoneVar . "SelectLink" ?>" class="upload-link <?php echo $this->data['linkClass'] ?>" onClick="" href="javascript:void(0);">
        <?php echo $this->translate($this->data['linkTitle']); ?>
    </a>
</div>
<div class="uploader-links-block">
    <div id="<?php echo $dropzoneVar . "Element" ?>" class="dropzone">
          <div class="dz-default dz-message"><?php echo $this->translate('Drop Files Here'); ?></div>
    </div>
    <div class="progress" style="display:none">
        <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress>
            <span class="progress-text"></span>
        </div>
    </div>
    <div class="error-message"></div>
    <div class="success-message"></div>
    <input type="hidden" name="<?php echo $elementName; ?>" class="file-ids" id="file" value="" />
</div>
<div class="uploader-links-btn">
    <button id="<?php echo $dropzoneVar . "UploadBtn" ?>" class="start-upload-link buttonlink" style='display:none;'>
        <?php echo $this->translate('Save Audio');?>
    </button>
</div>

<script type="text/javascript">

    var temp = '<?php echo $this->data['uploadMultiple']; ?>';
    <?php if ( $dropzoneVar ): ?>
        var <?php echo $dropzoneVar ?>;
    <?php endif ?>    

    Dropzone.autoDiscover = false; <?php // use this line before runonce so that dropzone would not configure automatically any element ?>

    en4.core.runonce.add(function (){

        var isAudioUploaded = false;
        const upload_max_filesize = <?php echo $upload_max_filesize ?>;
        var myDropzone = new Dropzone("#<?php echo $dropzoneVar . "Element" ?>", {
            url: "<?php echo $url ?>",
            parallelUploads : <?php echo $parallelUploads ?>,
            uploadMultiple : <?php echo json_encode($uploadMultiple) ?>,
            maxFilesize : <?php echo $maxFilesize ?>,
            maxFiles : <?php echo $maxFiles ?>,
            acceptedFiles : '<?php echo $acceptedFiles ?>',
            autoProcessQueue : <?php echo json_encode($autoProcessQueue) ?>,
            addRemoveLinks: true,
            success: function( file,response ) {
                <?php if ( $formId ) : ?>
                    if ( jQuery("#<?php echo $this->data['formId']; ?>").length > 0 && jQuery("#<?php echo $this->data['formId']; ?>").find('#file').length > 0 ) {
                        scriptJquery("#file").val( scriptJquery("#file").val() + response.song_id + ' ' );
                        scriptJquery("#fancyuploadfileids").val(scriptJquery("#file").val().toString());
                        
                        isAudioUploaded = true;
                        scriptJquery("#<?php echo $this->data['formId']; ?>").submit();
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
            var AudioFileElement = '<?php echo $dropzoneVar . "Element" ?>';
            var temp = jQuery('#' + AudioFileElement )[0].nextElementSibling;
            temp.innerHTML = "";
            var buttonId = '<?php echo $dropzoneVar . "UploadBtn" ?>';
            var objFiles = this.files; // get the files of this objects
            for( i=0; i < objFiles.length; i++ ) {
                if ( this.options.maxFilesize < objFiles[i].size/ ( 1024 * 1024 ) ) {
                    temp.html("<?php echo $this->translate("Max File Size which is allow is %s", $maxFilesize ) ?>");
                    isInValid = true;
                }
                if ( ( upload_max_filesize < objFiles[i].size/ ( 1024 * 1024 ) ) ) {
                    temp.html("<?php echo $this->translate("Max File Size which is allow is %s", $upload_max_filesize ) ?>");
                    isInValid = true;
                }
                // if( objFiles[i].type.indexOf( 'video/' ) == -1 ) {
                //     temp.html("<?php echo $this->translate("Only Audio files are allowed" ) ?>");
                //     isInValid = true;
                // }

                if ( temp < objFiles.length ) {
                    temp.html("<?php echo $this->translate("Only %s files are allowed", $maxFiles ) ?>");
                    isInValid = true;
                }
            }

            if ( isInValid || objFiles.length == 0 || objFiles.length > temp ) {
                jQuery('#' + buttonId )[0].style.display = 'none';
                jQuery('#' + '<?php echo $dropzoneVar . "UploadBtn" ?>').attr('disabled',true ) ;
            } else {
                // only one file upload is allowed
                jQuery('#' + buttonId )[0].style.display = 'block';
                jQuery('#' + '<?php echo $dropzoneVar . "UploadBtn" ?>').attr('disabled',false ) ;
            }

        });

        myDropzone.on("removedfile", function(file) {

            var isInValid = false;
            var AudioFileElement = '<?php echo $dropzoneVar . "Element" ?>';
            var temp = jQuery('#' + AudioFileElement ).next().next();
            temp.html("");
            var buttonId = '<?php echo $dropzoneVar . "UploadBtn" ?>';

            var objFiles = this.files; // get the files of this objects
            for( i=0; i < objFiles.length; i++ ) {
                if ( this.options.maxFilesize < objFiles[i].size/ ( 1024 * 1024 ) ) {
                    temp.html("<?php echo $this->translate("Max File Size which is allow is %s", $maxFilesize ) ?>");
                    isInValid = true;
                }
                if ( ( upload_max_filesize < objFiles[i].size/ ( 1024 * 1024 ) ) ) {
                    temp.html("<?php echo $this->translate("Max File Size which is allow is %s", $upload_max_filesize ) ?>");
                    isInValid = true;
                }
                // if( objFiles[i].type.indexOf( 'video/' ) == -1 ) {
                //     temp.html("<?php echo $this->translate("Only Audio files are allowed" ) ?>");
                //     isInValid = true;
                // }
                if ( temp < objFiles.length ) {
                    temp.html("<?php echo $this->translate("Only %s files are allowed", $maxFiles ) ?>");
                    isInValid = true;
                }
            }

            if ( isInValid || objFiles.length == 0 || objFiles.length > temp ) {
                jQuery('#' + buttonId )[0].style.display = 'none';
                jQuery('#' + '<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = true;
            } else {
                // only one file upload is allowed
                jQuery('#' + buttonId )[0].style.display = 'block';
                jQuery('#' + '<?php echo $dropzoneVar . "UploadBtn" ?>')[0].disabled = false;
            }

        });

        jQuery('#<?php echo $dropzoneVar . "UploadBtn" ?>').click(function(){
            myDropzone.processQueue();
            jQuery("#<?php echo $this->data['formId']; ?>").submit(function(e){
                return isAudioUploaded;
            });
        });
        jQuery('#<?php echo $dropzoneVar . "SelectLink" ?>').click(function(){
            myDropzone.hiddenFileInput.click();
        });

    });

</script>
