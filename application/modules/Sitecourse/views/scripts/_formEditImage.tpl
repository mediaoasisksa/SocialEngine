<?php $course_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id', null);
$sitecourse = Engine_Api::_()->getItem('sitecourse_course', $course_id);
?>

<?php if ($sitecourse->photo_id !== null): ?>
  <div>
    <?php echo $this->itemPhoto($sitecourse, 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  </div>
  <br />
  
  <div id="thumbnail-controller" class="thumbnail-controller">
    <?php if ($sitecourse->getPhotoUrl())
    echo '<a href="javascript:void(0);" onclick="lassoStart();"></a>'; ?>
  </div>
<?php endif; ?>



<?php if ($sitecourse->signaturePhoto_id !== null): ?>
  <div class="dashboard_signature_image">
    <?php $storage_file = Engine_Api::_()->getItem('storage_file', $sitecourse->signaturePhoto_id);
    $src = 'application/modules/Sitecourse/externals/images/transparent.png';
    if(!empty($storage_file)){
      $storage_file->toArray();
      $src=$storage_file['storage_path']; 
    }

    echo "<img src='".$this->layout()->staticBaseUrl .$src. "'>";     
    ?>
  </div>
  <br />
<?php endif; ?>


<script type="text/javascript">
  var orginalThumbSrc;
  var originalSize;
  var loader = scriptJquery.crtEle('img',{ src:en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/core/loading.gif'});
  var lassoCrop;

  // var lassoSetCoords = function(coords)
  // {
  //   var delta = (coords.w - 48) / coords.w;

  //   scriptJquery('#coordinates').val(
  //   coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h);

  //   scriptJquery('previewimage').css({
  //     top : -( coords.y - (coords.y * delta) ),
  //     left : -( coords.x - (coords.x * delta) ),
  //     height : ( originalSize.y - (originalSize.y * delta) ),
  //     width : ( originalSize.x - (originalSize.x * delta) )
  //   });
  // }

  // var lassoStart = function()
  // {
  //   if( !orginalThumbSrc ) orginalThumbSrc = document.getElementById('previewimage').src;
  //   originalSize = $("lassoImg").getSize();

  //   lassoCrop = new Lasso.Crop('lassoImg', {
  //     ratio : [1, 1],
  //     preset : [10,10,58,58],
  //     min : [48,48],
  //     handleSize : 8,
  //     opacity : .6,
  //     color : '#7389AE',
  //     border : '<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
  //     onResize : lassoSetCoords,
  //     bgimage : ''
  //   });

  //   document.getElementById('previewimage').src = document.getElementById('lassoImg').src;
  //     //document.getElementById('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+sourceImg+'"/>';
  //     document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes'); ?></a> <?php echo $this->translate('or'); ?> <a href="javascript:void(0);" onclick="lassoCancel();"><?php echo $this->translate('cancel'); ?></a>';
  //     document.getElementById('coordinates').value = 10 + ':' + 10 + ':' + 58+ ':' + 58;
  //   }

    // var lassoEnd = function() {
    //   document.getElementById('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif'/><?php echo $this->string()->escapeJavascript($this->translate('Loading...')); ?></div>";
    //   lassoCrop.destroy();
    //   document.getElementById('EditPhoto').submit();
    // }

    // var lassoCancel = function() {
    //   document.getElementById('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+orginalThumbSrc+'"/>';
    //   document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail'); ?></a>';
    //   document.getElementById('coordinates').value = "";
    //   lassoCrop.destroy();
    // }

    var uploadPhoto = function() {
      document.getElementById('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif'/><?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?></div>";
      document.getElementById('EditPhoto').submit();
      document.getElementById('Filedata-wrapper').innerHTML = "";
    }
  </script>
